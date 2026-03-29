<?php
error_reporting(0);
ini_set('display_errors', 0);

// 1. Critical CORS for the player
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/dash+xml");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

$url = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";

// STEP 1: Get the tokenized MPD URL
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/149.0.0.0'
]);
$res = curl_exec($ch);

if (preg_match('/src="([^"]+)"/', $res, $m)) {
    $mpd_url = str_replace('&amp;', '&', $m[1]);

    // STEP 2: Fetch the actual XML content of the MPD
    curl_setopt($ch, CURLOPT_URL, $mpd_url);
    $xml_content = curl_exec($ch);

    // STEP 3: The "Magic" Fix
    // We extract the base path from the Akamai URL and inject it into the XML
    // This forces the player to get video segments directly from Akamai
    $base_path = substr($mpd_url, 0, strrpos(explode('?', $mpd_url)[0], '/') + 1);
    
    if (strpos($xml_content, '<BaseURL>') === false) {
        $xml_content = str_replace('<Period', '<BaseURL>' . $base_path . '</BaseURL><Period', $xml_content);
    }

    echo $xml_content;
} else {
    http_response_code(500);
    echo "ERROR_SOURCE_UNREACHABLE";
}
