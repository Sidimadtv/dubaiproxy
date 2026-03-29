<?php
header("Access-Control-Allow-Origin: *");

// 1. Get the dynamic tokenized SMIL
$smil_url = "https://link.theplatform.eu/s/dmimain/media/dmi-prod-live-media-dubaisports1?format=SMIL&formats=MPEG-DASH";

$ctx = stream_context_create([
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/149.0\r\n"
    ]
]);

$data = file_get_contents($smil_url, false, $ctx);

// 2. Extract the Akamai URL
preg_match('/src="([^"]+)"/', $data, $m);

if (isset($m[1])) {
    $clean_url = str_replace('&amp;', '&', $m[1]);
    // Redirect the browser to the real stream
    header("Location: " . $clean_url);
    exit;
} else {
    http_response_code(500);
    echo "Error: Akamai token generation failed. Check SMIL source.";
}
