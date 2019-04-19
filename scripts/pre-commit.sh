#!/bin/sh
#
# This script enforces some rules before changes are committed. For now, it just
# sets the file permissions for all files and directories so they are accessible
# from the browser. Copy this file as `pre-commit` into `.git/hooks` and make it
# executable.
#
echo "Starting pre-commit..."

echo "Making sure permissions for files and directories are public"
for f in $(find . -not -path "./.git/*"); do
    if [ "$f" = "." ] || [ "$f" = ".." ] || [ "$f" ]; then
        continue
    fi
    FILE_PERMISSIONS=$(stat -c "%a" $f)
    if [ -d $f ] && [ "$FILE_PERMISSIONS" != '755' ]; then
        echo "Found directory '$f' with incorrect permissions '$FILE_PERMISSIONS'"
        echo "Run 'sh scripts/allow.sh' from the repository root before committing"
        exit 1
    elif [ -f $f ] && [ "$FILE_PERMISSIONS" -ne '664' ]; then
        echo "Found file '$f' with incorrect permissions '$FILE_PERMISSIONS'"
        echo "Run 'sh scripts/allow.sh' from the repository root before committing"
        exit 1
    fi
done

echo "Pre-commit done"