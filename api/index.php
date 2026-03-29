<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/dash+xml");

// 1. Fetch the Tokenized URL from thePlatform
$gateway = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";
$ch = curl_init($gateway);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/140.0.0.0');
$smil = curl_exec($ch);
curl_close($ch);

if (preg_match('/src="([^"]+)"/', $smil, $m)) {
    $mpd_url = str_replace('&amp;', '&', $m[1]);
    
    // 2. Fetch the actual MPD content
    $ch2 = curl_init($mpd_url);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/140.0.0.0');
    $mpd_content = curl_exec($ch2);
    curl_close($ch2);

    // 3. Fix Relative Links: Make segments point back to Akamai directly
    // This bypasses the IP-binding issue by "baking" the base URL into the manifest
    $base_url = explode('?', $mpd_url)[0];
    $base_path = substr($base_url, 0, strrpos($base_url, '/') + 1);
    
    $mpd_content = str_replace('<Period', '<BaseURL>' . $base_path . '</BaseURL><Period', $mpd_content);

    echo $mpd_content;
} else {
    echo "ERROR: Could not generate stream.";
}
