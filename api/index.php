<?php
header('Access-Control-Allow-Origin: *');

$url = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";

$ch = curl_init($url);
// CORRECT FUNCTION: curl_setopt_array
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/149.0.0.0'
]);

$res = curl_exec($ch);
curl_close($ch);

if (preg_match('/src="([^"]+)"/', $res, $m)) {
    $final_link = str_replace('&amp;', '&', $m[1]);
    header("Location: " . $final_link);
    exit;
} else {
    http_response_code(500);
    echo "ERROR: Akamai Token extraction failed. Raw Response: " . htmlspecialchars($res);
}
