#    TsBackdoor - a poor static site generator for my own use.
#    Copyright (C) 2024  Tanaka Takayuki
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <https://www.gnu.org/licenses/>.

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

# 引数のファイルパスがディレクトリを指す場合
# indexファイルを探して、見つかったらそれを対象ファイルとする
is_index = False
if os.path.isdir(filename):
    children = os.listdir(filename)
    for i in range(0, len(children)):
        if children[i].startswith("index."):
            filename = filename + '/' + children[i]

print("Filename: " + filename, file=sys.stderr)

# ベース名は引数のベース名
# ディレクトリ名は引数のディレクトリ名
# 親ディレクトリ名は引数のディレクトリ名の一つ上のディレクトリ名
filebasename = os.path.basename(filename)
filedirname = os.path.dirname(filename)
parentdirname = os.path.dirname(filedirname)

if filebasename.startswith("index."):
    is_index = True
    print("is index = true", file=sys.stderr)

# 対象ファイルのメタデータとコンテンツを読み込む
data = read_file_data(filename)
if not bool(data):
    print("{}")
    exit(1)

# パンくずリストのためのリストを作る
path_split = filename.split('/')
path_split.pop()
if is_index:
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

def check_file_extension(filename):
    for extension in ['.gif', '.jpeg', '.jpg', '.JPG', '.mp4', '.pdf', '.png', '.webp', '.PNG', '.txt', '.org', '.git']:
        if filename.endswith(extension):
            return False
    return True

# ファイルのリストを作成する
filenames = os.listdir(filedirname)
index = ''
for i in range(0, len(filenames)):
    if filenames[i].startswith("index."):
        index = filenames.pop(i)
        index_data = read_file_data(filedirname + '/' + index, False)
        break

filenames = list(filter(lambda f: check_file_extension(f), filenames))

try:
    if 'sort by' in index_data and index_data['sort by'] == 'desc':
        filenames.sort(reverse=True)
    else:
        filenames.sort()
except NameError:
    []

# 親ディレクトリのファイルのリストも作成する
parent_filenames = None
if is_index and parentdirname != '':
    parent_filenames = os.listdir(parentdirname)

    parent_index = ''
    for i in range(0, len(parent_filenames)):
        if parent_filenames[i].startswith("index."):
            parent_index = parent_filenames.pop(i)
            parent_index_data = read_file_data(parentdirname + '/' + parent_index, False)
            break

    parent_filenames = list(filter(lambda f: check_file_extension(f), parent_filenames))

    try:
        if 'sort by' in parent_index_data and parent_index_data['sort by'] == 'desc':
            parent_filenames.sort(reverse=True)
        else:
            parent_filenames.sort()
    except NameError:
        []
    
def get_files_for_nav(dirname, param_filenames, is_parent = False):
    files = []
    for i in range(0, len(param_filenames)):
        path = dirname + '/' + param_filenames[i]
        filedata = read_file_data(path, False)
        filedata['name'] = ("../" if is_parent else "./") + os.path.basename(replace_filename_extension(param_filenames[i], 'html'))
        filedata['title'] = filedata['title'] if 'title' in filedata else filenames[i]
        if is_index:
            if param_filenames[i] == os.path.basename(filedirname):
                filedata['current'] = True
            else:
                filedata['current'] = False
        else:
            if param_filenames[i] == filebasename:
                filedata['current'] = True
            else:
                filedata['current'] = False
        files.append(filedata)
    return files

def get_file_for_nav(file_path, is_up_dir = False):
    nav_file = read_file_data(file_path, False)
    if is_up_dir:
        nav_file_path = '../' + replace_filename_extension(os.path.basename(file_path), 'html')
    else:
        nav_file_path = replace_filename_extension(os.path.basename(file_path), 'html')
        
    return {
        'name': nav_file_path,
        'title': nav_file['title']
    }

if is_index:

    data['files'] = get_files_for_nav(filedirname, filenames)
    if parent_filenames != None:
        data['side_files'] = get_files_for_nav(parentdirname, parent_filenames, True)

    links = {}

    if parentdirname != "":
        upfilenames = os.listdir(parentdirname)
        upfilenames = list(filter(lambda n: not n.startswith("index."), upfilenames))
        upfilenames = list(filter(lambda f: check_file_extension(f), upfilenames))
        upfilenames.sort()
        
        for i in range(0, len(upfilenames)):
            if upfilenames[i] == os.path.basename(filedirname):
            
                if i > 0:
                    # get a previous file
                    links['prev'] = get_file_for_nav(parentdirname + '/' + upfilenames[i - 1], True)
            
                if i < len(upfilenames) - 1:
                    # get a next file
                    links['next'] = get_file_for_nav(parentdirname + '/' + upfilenames[i + 1], True)
    
                upfiledata = read_file_data(parentdirname + '/' + index, False)
                links['up'] = {
                    'name': '../',
                    'title': upfiledata['title']
                }
        
    data['links'] = links

else:

    data['side_files'] = get_files_for_nav(filedirname, filenames)

    filedata = {}
    for i in range(0, len(filenames)):
        if filenames[i] == filebasename:
        
            if i > 0:
                # get a previous file
                filedata['prev'] = get_file_for_nav(filedirname + '/' + filenames[i - 1], False)
        
            if i < len(filenames) - 1:
                # get a next file
                filedata['next'] = get_file_for_nav(filedirname + '/' + filenames[i + 1], False)

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
