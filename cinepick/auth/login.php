<?php
include '../db.php';
session_start(); // Memulai sesi di bagian paling atas

$message = '';

// Cek apakah form dikirimkan melalui metode POST dan tombol login diklik
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the statement untuk mengambil data user berdasarkan username atau email
    $stmt = $conn->prepare("SELECT * FROM akun WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifikasi apakah user ditemukan
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $storedHash = $user['password'];

        // Verifikasi password
        if (password_verify($password, $storedHash)) {
            // Simpan data sesi
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect berdasarkan role user
            if ($user['role'] == 'admin') {
                header('Location: ../admin/admin_dash.php');
            } else {
                header('Location: ../user_dash.php');
            }
            exit;
        } else {
            $message = "Password salah!";
            $messageClass = "error";
        }
    } else {
        $message = "Username atau email tidak ditemukan!";
        $messageClass = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login CinePicks</title>
    <style>
        /* Mengatur font utama */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #000;
        }
        .wrapper {
            width: 400px;
            padding: 40px;
            background: #000;
            border: 1px solid #fff;
            border-radius: 20px;
        }
        h2 {
            font-size: 30px;
            color: #fff;
            text-align: center;
        }
        .input-group {
            position: relative;
            margin: 30px 0;
            border-bottom: 2px solid #fff;
        }
        .input-group label {
            position: absolute;
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
            font-size: 16px;
            color: #fff;
            pointer-events: none;
            transition: .5s;
        }
        .input-group input {
            width: 100%;
            height: 40px;
            font-size: 16px;
            color: #fff;
            background: transparent;
            border: none;
            outline: none;
        }
        .input-group input:focus ~ label, .input-group input:valid ~ label {
            top: -5px;
        }
        .remember {
            color: #fff;
            margin-bottom: 10px;
            font-size: 14px;
        }
        button {
            width: 100%;
            height: 40px;
            background: #4dbf00;
            box-shadow: 0 0 10px #4dbf00;
            font-size: 16px;
            color: #000;
            border-radius: 30px;
            border: none;
            cursor: pointer;
        }
        .signUp-link p a {
            color: #4dbf00;
        }
        p {
            margin-top: 20px;
            color: #fff;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>
<a href="../index.php" class="left-button">Kembali</a>

<div class="wrapper">
    <!-- Menampilkan pesan jika ada -->
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $messageClass; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Form login -->
    <form action="" method="POST">
        <h2>Login</h2>
        <div class="input-group">
            <input type="text" name="username" required>
            <label for="username">Username atau Email</label>
        </div>
        <div class="input-group">
            <input type="password" name="password" required>
            <label for="password">Password</label>
        </div>
        <button type="submit" name="login">Login</button>
        <div class="signUp-link">
            <p>Belum memiliki akun? <a href="register.php">Sign Up</a></p>
        </div>
    </form>

    <!-- Footer -->
    <div class="footer">
        &copy; 2024 CinePicks. All Rights Reserved.
    </div>
</div>
</body>
</html>
