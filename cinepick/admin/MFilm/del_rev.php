<?php
session_start();
include '../../db.php';

// Cek apakah pengguna sudah login dan memiliki role "admin"
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    // Jika tidak memiliki role "user", tampilkan pesan dan hentikan proses
    header('Location: ../../login.php' );
    session_destroy();
    exit();
}

if (isset($_GET['id'])) {
    $reviewID = $_GET['id'];

    // Hapus review berdasarkan reviewID
    $query = "DELETE FROM review WHERE reviewID = $reviewID";
    mysqli_query($conn, $query);

    // Cek apakah ada review yang tersisa di tabel
    $result = mysqli_query($conn, "SELECT MAX(reviewID) AS max_id FROM review");
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    if ($max_id) {
        // Set ulang AUTO_INCREMENT ke nilai max_id + 1
        $new_increment = $max_id + 1;
    } else {
        // Jika tidak ada review lagi, reset AUTO_INCREMENT ke 1
        $new_increment = 1;
    }

    // Update AUTO_INCREMENT
    $query = "ALTER TABLE review AUTO_INCREMENT = $new_increment";
    mysqli_query($conn, $query);

    // Redirect kembali ke halaman review list atau halaman lain yang sesuai
    header("Location: list.php");
    exit;
}
?>
