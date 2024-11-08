<?php
session_start();
include '../../db.php';

// Cek apakah pengguna sudah login dan memiliki role "admin"
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../../login.php');
    session_destroy();
    exit();
}

// Mengecek apakah ada parameter 'id' di URL
if (isset($_GET['id'])) {
    $userID = $_GET['id'];

    $query = "DELETE FROM review WHERE userID = $userID";
    mysqli_query($conn, $query);
    // Query untuk menghapus pengguna berdasarkan userID
    $query = "DELETE FROM akun WHERE userID = $userID";
    mysqli_query($conn, $query);

    // Cek apakah ada userID yang tersisa di tabel
    $result = mysqli_query($conn, "SELECT MAX(userID) AS max_id FROM akun");
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    
    if ($max_id) {
        // Set ulang AUTO_INCREMENT ke nilai max_id + 1 agar sesuai dengan userID tertinggi saat ini
        $new_increment = $max_id + 1;
    } else {
        // Jika tidak ada pengguna lagi, reset AUTO_INCREMENT ke 1
        $new_increment = 1;
    }
    
    // Set ulang AUTO_INCREMENT untuk tabel akun
    $query = "ALTER TABLE akun AUTO_INCREMENT = $new_increment";
    mysqli_query($conn, $query);
    
    // Mengarahkan kembali ke halaman daftar pengguna setelah penghapusan
    header("Location: list_user.php");
    exit();
} else {
    // Jika 'id' tidak ditemukan di URL, kembalikan ke halaman daftar pengguna
    header("Location: list_user.php");
    exit();
}
?>
