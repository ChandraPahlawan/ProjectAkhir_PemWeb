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

$genres = [
    1 => "Romance",
    2 => "Action",
    3 => "Comedy",
    4 => "Horror",
    5 => "Family",
    6 => "etc..."
];

// Jika ada permintaan untuk menghapus film
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $filmID = intval($_GET['delete']);

    // Hapus semua review yang terkait dengan film ini terlebih dahulu
    $deleteReviewsQuery = "DELETE FROM review WHERE filmID = $filmID";
    mysqli_query($conn, $deleteReviewsQuery);

    // Hapus film dari database
    $deleteFilmQuery = "DELETE FROM film WHERE filmID = $filmID";
    if (mysqli_query($conn, $deleteFilmQuery)) {
        echo "<script>alert('Film dan review yang terkait berhasil dihapus!'); window.location.href='list.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus film.'); window.location.href='list.php';</script>";
    }
}

// Ambil semua film dari database
$query = "SELECT * FROM film";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film List</title>
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
        .film-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* 5 kolom */
            gap: 20px;
            width: 100%;
            max-width: 1200px;
        }
        .film-card {
            background-color: #111;
            border: 1px solid #fff;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }
        .film-card img {
            width: 100%;
            height: auto;
            max-height: 150px; /* Mengatur tinggi maksimal gambar */
            border-radius: 5px;
        }
        .film-card h3 {
            font-size: 18px;
            color: #4dbf00;
            margin: 10px 0;
        }
        .film-card p {
            color: #ccc;
            margin: 5px 0;
        }
        .film-card a {
            display: inline-block;
            margin: 10px 5px 0 0;
            padding: 5px 15px;
            background-color: #4dbf00;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        .film-card a:hover {
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
    </style>
</head>
<body>
    <a href="../admin_dash.php" class="left-button">Kembali</a>
    <h1>Daftar Film</h1>
    <div class="film-grid">
        <?php while ($film = mysqli_fetch_assoc($result)) { ?>
            <div class="film-card">
                <img src="uploads/<?php echo htmlspecialchars($film['upPic']); ?>" alt="<?php echo htmlspecialchars($film['title']); ?>">
                <h3><?php echo htmlspecialchars($film['title']); ?></h3>
                <p>Genre: <?php echo htmlspecialchars($genres[$film['genre']] ?? 'Unknown'); ?></p>
                <p>Release Year: <?php echo htmlspecialchars($film['releaseYear']); ?></p>
                <p>
                Description: 
                <?php
                $description = htmlspecialchars($film['description']);
                if (strlen($description) > 100) {
                    // Show only first 200 characters initially
                    echo substr($description, 0, 100) . "... ";
                    // echo "<button class='read-more' onclick='showFullDescription(this)'>Read More</button>";
                    // Hidden full description
                    echo "<span class='full-description' style='display: none;'>" . $description . "</span>";
                } else {
                    // If under 200 characters, show full description
                    echo $description;
                }
                ?>
            </p>                
                <a href="edit.php?id=<?php echo $film['filmID']; ?>">Edit</a>
                <a href="list.php?delete=<?php echo $film['filmID']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus film ini beserta review yang terkait?')">Delete</a>
                <br>
                <a href="review.php?filmID=<?php echo $film['filmID']; ?>">Lihat Review</a> <!-- Tautan untuk melihat review film ini -->
            </div>
        <?php } ?>
    </div>
</body>
<!-- <script>
    function showFullDescription(button) {
        const fullDescription = button.nextElementSibling;
        if (fullDescription.style.display === 'none') {
            fullDescription.style.display = 'inline';
            button.style.display = 'none';
        }
    }
</script> -->
</html>
