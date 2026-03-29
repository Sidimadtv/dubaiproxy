<?php
header('Access-Control-Allow-Origin: *');

$channel = "dubaisports1";
$api = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-" . $channel . "?format=SMIL&formats=MPEG-DASH";

// Use CURL - it's more stable on Vercel
$ch = curl_init($api);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/149.0.0.0 Safari/537.36');
$res = curl_exec($ch);
curl_close($ch);

// Find the Akamai URL with the hdntl token
if (preg_match('/src="([^"]+)"/', $res, $m)) {
    $link = str_replace('&amp;', '&', $m[1]);
    header("Location: " . $link);
    exit;
} else {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error: Akamai token could not be generated.";
}
