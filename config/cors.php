<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['*', 'api/*', 'orders', 'sanctum/csrf-cookie'], // Tambahkan '*' atau rute spesifikmu di sini

    'allowed_methods' => ['*'], // Mengizinkan semua method (POST, GET, dll)

    'allowed_origins' => ['*'], // Mengizinkan diakses dari origin mana saja (termasuk localhost react native kamu)

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Mengizinkan semua jenis header

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

    ];