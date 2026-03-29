<?php
header('Access-Control-Allow-Origin: *');

// Dubai Sports 1 - 2026 Token logic
$ch = curl_init("https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH");
curl_setopt_all($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/149.0.0.0'
]);
$res = curl_exec($ch);
curl_close($ch);

if (preg_match('/src="([^"]+)"/', $res, $m)) {
    // Redirect to the dynamic Akamai link
    header("Location: " . str_replace('&amp;', '&', $m[1]));
    exit;
} else {
    http_response_code(500);
    echo "Build Success, but Token Generation Failed. Check Source URL.";
}
