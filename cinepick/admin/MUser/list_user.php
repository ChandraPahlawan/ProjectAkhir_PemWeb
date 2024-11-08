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

// Query untuk mengambil semua data pengguna dari tabel akun
$query = "SELECT * FROM akun";
$result = mysqli_query($conn, $query);

// Cek apakah pengguna saat ini adalah admin
$currentUserRole = $_SESSION['role'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Poppins', sans-serif; 
        }
        body {
            background-color: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        h2 {
            color: #4dbf00;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            max-width: 1000px;
            border-collapse: collapse;
            margin-top: 10px;
            color: #fff;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #333;
        }
        th {
            background-color: #111;
            color: #4dbf00;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #222;
        }
        a {
            color: #ff4d4d;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            color: #ff1a1a;
        }
        .left-button {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #000;
            background: #4dbf00;
            padding: 10px 15px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s;
        }
        .left-button:hover {
            background-color: #3e9d00;
        }
    </style>
</head>
<body>
<a href="../admin_dash.php" class="left-button">Kembali</a>

<h2>Daftar Pengguna</h2>

<table>
    <tr>
        <th>ID Pengguna</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Aksi</th>
    </tr>

    <?php while ($user = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($user['userID']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td>
                <?php if ($user['role'] !== 'admin' || $currentUserRole !== 'admin') { ?>
                    <a href="del_user.php?id=<?php echo $user['userID']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                <?php } else { ?>
                    <span style="color: #888;">Tidak dapat menghapus</span>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
