<?php

// Enable error reporting to diagnose the 500 error on Vercel
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Vercel serverless environment is read-only except for the '/tmp' directory.
// Set up directory paths for Laravel's runtime storage.
$storagePath = '/tmp/storage';

$folders = [
    $storagePath,
    $storagePath . '/framework',
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/sessions',
    $storagePath . '/logs',
];

foreach ($folders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
}

// Override Laravel environment variables to write compile data & cache to '/tmp'
putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");
putenv("SESSION_DRIVER=cookie"); // Avoid write errors; store sessions in cookies
putenv("LOG_CHANNEL=stderr");     // Stream logs directly to Vercel logs stdout/stderr
putenv("CACHE_STORE=array");       // Prevent file caching errors on read-only system

// Forward request execution to the original Laravel entry point
require __DIR__ . '/../public/index.php';
