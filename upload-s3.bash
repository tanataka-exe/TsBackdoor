#!/bin/bash

cd $1
aws s3 rm "s3://$BUCKET/" --recursive --profile $PROFILE
for F in `find . -type f`
do
    F=`echo $F | sed -e 's:^\./::'`
    aws s3 cp "$F" "s3://$BUCKET/$F" --profile $PROFILE
done
