import sys
import os
import json
import markdown

if len(sys.argv) < 2:
    exit(-1)

filename = sys.argv[1]
filebasename = os.path.basename(filename)
filedirname = os.path.dirname(filename)
parentdirname = os.path.dirname(filedirname)

def replace_filename_extension(filename, new_extension):
    if filename.rfind('.') >= 0:
        return filename[0:filename.rfind('.')] + '.' + new_extension
    else:
        return filename

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

    # The object that will be returned
    data = dict()
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
        contents = [line]
        for line in file:
            contents.append(line.strip())
    
        #
        # Convert markdown text to HTML
        #
        data['contents'] = markdown.markdown('\n'.join(contents).strip())
    
    #
    # file operation finished
    #
    return data

data = read_file_data(filename)

#
# if it is an index file then add file list to it.
#
filenames = os.listdir(filedirname)
for i in range(0, len(filenames)):
    if filenames[i].startswith("index."):
        index = filenames.pop(i)
        index_data = read_file_data(filedirname + '/' + index)
        break

filenames = list(filter(lambda f: not f.endswith(".css") and not f.endswith(".png") and not f.endswith(".jpg"), filenames))

if 'sort by' in index_data and index_data['sort by'] == 'desc':
    filenames.sort(reverse=True)
else:
    filenames.sort()

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
                    'name': replace_filename_extension(filenames[i - 1], 'html'),
                    'title': prev_file['title'] if 'title' in prev_file else filenames[i - 1]
                }
        
            if i < len(filenames) - 1:
                next_file = read_file_data(filedirname + '/' + filenames[i + 1])
                filedata['next'] = {
                    'name': replace_filename_extension(filenames[i + 1], 'html'),
                    'title': next_file['title'] if 'title' in next_file else filenames[i + 1]
                }

            upfiledata = read_file_data(filedirname + '/' + index)
            filedata['up'] = {
                'name': replace_filename_extension(index, 'html'),
                'title': upfiledata['title'] if 'title' in upfiledata else None
            }
    
    data['links'] = filedata

#
# Dump to output
#
json.dump(data, sys.stdout, ensure_ascii=False)
