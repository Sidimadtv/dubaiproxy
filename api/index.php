<?php
// 1. Prevent any warnings from leaking and breaking the redirect
error_reporting(0);
ini_set('display_errors', 0);

header('Access-Control-Allow-Origin: *');

$url = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/149.0.0.0'
]);

$res = curl_exec($ch);

// 2. Removed curl_close($ch); as it is deprecated in PHP 8.5+

if (preg_match('/src="([^"]+)"/', $res, $m)) {
    $final_link = str_replace('&amp;', '&', $m[1]);
    
    // 3. The Redirect
    header("Location: " . $final_link);
    exit;
} else {
    http_response_code(500);
    echo "ERROR: Token extraction failed.";
}
