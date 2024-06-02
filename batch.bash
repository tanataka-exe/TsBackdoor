#!/bin/bash

CMD_FULLPATH=`realpath $0`
CMD_BASE=`dirname "$CMD_FULLPATH"`
BASEDIR=$1
BASEDIRNAME=`basename "$BASEDIR"`
BASEDIRPARENT=`dirname $1`

IFS='
'

#
# By default, the configuration file are placed at the same directory of this script!
# 
CONFIG=~/.config/TsBackdoor.conf

if [ ! -f "$CONFIG" ]
then
    #
    # Create a configuration file with a default contents if it doesn't exist
    #
    cat > "$CONFIG" <<EOF
static_path=$CMD_BASE/static
tmp_path=/tmp/TsBackdoor

# your bucket name goes here
aws_s3_bucket=$BUCKET

# your profile name goes here
aws_profile=$PROFILE
EOF
fi

#
# Static resources directory
#
STATICDIR=`grep static_path "$CONFIG" | cut -d '=' -f 2`
if [ ! -d "$STATICDIR" ]
then
    echo "\"static\" directory doesn't exist!" >&2
    exit 125
fi

#
# Temporary directory
#
TMPDIR=`grep tmp_path "$CONFIG" | cut -d '=' -f 2`
if [ -d "$TMPDIR" ]
then
    rm -r $TMPDIR
fi

cd "$BASEDIRPARENT"

for F in `find "$BASEDIRNAME" -type f`
do
    BASENAME=`basename "$F"`
    DIRPATH=`dirname "$F"`
    #
    # Change the extension of the file
    #
    OUTFILE=/tmp/$DIRPATH/${BASENAME%.*}.html
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
        python $CMD_BASE/parse-file.py "$F" | php $CMD_BASE/make-html.php > "$OUTFILE"
    else
        #
        # copy files 
        #
        cp "$F" "$OUTDIR"
    fi
done

mv "/tmp/$BASEDIRNAME" $TMPDIR
cp -r $STATICDIR/* $TMPDIR
sudo cp -r $TMPDIR/* /srv/http

#
# upload files what it genereted to S3 bucket
#
BUCKET=`grep aws_s3_bucket "$CONFIG" | cut -d '=' -f 2`
if [ ! -v BUCKET ] || [ "$BUCKET" = "" ]
then
    echo "aws_s3_bucket not exist or not been set in your configuration file!"
    exit 126
fi

PROFILE=`grep aws_profile "$CONFIG" | cut -d '=' -f 2`
if [ ! -v PROFILE ] || [ "$BUCKET" = "" ]
then
    echo "aws_profile not exist or not been set in your configuration file!"
    exit 127
fi    

export BUCKET PROFILE
$CMD_BASE/upload-s3.bash $TMPDIR
