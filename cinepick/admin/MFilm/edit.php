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
    $query = "SELECT * FROM film WHERE filmID = $filmID";
    $result = mysqli_query($conn, $query);
    $film = mysqli_fetch_assoc($result);
}

if (isset($_POST['updateFilm'])) {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $releaseYear = $_POST['releaseYear'];
    $description = $_POST['description'];
    $videoURL = $_POST['videoURL'];
    $newPic = $_FILES['upPic']['name'];
    $oldPic = $film['upPic'];

    // Check if a new picture is uploaded
    if ($newPic) {
        $target = "uploads/" . basename($newPic);

        if ($oldPic && file_exists("uploads/" . $oldPic)) {
            unlink("uploads/" . $oldPic);
        }

        move_uploaded_file($_FILES['upPic']['tmp_name'], $target);
    } else {
        $newPic = $oldPic;
    }

    $query = "UPDATE film SET title='$title', genre='$genre', releaseYear='$releaseYear', description='$description', upPic='$newPic', videoURL='$videoURL' WHERE filmID=$filmID";
    mysqli_query($conn, $query);
    
    header("Location: list.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Film</title>
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
            padding: 40px; 
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
        .custom-file-label {
            display: inline-block;
            background-color: #4dbf00;
            color: #000;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        #file-chosen {
            color: #fff;
            font-size: 16px;
            margin-left: 15px;
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
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }
        .current-image {
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-card">
        <h2 class="form-title">Edit Film</h2>
        <form action="edit.php?id=<?php echo $filmID; ?>" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($film['title']); ?>" required>
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
                <input type="number" id="releaseYear" name="releaseYear" value="<?php echo htmlspecialchars($film['releaseYear']); ?>" required>
                <label for="releaseYear">Release Year</label>
            </div>
            <div class="input-group">
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($film['description']); ?></textarea>
                <label for="description">Description</label>
            </div>
            <div class="input-group">
                <input type="text" id="videoURL" name="videoURL" value="<?php echo htmlspecialchars($film['videoURL']); ?>" required>
                <label for="videoURL">Video URL</label>
            </div>
            <div class="input-group">
                <label for="upPic" class="custom-file-label">Upload New Picture</label>
                <input type="file" id="upPic" name="upPic" class="custom-file-input">
                <span id="file-chosen"><?php echo $film['upPic'] ? $film['upPic'] : "No file chosen"; ?></span>
            </div>
            <?php if ($film['upPic']): ?>
                <div class="current-image">
                    <img src="uploads/<?php echo $film['upPic']; ?>" alt="Current Picture" width="100%">
                </div>
            <?php endif; ?>
            <button type="submit" name="updateFilm">Update Film</button>
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
