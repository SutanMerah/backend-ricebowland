<?php

// Trik agar Laravel mengenali base path di Vercel
$_ENV['APP_PATH'] = __DIR__;

// Panggil framework yang sudah terinstal di vendor
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Jalankan kernel dan kirim response
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);