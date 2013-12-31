#! /bin/bash
# Determines the currently playing track and outputs it to a file
lsof -p $(pidof mplayer) | grep "/home/pi/music/" | egrep '(mp3)|(m4a)|(m4p)' \
    | cut -d'/' -f2- | sed 's/home\/pi\/music\///' | sed 's/\.mp3//' \
    | sed 's/\//\n/g' > ~/playing.txt
