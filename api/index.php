<?php

// Pastikan Vercel melacak autoloader Vendor secara absolut
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}

// Jalankan aplikasi Laravel bawaan public
require __DIR__ . '/../public/index.php';