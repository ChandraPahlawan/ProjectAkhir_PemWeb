<?php
// Memulai sesi dan menghubungkan ke database
include 'db.php';
session_start();

// Cek apakah pengguna telah login dengan role "user"
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: auth/login.php');
    session_destroy();
    exit();
}

// Cek apakah userID tersimpan dalam sesi
if (!isset($_SESSION['userID'])) {
    header("Location: auth/login.php");
    exit;
}

$userID = $_SESSION['userID'];
$message = '';

// Mengambil data pengguna saat ini
$query = "SELECT username, email, profile_pic FROM akun WHERE userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Memproses pembaruan profil jika form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $newPassword = $_POST['newPassword'];
    $profile_pic = $_FILES['profile_pic']['name'];
    $currentPic = $user['profile_pic'];

    // Validasi panjang password baru minimal 8 karakter
    if (!empty($newPassword) && strlen($newPassword) < 8) {
        $message = "Password baru harus memiliki minimal 8 karakter!";
    } else {
        // Mengatur query sesuai dengan input yang diisi
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE akun SET username = ?, email = ?, password = ? WHERE userID = ?");
            $stmt->bind_param("sssi", $username, $email, $hashedPassword, $userID);
        } else {
            $stmt = $conn->prepare("UPDATE akun SET username = ?, email = ? WHERE userID = ?");
            $stmt->bind_param("ssi", $username, $email, $userID);
        }

        // Menjalankan update data user
        $stmt->execute();

        // Mengelola update foto profil jika ada file yang di-upload
        if ($profile_pic) {
            $targetDir = "uploads/profile_pics";
            $targetFile = $targetDir . basename($profile_pic);

            // Hapus foto profil lama jika ada
            if ($currentPic && file_exists($targetDir . $currentPic)) {
                unlink($targetDir . $currentPic);
            }

            // Simpan file foto profil baru
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile);

            // Update nama file di database
            $stmt = $conn->prepare("UPDATE akun SET profile_pic = ? WHERE userID = ?");
            $stmt->bind_param("si", $profile_pic, $userID);
            $stmt->execute();
        }

        $message = "Profil berhasil diperbarui!";
        header('refresh: 3;Location: MProfile.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <style>
        /* Mengimpor font dan pengaturan dasar */
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
        /* Gaya untuk container profil */
        .profile-container {
            width: 100%;
            max-width: 600px;
            background-color: #111;
            border: 1px solid #fff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        h2 {
            font-size: 24px;
            color: #4dbf00;
            margin-bottom: 20px;
        }
        .profile-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            color: #ccc;
            margin-top: 10px;
            text-align: left;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #4dbf00;
            background-color: #222;
            color: #fff;
        }
        input[type="file"] {
            margin-top: 10px;
            color: #ccc;
        }
        button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #4dbf00;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #3e9d00;
        }
        /* Gaya tombol kembali ke halaman sebelumnya */
        a.left-button {
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
        a.left-button:hover {
            background-color: #3e9d00;
        }
        .message {
            margin-bottom: 15px;
            color: #4dbf00;
        }
        /* Gaya custom untuk upload file */
        .customfile {
            border: 5px solid #4dbf00;
            border-radius: 50px;
            padding: 2px;
            font-size: 20px;
        }
        .customfile::-webkit-file-upload-button {
            background: #4dbf00;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<a href="user_dash.php" class="left-button">Kembali</a>

<div class="profile-container">
    <h2>Profil Saya</h2>
    
    <!-- Menampilkan pesan jika ada -->
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- Form untuk memperbarui profil -->
    <form action="MProfile.php" method="post" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="newPassword">Password Baru:</label>
        <input type="password" name="newPassword">

        <label for="profile_pic">Foto Profil:</label><br>
        <?php if ($user['profile_pic']): ?>
            <img src="uploads/profile_pics<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Foto Profil"><br>
        <?php endif; ?>
        <input type="file" class="customfile" name="profile_pic"><br><br>

        <button type="submit">Perbarui Profil</button>
    </form>
</div>

</body>
</html>
