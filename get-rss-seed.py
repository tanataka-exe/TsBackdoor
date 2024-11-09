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
import glob
import json
import datetime
from common_functions import read_file_data, replace_filename_extension

path = sys.argv[1]
filelist = []

def date_to_iso(date_str):
    date_parts = date_str.split('/')
    return str(datetime.date(int(date_parts[0]), int(date_parts[1]), int(date_parts[2])))

def get_subject(filepath):
    basepath = path
    result = []
    parts = filepath.replace(basepath, '').split('/')
    parts.pop(0)
    parts.pop()
    for i in parts:
        basepath = basepath + '/' + i
        info = read_file_data(basepath, False)
        result.append(info['title'])
    return ' / '.join(result)

for filepath in glob.iglob(path + "/**/*.md", recursive=True):
    try:
        filedata = read_file_data(filepath, False)
        filedata['path'] = replace_filename_extension(filepath.replace(path, ''), 'html')
        filedata['subject'] = get_subject(filepath)
        if 'date' in filedata:
            filedata['date_iso'] = date_to_iso(filedata['date'])
        filelist.append(filedata)
    except BaseException as e:
        print("error: " + str(e))

def date_to_number(date_str):
    return [('%04d' % int(i)) for i in date_str.split("/")]
    
filelist2 = list(filter(lambda f: 'date' in f and not f['name'].startswith("index"), filelist))
filelist3 = sorted(filelist2, key=lambda x: date_to_number(x['date']), reverse=True)
filelist4 = filelist3[0:100]
json.dump(filelist4, sys.stdout, ensure_ascii=False)

