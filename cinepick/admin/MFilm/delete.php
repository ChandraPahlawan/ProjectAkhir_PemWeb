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
    $filmID = $_GET['id'];

    // Get the current picture filename to delete it from the uploads folder
    $query = "SELECT upPic FROM film WHERE filmID = $filmID";
    $result = mysqli_query($conn, $query);
    $film = mysqli_fetch_assoc($result);
    $upPic = $film['upPic'];

    // Delete the picture file if it exists
    if ($upPic && file_exists("uploads/" . $upPic)) {
        unlink("uploads/" . $upPic);
    }

    // Delete the film record from the database
    $query = "DELETE FROM film WHERE filmID = $filmID";
    mysqli_query($conn, $query);

    // Reset the auto-increment value if necessary
    $query = "ALTER TABLE film AUTO_INCREMENT = 1";
    mysqli_query($conn, $query);

    // Redirect to the list page
    header("Location: list.php");
}
?>
