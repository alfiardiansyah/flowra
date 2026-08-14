<?php

// Serverless PHP handler for Vercel deployment
// Prepare required writable storage directories in /tmp
$storageDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/testing',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

putenv('APP_STORAGE_PATH=/tmp/storage');
$_ENV['APP_STORAGE_PATH'] = '/tmp/storage';

// Forward request to Laravel public entrypoint
require __DIR__ . '/../public/index.php';
