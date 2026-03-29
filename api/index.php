<?php
// Force silence - absolutely no warnings allowed to print
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/dash+xml");

$gateway = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";

// Fetch the SMIL
$ch = curl_init($gateway);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/140.0.0.0'
]);
$smil = curl_exec($ch);

if (preg_match('/src="([^"]+)"/', $smil, $m)) {
    $mpd_url = str_replace('&amp;', '&', $m[1]);
    
    // Fetch the MPD
    $ch2 = curl_init($mpd_url);
    curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/140.0.0.0'
    ]);
    $mpd_content = curl_exec($ch2);

    // Fix the "Relative Path" issue by telling the player where Akamai lives
    $base_url = explode('?', $mpd_url)[0];
    $base_path = substr($base_url, 0, strrpos($base_url, '/') + 1);
    
    // Inject BaseURL so segments load from Akamai, not Vercel
    $mpd_content = str_replace('<Period', '<BaseURL>' . $base_path . '</BaseURL><Period', $mpd_content);

    echo $mpd_content;
}
// No curl_close here - PHP 8.5 will clean it up automatically
