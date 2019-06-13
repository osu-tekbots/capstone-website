#!/usr/bin/env bash
#
# On the OSU servers the files and directories have to have certain permissions in order to be
# accessible from a browser. We set the correct permissions here.
for f in $(find . -not -path "./.git/*"); do
    if [ "$f" = "." ] || [ "$f" = ".." ]; then
        continue
    fi
    if [ -d "$f" ]; then
        chmod 755 "$f"
    else
        chmod 664 "$f"
    fi
done
