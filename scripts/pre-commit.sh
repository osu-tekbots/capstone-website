#!/bin/sh
#
# This script enforces some rules before changes are committed. For now, it just
# sets the file permissions for all files and directories so they are accessible
# from the browser. Copy this file as `pre-commit` into `.git/hooks` and make it
# executable.
#
echo "Starting pre-commit..."

echo "Changing file permissions for files and directories to be public before commit"
sh scripts/allow.sh

echo "Pre-commit done"