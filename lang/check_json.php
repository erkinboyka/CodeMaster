<?php
$langDir = __DIR__;
$langs = ['en', 'ru', 'tg'];
foreach ($langs as $lang) {
    $file = $langDir . '/' . $lang . '.json';
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    $err = json_last_error_msg();
    $valid = ($data !== null) ? 'YES' : 'NO';
    $count = is_array($data) ? count($data) : 0;
    $bom = (substr($raw, 0, 3) === "\xEF\xBB\xBF") ? 'BOM!' : 'no BOM';
    echo "$lang.json: valid=$valid err=$err keys=$count $bom\n";
    
    if (!$data) continue;
    
    // Check specific keys
    $checks = ['peer.title', 'peer.hero_desc', 'peer.create_room', 'peer.join', 'interview_prep_title'];
    foreach ($checks as $key) {
        $val = $data[$key] ?? 'MISSING';
        $same = ($val === $key) ? ' <-- SAME AS KEY!' : '';
        echo "  $key => $val$same\n";
    }
}
