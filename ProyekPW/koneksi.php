<?php
$host = "localhost";
$user = "root";     // Default user di Laragon/XAMPP
$pass = "";         // Default password biasanya kosong
$db   = "proyek_mb"; // GANTI dengan nama database yang ada di phpMyAdmin
$port = "3307";

// Membuat koneksi ke database
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// Mengecek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>