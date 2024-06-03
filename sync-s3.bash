#!/bin/bash

aws s3 sync "$1" "s3://$BUCKET/" --profile $PROFILE
