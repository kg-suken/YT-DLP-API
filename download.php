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



if(isset($_GET['setting']) && $_GET['setting'] == 'video') {
$output = shell_exec('yt-dlp --format mp4 -o ./download/'.$newvid.'.mp4 '.$youtube_url);
$file_url = './download/'.$newvid.'.mp4';
$file_name = $title . '.mp4';
header('Content-Description: File Transfer');
header('Content-Type: video/mp4');
header('Content-Disposition: attachment; filename="' . rawurlencode($file_name) . '"; filename*=UTF-8\'\'' . rawurlencode($file_name));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_url));
readfile($file_url);
exit;
}


if(isset($_GET['setting']) && $_GET['setting'] == 'video-high') {
$output = shell_exec('yt-dlp -f bestvideo+bestaudio[ext=m4a] --merge-output-format mp4 -o ./download/'.$newvid.'-high.mp4 '.$youtube_url);
$file_url = './download/'.$newvid.'-high.mp4';
$file_name = $title . '-high.mp4';
header('Content-Description: File Transfer');
header('Content-Type: video/mp4');
header('Content-Disposition: attachment; filename="' . rawurlencode($file_name) . '"; filename*=UTF-8\'\'' . rawurlencode($file_name));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_url));
readfile($file_url);
exit;
}

if(isset($_GET['setting']) && $_GET['setting'] == 'music') {
$output = shell_exec('yt-dlp -x --audio-format mp3 -o ./download/'.$newvid.'.mp3 '.$youtube_url);	
$file_url = './download/'.$newvid.'.mp3';
$file_name = $title . '.mp3';
header('Content-Description: File Transfer');
header('Content-Type: audio/mpeg');
header('Content-Disposition: attachment; filename="' . rawurlencode($file_name) . '"; filename*=UTF-8\'\'' . rawurlencode($file_name));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_url));
readfile($file_url);
exit;
}

if(isset($_GET['setting']) && $_GET['setting'] == 'music-worst') {
$output = shell_exec('yt-dlp -f worstaudio[ext=m4a]/worst[ext=mp3]/worst -o ./download/'.$newvid.'-worst.mp3 '.$youtube_url);	
$file_url = './download/'.$newvid.'-worst.mp3';
$file_name = $title . '-worst.mp3';
header('Content-Description: File Transfer');
header('Content-Type: audio/mpeg');
header('Content-Disposition: attachment; filename="' . rawurlencode($file_name) . '"; filename*=UTF-8\'\'' . rawurlencode($file_name));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_url));
readfile($file_url);
exit;
}

?>
