<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "satu_sehat_db";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_errno) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>