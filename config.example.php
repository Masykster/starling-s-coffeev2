<?php
// Contoh Konfigurasi Database
// Copy file ini menjadi config.php dan sesuaikan dengan konfigurasi database Anda

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'starling_coffee');

// Membuat koneksi ke database
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    // Set charset ke UTF-8
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Fungsi untuk mendapatkan path halaman saat ini
function getCurrentPage() {
    $page = basename($_SERVER['PHP_SELF'], '.php');
    return $page;
}
?>

