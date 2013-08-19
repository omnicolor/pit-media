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
 * Return information about the currently playing song.
 * @return array ['song', 'artist']
 */
function getCurrentSong() {
    $file = file('/tmp/ices.cue');
    return array(
        'song' => array_pop($file),
        'artist' => array_pop($file),
    );
}

$volume = getVolume();
echo 'Current volume: ', $volume, '<br>';
$song = getCurrentSong();
echo 'Playing: ', $song['song'], ' by ', $song['artist'], '<br>';

if (isset($_GET['volume'])) {
    $volume = (int)$_GET['volume'];
    setVolume($volume);
    echo 'Volume changed to ', $volume, '<br>';
} elseif (isset($_GET['volumeUp'])) {
    $volume += 10;
    setVolume((int)$volume);
    echo 'Volume changed to ', getVolume(), '<br>';
} elseif (isset($_GET['volumeDown'])) {
    $volume -= 10;
    setVolume((int)$volume);
    echo 'Volume changed to ', getVolume(), '<br>';
}
?>
<input type="range" id="volume" min="0" max="100" value="<?php echo $volume; ?>">
