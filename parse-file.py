import sys
import os
import json
import markdown
import re

if len(sys.argv) < 2:
    exit(-1)

filename = sys.argv[1]
if not os.path.exists(filename):
    exit(-1)

if not os.path.isdir(filename):
    filebasename = os.path.basename(filename)
    filedirname = os.path.dirname(filename)
    parentdirname = os.path.dirname(filedirname)
else:
    filebasename = ''
    filedirname = filename
    parentdirname = os.path.dirname(filedirname)
    
def replace_filename_extension(filename, new_extension):
    if filename.rfind('.') >= 0:
        return filename[0:filename.rfind('.')] + '.' + new_extension
    else:
        return filename

def remove_extension(filename):
    return re.sub('\\.[^.]+$', '', filename)

def read_file_data(filename):
    """Open file, read it, and return metadata and markdown contents as a dict
    """
    if os.path.isdir(filename):
        #
        # If it is directory, read index file contained in it instead of it.
        #
        children = os.listdir(filename)

        for i in range(0, len(children)):
            if children[i].startswith("index."):
                filename = filename + '/' + children[i]

        if not os.path.basename(filename).startswith("index."):
            return {'title': os.path.basename(filename), 'name': os.path.basename(filename)}

    # The object that will be returned
    data = dict()
    data['name'] = replace_filename_extension(os.path.basename(filename), 'html')
    with open(filename, 'r') as file:
        line = file.readline()
        
        if line[0:3] == '---':
            #
            # This is metadata
            #
            while True:
                line = file.readline()
    
                #
                # The end of metadata part
                #
                if line[0:3] == '---':
                    line = file.readline()
                    break
    
                #
                # metadata is divided into two parts by a ':' character
                #
                try:
                    if line.index(':') > 0:
                        metadata = [item.strip() for item in line.split(':')]
                        data[metadata[0]] = metadata[1]
                except:
                    #
                    # if no colon character was found in the line, this is an error
                    #
                    break
                
        #
        # This is contents
        #
        langs = set()
        contents = [''.join(line.splitlines())]
        for line in file:
            line = ''.join(line.splitlines())

            matches = re.match('``` *(.*)', line)
            if matches != None and matches[1] != "":
                langs.add(matches[1])
            
            contents.append(line)

        if len(langs) > 0:
            data['languages'] = list(langs)
        
        #
        # Convert markdown text to HTML
        #
        data['contents'] = '\n'.join(contents).strip()

        data['number_of_lines'] = len(contents)
        
    if 'title' not in data:
        data['title'] = remove_extension(data['name'])
    #
    # file operation finished
    #
    return data

data = read_file_data(filename)

#
# Create breadcrumbs list
#
path_split = filename.split('/')
path_split.pop()
if filebasename.startswith("index."):
    path_split.pop()
    prefix = '../'
else:
    prefix = ''

breadcrumb = []
while len(path_split) > 0:
    path = '/'.join(path_split)
    i_data = read_file_data(path)
    if 'name' in i_data:
        breadcrumb.insert(0, {
            'name': prefix + i_data['name'],
            'title': i_data['title']
        })
    else:
        break
    path_split.pop()
    prefix += '../'

data['breadcrumb'] = breadcrumb

#
# if it is an index file then add file list to it.
#
filenames = os.listdir(filedirname)
index = ''
for i in range(0, len(filenames)):
    if filenames[i].startswith("index."):
        index = filenames.pop(i)
        index_data = read_file_data(filedirname + '/' + index)
        break

def check_file_extension(filename, extension_list):
    for extension in ['.gif', '.jpeg', '.jpg', '.JPG', '.mp4', '.pdf', '.png', '.webp', '.PNG']:
        if filename.endswith(extension):
            return False
    return True

filenames = list(filter(lambda f: check_file_extension(f), filenames))

try:
    if 'sort by' in index_data and index_data['sort by'] == 'desc':
        filenames.sort(reverse=True)
    else:
        filenames.sort()
except NameError:
    []
files = []

if index == filebasename:

    for i in range(0, len(filenames)):
        filedata = read_file_data(filedirname + '/' + filenames[i])
        files.append({
            'name': replace_filename_extension(filenames[i], 'html'),
            'title': filedata['title'] if 'title' in filedata else filenames[i]
        })

    data['files'] = files

    links = {}

    if parentdirname != "":
        upname = os.path.dirname(filedirname)
        upfiledata = read_file_data(upname)
        if len(upfiledata) > 0:
            links['up'] = {
                'name': '../index.html',
                'title': upfiledata['title'] if 'title' in upfiledata else None
            }

    data['links'] = links

else:

    filedata = {}
    for i in range(0, len(filenames)):
        if filenames[i] == filebasename:
        
            if i > 0:
                prev_file = read_file_data(filedirname + '/' + filenames[i - 1])
                filedata['prev'] = {
                    'name': prev_file['name'],
                    'title': prev_file['title']
                }
        
            if i < len(filenames) - 1:
                next_file = read_file_data(filedirname + '/' + filenames[i + 1])
                filedata['next'] = {
                    'name': next_file['name'],
                    'title': next_file['title']
                }

            upfiledata = read_file_data(filedirname + '/' + index)
            filedata['up'] = {
                'name': upfiledata['name'],
                'title': upfiledata['title']
            }
    
    data['links'] = filedata

#
# Dump to output
#
json.dump(data, sys.stdout, ensure_ascii=False)
