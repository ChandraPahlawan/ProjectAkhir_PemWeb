<?php
include '../db.php';
session_start();

// Pastikan hanya admin yang bisa mengakses halaman ini
if ($_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$query = "SELECT * FROM film";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="navbar">
            <div class="navbar-container">
                <div class="logo-container">
                    <h1 class="logo">CinePicks Admin Menu</h1>
                </div>
                <div class="toggle-logout">
                    <button class="logout-btn" onclick="window.location.href='../auth/logout.php';">Logout</button>
                </div>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="card">
            <div class="icon-container">
                <div class="icon">
                    <a href="MFilm/add.php"><img src="../img/add.png" alt="Tambah Film" width="100" height="100"></a>
                </div>
                <p class="icon-text">Tambah Film</p>
            </div>
        </div>
        <div class="card">
            <div class="icon-container">
                <div class="icon">
                    <a href="MFilm/list.php"><img src="../img/edit.png" alt="Sunting Film" width="100" height="100"></a>
                </div>
                <p class="icon-text">Sunting Film</p>
            </div>
        </div>
        </div>
        <div class="card">
            <div class="icon-container">
                <div class="icon">
                    <a href="MUser/list_user.php"><img src="../img/group.png" alt="Daftar Pengguna" width="100" height="100"></a>
                </div>
                <p class="icon-text">Daftar Pengguna</p>
            </div>
        </div>
    </main>
        <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 CinePicks. All Rights Reserved.</p>
    </footer>
</body>
</html>
