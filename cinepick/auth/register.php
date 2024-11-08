<?php
// Memulai sesi dan menghubungkan ke database
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = 'user';

    // Validasi panjang password minimal 8 karakter
    if (strlen($password) < 8) {
        $message = "Password harus minimal 8 karakter.";
        $messageClass = "error";
    } else {
        // Hash password setelah pengecekan panjang
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Cek apakah username sudah digunakan
        $check_user = $conn->prepare("SELECT * FROM akun WHERE username = ?");
        $check_user->bind_param("s", $username);
        $check_user->execute();
        $result_user = $check_user->get_result();

        // Cek apakah email sudah digunakan
        $check_email = $conn->prepare("SELECT * FROM akun WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $result_email = $check_email->get_result();

        // Validasi jika username atau email sudah terdaftar
        if ($result_user->num_rows > 0) {
            $message = "Username sudah digunakan. Silakan pilih username lain.";
            $messageClass = "error";
        } elseif ($result_email->num_rows > 0) {
            $message = "Email sudah digunakan. Silakan gunakan email lain.";
            $messageClass = "error";
        } else {
            // Insert user baru jika username dan email belum terdaftar
            $stmt = $conn->prepare("INSERT INTO akun (username, password, email, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $hashed_password, $email, $role);
            if ($stmt->execute()) {
                header("Location: login.php"); 
                exit();
            } else {
                $message = "Registrasi gagal!";
                $messageClass = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <style>
        /* Pengaturan font dari Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        
        /* Reset dan pengaturan default */
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

        /* Pengaturan tampilan form registrasi */
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

        /* Input field styling */
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

        .input-group input:focus~label,
        .input-group input:valid~label {
            top: -5px;
        }

        /* Checkbox dan button styling */
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

        .signIn-link a {
            color: #4dbf00;
        }
        .signIn-link p {
            color: #fff;
        }

        /* Pesan untuk validasi */
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

        /* Tombol kembali */
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

        /* Footer styling */
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Tombol kembali ke halaman index -->
    <a href="../index.php" class="left-button">Kembali</a>

    <div class="wrapper">
        <!-- Menampilkan pesan validasi -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>  

        <!-- Form registrasi -->
        <form action="" method="POST">
            <h2>Sign Up</h2>

            <div class="input-group">
                <input type="text" name="username" required>
                <label for="username">Username</label>
            </div>

            <div class="input-group">
                <input type="email" name="email" required>
                <label for="email">Email</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" required>
                <label for="password">Password</label>
            </div>

            <div class="remember">
                <label><input type="checkbox" required> Saya menyetujui syarat & ketentuan</label>
            </div>

            <button type="submit" name="register">Sign Up</button>

            <div class="signIn-link">
                <p>Sudah memiliki akun? <a href="login.php">Login</a></p>
            </div>
        </form>

        <!-- Footer -->
        <div class="footer">
            &copy; 2024 CinePicks. All Rights Reserved.
        </div>
    </div>
</body>
</html>
