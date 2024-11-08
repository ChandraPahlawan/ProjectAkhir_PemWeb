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


if (isset($_POST['addFilm'])) {
    // Ambil data dari form
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $releaseYear = $_POST['releaseYear'];
    $description = $_POST['description'];
    $videoURL = $_POST['videoURL'];

    // Cek apakah file diunggah
    if (isset($_FILES['upPic']) && $_FILES['upPic']['error'] === UPLOAD_ERR_OK) {
        $upPic = $_FILES['upPic']['name'];
        $target = "uploads/" . basename($upPic);

        // Pindahkan file yang diunggah ke folder tujuan
        if (move_uploaded_file($_FILES['upPic']['tmp_name'], $target)) {
            // Insert data ke dalam database
            $query = "INSERT INTO film (title, genre, releaseYear, description, upPic, videoURL) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssisss", $title, $genre, $releaseYear, $description, $upPic, $videoURL);
            
            if ($stmt->execute()) {
                header("Location: list.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Gagal mengunggah gambar.";
        }
    } else {
        echo "Gambar tidak diunggah atau terjadi kesalahan.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Film</title>
    <style>
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
            color: #fff;
        }
        .form-card { 
            width: 100%; 
            max-width: 600px; 
            padding: 50px; 
            background: #000; 
            border: 1px solid #fff; 
            border-radius: 20px; 
        }
        h2.form-title { 
            font-size: 30px; 
            color: #fff; 
            text-align: center; 
            margin-bottom: 30px;
        }
        .input-group {
            color: #fff;
            position: relative; 
            margin-bottom: 25px; 
            border-bottom: 2px solid #fff;
        }
        .input-group label { 
            position: absolute; 
            top: 50%; 
            left: 10px; 
            transform: translateY(-50%); 
            font-size: 16px; 
            color: #fff; 
            pointer-events: none; 
            transition: 0.5s; 
        }
        .input-group input, 
        .input-group textarea { 
            width: 100%; 
            padding: 10px 10px 10px 5px; 
            font-size: 16px; 
            color: #fff; 
            background: transparent; 
            border: none; 
            outline: none; 
        }
        .input-group input:focus ~ label, 
        .input-group input:valid ~ label,
        .input-group textarea:focus ~ label,
        .input-group textarea:valid ~ label {
            top: -10px;
            font-size: 14px;
            color: #4dbf00;
        }
        .input-group input[type="file"] {
            opacity: 0;
            position: absolute;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .genre-container {
            margin-top: 20px;
            text-align: center;
        }
        .genre-container label {
            font-size: 18px;
            color: #4dbf00;
            font-weight: 600;
        }
        #genre {
            width: 100%;
            padding: 10px;
            background-color: #111;
            color: #fff;
            border: 1px solid #4dbf00;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 10px;
        }
        #genre:focus {
            outline: none;
            box-shadow: 0 0 5px #4dbf00;
        }
        .custom-file-label {
            display: inline-block;
            background-color: #4dbf00;
            color: #000;
            padding: 10px 20px;
            border-radius: 60px;
            font-size: 16px;
            cursor: pointer;
        }
        #file-chosen {
            color: #fff;
            font-size: 16px;
            margin-left: 30px;
            margin-bottom: 80px;
            display: inline-block;
        }
        button { 
            width: 100%; 
            height: 45px; 
            background: #4dbf00; 
            box-shadow: 0 0 10px #4dbf00; 
            font-size: 16px; 
            color: #000; 
            border-radius: 30px; 
            border: none; 
            cursor: pointer; 
            margin-top: 20px;
        }
        button:hover {
            background-color: #3e9d00;
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
    <a href="../admin_dash.php" class="left-button">Kembali</a>
    <div class="form-card">
        <h2 class="form-title">Tambah Film</h2>
        <form action="add.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <input type="text" id="title" name="title" required>
                <label for="title">Title</label>
            </div>
            <div class="genre-container">
                <label for="genre">Genre:</label><br>
                <select name="genre" id="genre" required>
                    <option value="Romance">Romance</option>
                    <option value="Action">Action</option>
                    <option value="Comedy">Comedy</option>
                    <option value="Horror">Horror</option>
                    <option value="Family">Family</option>
                    <option value="etc">etc...</option>
                </select>
            </div><br>
            <div class="input-group">
                <input type="number" id="releaseYear" name="releaseYear" value="<?php echo htmlspecialchars($film['releaseYear']); ?>" required>
                <label for="releaseYear">Release Year</label>
            </div>
            <div class="input-group">
                <textarea id="description" name="description" rows="4"></textarea>
                <label for="description">Description</label>
            </div>
            <div class="input-group">
                <input type="text" id="videoURL" name="videoURL" required>
                <label for="videoURL">Video URL</label>
            </div>
            <div class="input-group">
                <label for="upPic" class="custom-file-label">Upload Thumbnail</label>
                <input type="file" id="upPic" name="upPic" class="custom-file-input" required>
                <span id="file-chosen">No file chosen</span>
            </div>
            <button type="submit" name="addFilm">Add Film</button>
        </form>
        <div class="footer">
            &copy; 2024 CinePicks. All Rights Reserved.
        </div>
    </div>

    <script>
        document.getElementById('upPic').addEventListener('change', function() {
            const fileName = this.files.length > 0 ? this.files[0].name : "No file chosen";
            document.getElementById('file-chosen').textContent = fileName;
        });
    </script>
</body>
</html>
