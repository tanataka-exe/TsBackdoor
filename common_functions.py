import os
import re

def replace_filename_extension(filename, new_extension):
    if filename.rfind('.') >= 0:
        return filename[0:filename.rfind('.')] + '.' + new_extension
    else:
        return filename

def remove_extension(filename):
    return re.sub('\\.[^.]+$', '', filename)

def read_file_data(filename, require_contents = True):
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
    try:
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
    
            if 'title' not in data:
                data['title'] = remove_extension(data['name'])
    
            if not require_contents:
                return data
            
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

    except Exception as err: 
        raise err
        
    #
    # file operation finished
    #
    return data
