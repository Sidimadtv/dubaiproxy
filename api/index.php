<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: *");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

// 1. Get the source SMIL
$gateway = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";
$ch = curl_init($gateway);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/140.0.0.0'
]);
$smil = curl_exec($ch);

if (preg_match('/src="([^"]+)"/', $smil, $m)) {
    $mpd_url = str_replace('&amp;', '&', $m[1]);
    
    // 2. Fetch the actual MPD content
    curl_setopt($ch, CURLOPT_URL, $mpd_url);
    $mpd_data = curl_exec($ch);

    // 3. Extract the Akamai Token and Base Path
    $query_string = parse_url($mpd_url, PHP_URL_QUERY);
    $base_path = substr($mpd_url, 0, strrpos(explode('?', $mpd_url)[0], '/') + 1);

    // 4. Inject the BaseURL and Token into the Manifest
    // This tells the player: "Load segments from Akamai, but use THIS token."
    $injection = "<BaseURL>" . $base_path . "</BaseURL>";
    $mpd_data = str_replace('<Period', $injection . '<Period', $mpd_data);
    
    // Fix the segment template to include the token on every request
    $mpd_data = str_replace('.m4v', '.m4v?' . $query_string, $mpd_data);
    $mpd_data = str_replace('.m4a', '.m4a?' . $query_string, $mpd_data);
    $mpd_data = str_replace('.m4i', '.m4i?' . $query_string, $mpd_data);

    header("Content-Type: application/dash+xml");
    echo $mpd_data;
}
