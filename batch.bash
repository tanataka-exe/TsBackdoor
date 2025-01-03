#!/bin/bash
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

#SITE_NAME="T's Backdoor"
APP_NAME=TsBackdoor
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
CONFIG=~/.config/${APP_NAME}.conf

if [ ! -f "$CONFIG" ]
then
    if [ ! -v SITE_NAME ];
    then
        echo "Please set the environment variable named SITE_NAME."
        exit 119
    fi
    if [ ! -v SERVER_BASE ]
    then
        echo "Please set the environment variable named SERVER_BASE."
        exit 122
    fi
    if [ ! -v SITE_URL ]
    then
        echo "Please set the environment variable named SITE_URL."
        exit 123
    fi
    
    #
    # Create a configuration file with a default contents if it doesn't exist
    #
    cat > "$CONFIG" <<EOF
site_name=${SITE_NAME}
static_path=$CMD_BASE/static
tmp_path=/tmp/${APP_NAME}
server_basedir=${SERVER_BASE}
site_url=${SITE_URL}
EOF
fi

#
# Site name
#
SITENAME=`grep site_name "$CONFIG" | cut -d '=' -f 2`
if [ "$SITENAME" = "" ]
then
    echo "set a \"site_name\" in configuration file" >&2
    exit 123
fi

#
# Static resources directory
#
STATICDIR=`grep static_path "$CONFIG" | cut -d '=' -f 2`
if [ ! -d "$STATICDIR" ]
then
    echo "\"static\" directory doesn't exist!" >&2
    exit 124
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

#
# Site URL
#
SITEURL=`grep site_url "$CONFIG" | cut -d '=' -f 2`
if [ "$SITEURL" = "" ]
then
    echo "set a \"site_url\" in configuration file" >&2
    exit 126
fi

for F in `find "$BASEDIRNAME" -type f -not -path "$BASEDIRNAME/.git/*"`
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
        python $CMD_BASE/parse-file.py "$F" | php $CMD_BASE/make-html.php "$SITENAME" > "$OUTFILE"
	if [ "$?" -ne 0 ]
	then
	    echo "ERROR: Some error has occured on file $F" >&2
	fi
    else
        #
        # copy files 
        #
        cp "$F" "$OUTDIR"
    fi
done

python $CMD_BASE/get-rss-seed.py "$BASEDIR" | php $CMD_BASE/make-rss.php "$SITENAME" "$SITEURL" > /tmp/$BASEDIRNAME/rss.xml

if [ "/tmp/$BASEDIRNAME" != $TMPDIR ]
then
    mv "/tmp/$BASEDIRNAME" $TMPDIR
fi
cp -rp $STATICDIR/* $TMPDIR
SRVBASE=`grep server_basedir "$CONFIG" | cut -d '=' -f 2`
sudo rm -r $SRVBASE/*
sudo cp -r $TMPDIR/* $SRVBASE

