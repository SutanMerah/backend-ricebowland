<?php
// index.php

$host = 'db.adfrmljnbgdcajwpocco.supabase.co';
$db   = 'postgres';
$user = 'postgres';
$pass = 'jangandihackya';
$port = '5432';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "KONEKSI DATABASE BERHASIL!";
} catch (\PDOException $e) {
    echo "KONEKSI GAGAL: " . $e->getMessage();
}
exit;