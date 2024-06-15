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

