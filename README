# Web frontend for controlling mplayer on another server

I've got a Raspberry Pi hooked up to my house's sound system. I've mounted my
music collection on the Pi, and built a playlist. I use mplayer to play the
playlist, but wanted a way to easily control the volume and current track
remotely so I wouldn't have to walk to the master closet to fiddle with a knob
or to my office to mess with the CLI.

## Mounting music on the Pi

Use sshfs. I've got ssh keys set up so that running:

    pi@master ~ $ sshfs media-server:/home/music music

will mount /home/music on the machine named media-server on the Pi's home
directory in a new directory called music.

## Creating a named pipe to control mplayer

The media player is controlled through a named pipe, which you'll have to
create.

    pi@master ~ $ mkfifo mplayer-control

## Use screen or tmux

Running the media player and watch command should be done in screen or tmux so
you can detach from it and leave it running in the background. I use screen.
Everyone else uses tmux. Press "ctrl-a c" to create a new screen. Use "ctrl-a d"
to detach. screen -r will reattach.

    pi@master ~ $ screen bash

## Starting mplayer

Start the mplayer program in the music directory looking for your named pipe for
input and your playlist (mine's called party.m3u).

    pi@master ~ $ cd music
    pi@master ~/music $ mplayer -quiet -slave -input file=~/mplayer-control -shuffle -playlist party.m3u

## Start the now playing script

Start the watch script in the Pi's home directory. It will run the now playing
script every five seconds so the now playing display is relatively recent.

    pi@master ~/music $ cd
    pi@master ~ $ watch -n 5 ./now-playing.sh

## Using the web frontend

Finally, put media.php in a web directory that can run PHP scripts and set the
IP address for the Pi. You'll want to have SSH keys set up for it to log in
unattended.
