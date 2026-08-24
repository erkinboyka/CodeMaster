<?php
$ch = curl_init('https://stepik.org/api/courses/3078');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$r = curl_exec($ch);
echo "Stepik course 3078: " . strlen($r) . " bytes\n";
echo substr($r, 0, 300) . "\n\n";

$ch2 = curl_init('https://stepik.org/api/sections?course=3078');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0');
$r2 = curl_exec($ch2);
echo "Stepik sections: " . strlen($r2) . " bytes\n";
echo substr($r2, 0, 500) . "\n\n";

$ch3 = curl_init('https://metanit.com/net/');
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_TIMEOUT, 30);
curl_setopt($ch3, CURLOPT_USERAGENT, 'Mozilla/5.0');
$r3 = curl_exec($ch3);
echo "Metanit /net/: " . strlen($r3) . " bytes\n";
preg_match_all('/href="(\/net\/[^"]+)"/i', $r3, $m);
echo "Links found: " . count($m[1]) . "\n";
if (!empty($m[1])) {
    echo "Sample: " . $m[1][0] . "\n";
}
