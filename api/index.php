<?php
header('Access-Control-Allow-Origin: *');

// We use a simple curl to avoid 'file_get_contents' restriction on some runtimes
$ch = curl_init("https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$res = curl_exec($ch);
curl_close($ch);

preg_match('/src="([^"]+)"/', $res, $m);

if (isset($m[1])) {
    $link = str_replace('&amp;', '&', $m[1]);
    header("Location: " . $link);
    exit;
} else {
    echo "Check failed: SMIL Source Unreachable";
}
