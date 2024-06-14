#!/usr/bin/python

import sys
import os
import json
import markdown
import re
import traceback
from common_functions import read_file_data, replace_filename_extension, remove_extension

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
    i_data = read_file_data(path, False)
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
        index_data = read_file_data(filedirname + '/' + index, False)
        break

def check_file_extension(filename):
    for extension in ['.gif', '.jpeg', '.jpg', '.JPG', '.mp4', '.pdf', '.png', '.webp', '.PNG', '.txt', '.org']:
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

if os.path.isdir(filename) or index == filebasename:

    for i in range(0, len(filenames)):
        filedata = read_file_data(filedirname + '/' + filenames[i], False)
        filedata['name'] = replace_filename_extension(filenames[i], 'html')
        filedata['title'] = filedata['title'] if 'title' in filedata else filenames[i]
        files.append(filedata)

    data['files'] = files

    links = {}

    if parentdirname != "":
        upfilenames = os.listdir(parentdirname)
        upfilenames = list(filter(lambda n: not n.startswith("index."), upfilenames))
        upfilenames = list(filter(lambda f: check_file_extension(f), upfilenames))
        upfilenames.sort()
        
        for i in range(0, len(upfilenames)):
            if upfilenames[i] == os.path.basename(filedirname):
            
                if i > 0:
                    prev_file = read_file_data(parentdirname + '/' + upfilenames[i - 1], False)
                    links['prev'] = {
                        'name': '../' + replace_filename_extension(upfilenames[i - 1], 'html'),
                        'title': prev_file['title']
                    }
            
                if i < len(upfilenames) - 1:
                    next_file = read_file_data(parentdirname + '/' + upfilenames[i + 1], False)
                    links['next'] = {
                        'name': '../' + replace_filename_extension(upfilenames[i + 1], 'html'),
                        'title': next_file['title']
                    }
    
                upfiledata = read_file_data(parentdirname + '/' + index, False)
                links['up'] = {
                    'name': '../',
                    'title': upfiledata['title']
                }
        
    data['links'] = links

else:

    filedata = {}
    for i in range(0, len(filenames)):
        if filenames[i] == filebasename:
        
            if i > 0:
                prev_file = read_file_data(filedirname + '/' + filenames[i - 1], False)
                filedata['prev'] = {
                    'name': replace_filename_extension(filenames[i - 1], 'html'),
                    'title': prev_file['title']
                }
        
            if i < len(filenames) - 1:
                next_file = read_file_data(filedirname + '/' + filenames[i + 1], False)
                filedata['next'] = {
                    'name': replace_filename_extension(filenames[i + 1], 'html'),
                    'title': next_file['title']
                }

            upfiledata = read_file_data(filedirname + '/' + index, False)
            filedata['up'] = {
                'name': replace_filename_extension(upfiledata['name'], 'html'),
                'title': upfiledata['title']
            }
    
    data['links'] = filedata

#
# Dump to output
#
json.dump(data, sys.stdout, ensure_ascii=False)
