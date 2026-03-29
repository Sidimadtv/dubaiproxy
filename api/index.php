<?php
// 1. Silence all warnings so they don't corrupt the XML output
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/dash+xml");

// Fetch the Tokenized URL
$gateway = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";
$ch = curl_init($gateway);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/140.0.0.0'
]);
$smil = curl_exec($ch);
// REMOVED: curl_close($ch); (Deprecated in 8.5)

if (preg_match('/src="([^"]+)"/', $smil, $m)) {
    $mpd_url = str_replace('&amp;', '&', $m[1]);
    
    // Fetch the actual MPD content
    $ch2 = curl_init($mpd_url);
    curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/140.0.0.0'
    ]);
    $mpd_content = curl_exec($ch2);
    // REMOVED: curl_close($ch2); (Deprecated in 8.5)

    // Fix Relative Links: Tell the player where to find the video chunks on Akamai
    $base_url = explode('?', $mpd_url)[0];
    $base_path = substr($base_url, 0, strrpos($base_url, '/') + 1);
    
    // Inject the BaseURL into the XML so the player knows where to go
    $mpd_content = str_replace('<Period', '<BaseURL>' . $base_path . '</BaseURL><Period', $mpd_content);

    echo $mpd_content;
} else {
    http_response_code(500);
    echo "ERROR: Stream Generation Failed";
}
