<?php
/**
 * Frontend script for controlling a media player.
 */

$pi = 'pi@ip-address';

/**
 * Get the volume from a remote player.
 * @param string $pi User and host to log in to
 * @return integer Percent volume the remote server is playing at
 */
function getVolume($pi) {
    exec('ssh ' . $pi . ' amixer', $output);
    $volume = array_pop($output);
    preg_match('/\[(\d*)/', $volume, $matches);
    return array_pop($matches);
}


/**
 * Set the volume on the remote player.
 * @param integer $volume Percentage volume to play at.
 * @param string $pi User and host to log in to
 */
function setVolume($volume, $pi) {
    $volume = escapeshellarg($volume) . '%';
    exec('ssh ' . $pi . ' amixer set PCM ' . $volume);
}


/**
 * Go to the next song and update the now playing file.
 * @param string $pi User and host to log in to
 */
function nextSong($pi) {
    exec('ssh ' . $pi . ' \'echo "pausing_keep_force pt_step 1" >> mplayer-control\'');
    sleep(1);
    exec('ssh ' . $pi . ' ./now-playing.sh');
}


/**
 * Return information about the currently playing song.
 * @param string $pi User and host to log in to
 * @return array ['artist', 'album', 'song']
 */
function getCurrentSong() {
    exec('ssh pi@192.168.1.87 cat playing.txt', $output);
    return array(
        'artist' => array_shift($output),
        'album' => array_shift($output),
        'song' => array_shift($output),
    );
}


/**
 * Toggle muting on or off.
 * @param boolean $mute Whether to mute or not.
 * @param string $pi User and host to log in to
 */
function mute($mute, $pi) {
    $mute = (int)$mute;
    exec('ssh ' . $pi . ' echo "mute ' . $mute . '" >> mplayer-control');
}


$volume = getVolume($pi);
if (isset($_POST['volume'])) {
    $volume = (int)$_POST['volume'];
    setVolume($volume, $pi);
} elseif (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'volumeUp':
            $volume += 5;
            setVolume((int)$volume, $pi);
            break;
        case 'volumeDown':
            $volume -= 5;
            setVolume((int)$volume, $pi);
            break;
        case 'next':
            nextSong($pi);
            break;
        case 'mute':
            mute(true, $pi);
            break;
        case 'unmute':
            mute(false, $pi);
            setVolume(60, $pi);
            break;
    }
}

$nowPlaying = getCurrentSong($pi);
$nowPlaying['song'] = preg_replace('/^\d+ /', '', $nowPlaying['song']);
?>
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta http-equiv="refresh" content="5">
    <title>Media player</title>
</head>

<body>
<?php
echo 'Playing: ', $nowPlaying['song'], ' by ', $nowPlaying['artist'], '<br>';
echo 'Current volume: ', $volume, '<br>';
?>
<form method="POST">
<div>
    <button name="action" value="volumeUp">Vol +</button>
    <button name="action" value="volumeDown">Vol -</button>
</div>
<div>
    <button name="action" value="mute">Mute</button>
    <button name="action" value="unmute">Unmute</button>
</div>
<div>
    <button name="volume" value="60">60%</button>
</div>
<div>
    <button name="action" value="next">Next</button>
</div>
</form>

</body>
</html>
