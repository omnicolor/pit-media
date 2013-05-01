<?php
function getVolume() {
    exec('ssh pi@192.168.1.87 amixer', $output);
    $volume = array_pop($output);
    preg_match('/\[(\d*)/', $volume, $matches);
    return array_pop($matches);
}

function setVolume($volume) {
    $volume = escapeshellarg($volume) . '%';
    exec('ssh pi@192.168.1.87 amixer set PCM ' . $volume);
}

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
    setVolume((int)$_GET['volume']);
    echo 'Volume changed to ', getVolume(), '<br>';
} elseif (isset($_GET['volumeUp'])) {
    $volume += 10;
    setVolume((int)$volume);
    echo 'Volume changed to ', getVolume(), '<br>';
} elseif (isset($_GET['volumeDown'])) {
    $volume -= 10;
    setVolume((int)$volume);
    echo 'Volume changed to ', getVolume(), '<br>';
}
