#!/bin/bash

# Variables
REMOTE_SERVER="roni@192.168.0.19/"
REMOTE_DIR="/roni/home/"
LOCAL_FILE="$1"

# Check if file exists
if [ ! -f "$LOCAL_FILE" ]; then
    echo "File not found!"
    exit 1
fi

# Upload file using SCP
scp "$LOCAL_FILE" "$REMOTE_SERVER":"$REMOTE_DIR"

# Check if SCP was successful
if [ $? -eq 0 ]; then
    echo "File uploaded successfully to $REMOTE_SERVER:$REMOTE_DIR"
else
    echo "File upload failed."
fi
