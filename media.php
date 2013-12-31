<?php
/**
 * Frontend script for controlling a media player.
 */


/**
 * Get the volume from a remote player.
 * @return integer Percent volume the remote server is playing at.
 */
function getVolume() {
    exec('ssh pi@192.168.1.87 amixer', $output);
    $volume = array_pop($output);
    preg_match('/\[(\d*)/', $volume, $matches);
    return array_pop($matches);
}


/**
 * Set the volume on the remote player.
 * @param integer $volume Percentage volume to play at.
 */
function setVolume($volume) {
    $volume = escapeshellarg($volume) . '%';
    exec('ssh pi@192.168.1.87 amixer set PCM ' . $volume);
}


/**
 * Go to the next song and update the now playing file.
 */
function nextSong() {
    exec('ssh pi@192.168.1.87 \'echo "pausing_keep_force pt_step 1" >> mplayer-control\'');
    sleep(1);
    exec('ssh pi@192.168.1.87 ./now-playing.sh');
}


/**
 * Return information about the currently playing song.
 * @return array ['song', 'artist']
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
 */
function mute($mute) {
    $mute = (int)$mute;
    exec('ssh pi@192.168.1.87 echo "mute ' . $mute . '" >> mplayer-control');
}


$volume = getVolume();
if (isset($_POST['volume'])) {
    $volume = (int)$_POST['volume'];
    setVolume($volume);
} elseif (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'volumeUp':
            $volume += 5;
            setVolume((int)$volume);
            break;
        case 'volumeDown':
            $volume -= 5;
            setVolume((int)$volume);
            break;
        case 'next':
            nextSong();
            break;
        case 'mute':
            mute(true);
            break;
        case 'unmute':
            mute(false);
            setVolume(60);
            break;
    }
}

$nowPlaying = getCurrentSong();
$nowPlaying['song'] = preg_replace('/^\d+ /', '', $nowPlaying['song']);
?>
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
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
