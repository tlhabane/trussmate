<?php

$slash = DIRECTORY_SEPARATOR;
$constants = [];

/* Base url */
$base_url = 'http://localhost/trussmate/backend-php';
$constants['baseUrl'] = $base_url;
$constants['directory_separator'] = $slash;

/* Attachments */
$constants['attachments'] = [
    'directory' => __DIR__ . "{$slash}..{$slash}public{$slash}attachments{$slash}",
    'url' => "{$base_url}/attachments"
];

/* Uploads */
$constants['uploads'] = [
    'directory' => __DIR__ . "{$slash}..{$slash}public{$slash}uploads{$slash}",
    'url' => "{$base_url}/uploads"
];

/* Downloads */
$constants['downloads'] = [
    'directory' => __DIR__ . "{$slash}..{$slash}public{$slash}downloads{$slash}",
    'url' => "{$base_url}/downloads"
];

return $constants;
