<?php
// 1. SILENCE ERRORS (Prevent corruption of the XML)
error_reporting(0);
ini_set('display_errors', 0);

// 2. FORCE STREAMING HEADERS
header("Content-Type: application/dash+xml");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Expose-Headers: *");
// This tells the browser: "Do NOT download this, just show it."
header("Content-Disposition: inline");
header("Cache-Control: no-cache, no-store, must-revalidate");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

$gateway = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";

$ch = curl_init($gateway);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/149.0.0.0'
]);
$res = curl_exec($ch);

if (preg_match('/src="([^"]+)"/', $res, $m)) {
    $mpd_url = str_replace('&amp;', '&', $m[1]);
    $mpd_content = file_get_contents($mpd_url);

    // Fix the BaseURL so segments load from Akamai
    $base_path = substr($mpd_url, 0, strrpos(explode('?', $mpd_url)[0], '/') + 1);
    $mpd_content = str_replace('<Period', '<BaseURL>' . $base_path . '</BaseURL><Period', $mpd_content);

    echo $mpd_content;
}
