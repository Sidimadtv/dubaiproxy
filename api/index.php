<?php
error_reporting(0);
ini_set('display_errors', 0);

// CRITICAL: These 3 lines allow the player to read the redirect
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

$url = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/149.0.0.0'
]);

$res = curl_exec($ch);

if (preg_match('/src="([^"]+)"/', $res, $m)) {
    $final_link = str_replace('&amp;', '&', $m[1]);
    header("Location: " . $final_link);
    exit;
} else {
    http_response_code(500);
    echo "ERROR: Token extraction failed.";
}
