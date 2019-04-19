#!/bin/sh
#
# This script enforces some rules before changes are committed. For now, it just
# sets the file permissions for all files and directories so they are accessible
# from the browser. To use it, execute the following in Linux or bash, or just
# copy the file as `pre-commit` in the .git/hooks directory.
#
# ln -s scripts/pre-commit.sh .git/hooks/pre-commit
#
echo "Starting pre-commit..."

echo "Changing file permissions for files and directories to be public before commit"
sh scripts/allow.sh

echo "Pre-commit done"