#!/bin/bash

BASEDIR=$1
IFS='
'
rm -r /tmp/html

CMD_BASE=/home/ta/work/else-not-implemented
cd "$BASEDIR"

for F in `find "text" -type f`
do
    BASENAME=`basename "$F"`
    BASEDIR=`dirname "$F"`
    #
    # Change the extension of the file
    #
    OUTFILE=/tmp/$BASEDIR/${BASENAME%.*}.html
    #
    # This is a directory what is a parent of the target file
    #
    OUTDIR=`dirname "$OUTFILE"`
    #
    # Create a parent directory if it does not exist
    #
    if [ ! -d "$OUTDIR" ]
    then
        mkdir -p "$OUTDIR"
    fi

    if [[ "$BASENAME" =~ .*\.md ]]
    then
        #
        # Parse the file and write it as a HTML file
        #
        python "$CMD_BASE/parse-file.py" "$F" | php "$CMD_BASE/make-html.php" > "$OUTFILE"
    else
        #
        # copy files 
        #
        cp "$F" "$OUTDIR"
    fi
done

mv /tmp/text /tmp/html
cp -r static/* /tmp/html
sudo cp -r /tmp/html/* /srv/http
#S3_BUCKET_NAME=test-static-site-ahalogist-01

