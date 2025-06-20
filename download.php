<?php
$url = $_SERVER['REQUEST_URI'];
$setting = $_GET['setting'];
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($searchQuery)) {
    $vid = shell_exec('yt-dlp --get-id "ytsearch:'. $searchQuery.'"');
    $vid = preg_replace('/\s+/', '', $vid);
    $url = '/download.php?vid='.$vid.'&setting='.$setting;
}

$vid_start = strpos($url, '?vid=');
if ($vid_start === false) {
    $vid_start = strpos($url, '&vid=');
}
if ($vid_start !== false) {
    $setting_start = strpos($url, '?setting=', $vid_start);
    if ($setting_start === false) {
        $setting_start = strpos($url, '&setting=', $vid_start);
    }

    if ($setting_start !== false) {
        $vid_end = $setting_start;
        $vid = substr($url, $vid_start + 5, $vid_end - ($vid_start + 5));
    } else {
        $vid = substr($url, $vid_start + 5);
    }
}

function extractVid($url) {
    $parsedUrl = parse_url($url);
    $path = $parsedUrl['path'];
    $query = $parsedUrl['query'] ?? '';

    // Extract $vid from different URL patterns
    if (preg_match('/\/shorts\/([^\/?]+)/', $path, $matches)) {
        return $matches[1];
    } elseif (preg_match('/\/watch/', $path) && preg_match('/v=([^&]+)/', $query, $matches)) {
        return $matches[1];
    } elseif (preg_match('/youtu\.be\/([^\/?]+)/', $url, $matches)) {
        return $matches[1];
    } elseif (preg_match('/^\/watch$/', $path) && preg_match('/v=([^&]+)/', $query, $matches)) {
        return $matches[1];
    }

    // Extract $vid when query parameter is before the path
    if (preg_match('/v=([^&]+)/', $query, $matches)) {
        return $matches[1];
    }
    return false; // $vid not found

}

if (strpos($vid, 'http') === 0) {
    $vid = extractVid($vid);
}
$newvid = $vid;
if (substr($vid, 0, 1) === "-") {
    $newvid = "underscore" . substr($vid, 1);
}
$youtube_url = "https://www.youtube.com/watch?v=".$vid;

$sourceCode = file_get_contents($youtube_url);
$titleStart = strpos($sourceCode, '<title>');
$titleEnd = strpos($sourceCode, '</title>', $titleStart);
$title = substr($sourceCode, $titleStart + 7, $titleEnd - $titleStart - 7);

$settings = [
    'video' => [
        'cmd' => 'yt-dlp --format mp4 -o ./download/'.$newvid.'.mp4 '.$youtube_url,
        'file' => './download/'.$newvid.'.mp4',
        'name' => $title . '.mp4',
        'type' => 'video/mp4'
    ],
    'video-high' => [
        'cmd' => 'yt-dlp -f bestvideo+bestaudio[ext=m4a] --merge-output-format mp4 -o ./download/'.$newvid.'-high.mp4 '.$youtube_url,
        'file' => './download/'.$newvid.'-high.mp4',
        'name' => $title . '-high.mp4',
        'type' => 'video/mp4'
    ],
    'music' => [
        'cmd' => 'yt-dlp -x --audio-format mp3 -o ./download/'.$newvid.'.mp3 '.$youtube_url,
        'file' => './download/'.$newvid.'.mp3',
        'name' => $title . '.mp3',
        'type' => 'audio/mpeg'
    ],
    'music-worst' => [
        'cmd' => 'yt-dlp -f worstaudio[ext=m4a]/worst[ext=mp3]/worst -o ./download/'.$newvid.'-worst.mp3 '.$youtube_url,
        'file' => './download/'.$newvid.'-worst.mp3',
        'name' => $title . '-worst.mp3',
        'type' => 'audio/mpeg'
    ],
    'music-wav' => [
        'cmd' => 'yt-dlp -f bestaudio[ext=m4a]/bestaudio --extract-audio --audio-format wav -o ./download/'.$newvid.'-best.wav '.$youtube_url,
        'file' => './download/'.$newvid.'-best.wav',
        'name' => $title . '-best.wav',
        'type' => 'audio/wav'
    ]
];

function downloadAndSend($cmd, $file_url, $file_name, $content_type) {
    shell_exec($cmd);
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $content_type);
    header('Content-Disposition: attachment; filename="' . rawurlencode($file_name) . '"; filename*=UTF-8\'\'' . rawurlencode($file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_url));
    readfile($file_url);
    exit;
}

if (isset($settings[$setting])) {
    $s = $settings[$setting];
    downloadAndSend($s['cmd'], $s['file'], $s['name'], $s['type']);
}

?>