<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: *");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

// Check if we are requesting a specific video segment
if (isset($_GET['ts'])) {
    $segmentUrl = base64_decode($_GET['ts']);
    $ch = curl_init($segmentUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/149.0.0.0');
    $data = curl_exec($ch);
    
    // Set correct headers for video/audio chunks
    if (strpos($segmentUrl, '.m4a') !== false) header("Content-Type: audio/mp4");
    else header("Content-Type: video/mp4");
    
    echo $data;
    exit;
}

// Otherwise, fetch the Manifest
$gateway = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";
$ch = curl_init($gateway);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$res = curl_exec($ch);

if (preg_match('/src="([^"]+)"/', $res, $m)) {
    $mpd_url = str_replace('&amp;', '&', $m[1]);
    $mpd_data = file_get_contents($mpd_url);

    $base_url = explode('?', $mpd_url)[0];
    $base_path = substr($base_url, 0, strrpos($base_url, '/') + 1);
    $token = explode('?', $mpd_url)[1];

    // THE MAGIC: Rewrite the manifest so EVERY segment goes through YOUR Vercel proxy
    $proxy_root = "https://" . $_SERVER['HTTP_HOST'] . "/api/index.php?ts=";
    
    // We encode the full Akamai URL for each segment into a Base64 string for our proxy
    $mpd_data = str_replace('$RepresentationID$/', $proxy_root . base64_encode($base_path . '$RepresentationID$/'), $mpd_data);

    header("Content-Type: application/dash+xml");
    echo $mpd_data;
}
