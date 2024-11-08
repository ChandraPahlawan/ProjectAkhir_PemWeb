<?php
$host = 'localhost';
$dbname = 'dbcp';
$username = 'root'; // sesuaikan username MySQL Anda
$password = ''; // sesuaikan password MySQL Anda

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
