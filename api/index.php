<?php
header('Access-Control-Allow-Origin: *');

// Dubai Sports 1 Handshake
$ch = curl_init("https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$res = curl_exec($ch);
curl_close($ch);

if (preg_match('/src="([^"]+)"/', $res, $m)) {
    $link = str_replace('&amp;', '&', $m[1]);
    header("Location: " . $link);
    exit;
} else {
    echo "ERROR: Akamai Token not found.";
}
