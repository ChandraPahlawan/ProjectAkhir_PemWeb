<?php
// Menghubungkan ke file koneksi database
include 'db.php';
// Memulai sesi untuk mengakses data sesi pengguna
session_start();

// Cek apakah pengguna sudah login dan memiliki role "user"
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // Jika tidak memiliki role "user", tampilkan pesan dan hentikan proses
    echo "Akses ditolak. Halaman ini hanya untuk pengguna.";
    exit();
}

// Ambil data pengguna yang sedang login
$userID = $_SESSION['userID'];
// Query untuk mengambil foto profil pengguna berdasarkan userID
$query = "SELECT profile_pic FROM akun WHERE userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Jika pengguna tidak memiliki foto profil, gunakan gambar default
$profile_pic = !empty($user['profile_pic']) ? "uploads/profile_pics" . htmlspecialchars($user['profile_pic']) : "uploads/profil.png";

// Ambil data film dari database untuk ditampilkan
$query = "SELECT * FROM film";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link ke stylesheet untuk styling -->
    <link rel="stylesheet" href="style.css">
    <!-- Link ke Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <!-- Link ke font awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <title>CinePicks</title>
</head>

<body>
<!-- Style untuk berbagai elemen pada halaman -->
<style>
  /* Gaya untuk section umum */
  .section {
        padding: 50px 20px;
        max-width: 900px;
        margin: auto;
    }

    /* Gaya untuk judul section */
    .section h2 {
        font-size: 2.5rem;
        color: #4dbf00;
        text-align: center;
    }

    /* Gaya untuk kotak informasi tentang kami */
    .about-us, .about-website {
        background-color: #1d1d1d;
        border-radius: 10px;
        padding: 30px;
        margin-top: 30px;
        text-align: center;
        color: #ffffff;
    }

    .team-member img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        margin-bottom: 15px;
        border: 2px solid #4dbf00;
    }
    

    /* Responsivitas untuk tampilan lebih kecil */
    @media (max-width: 768px) {
        .section h2 { font-size: 2rem; }
        .about-us, .about-website { padding: 20px; margin: 10px 15px; }
        .team-member img { width: 120px; height: 120px; }
    }

    /* Gaya untuk elemen tertentu seperti menu */
    .menu-list-item a {
        color: white;
        text-decoration: none !important;
        padding: 10px 20px;
        font-weight: bold;
    }

    /* Efek hover */
    .menu-list-item a:hover {
        color: #ffcc00;
        transition: color 0.3s ease;
    }

    /* Style untuk tombol menu yang aktif */
    .menu-list-item.active a { color: #ffffff; }
</style>

<!-- Bagian Navbar Desktop -->
<div class="navbar-desktop">
    <div class="logo-container">
        <h1 class="logo">CinePicks</h1>
    </div>
    <div class="menu-container">
        <ul class="menu-list">
            <li class="menu-list-item"><a href="#home">Home</a></li>
            <li class="menu-list-item"><a href="allmovies.php">All Movies</a></li>
            <li class="menu-list-item"><a href="#about">About Us</a></li>
        </ul>
    </div>
    <div class="search-container">
        <form method="GET" action="search.php">
            <input type="text" name="query" placeholder="Search..." aria-label="Search">
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="profile-container">
        <!-- Gambar profil pengguna -->
        <img class="profile-picture" src="<?php echo $profile_pic; ?>" alt="Profile Picture">
        <div class="profile-text-container">
            <a href="MProfile.php" class="profile-text-link">
                <span class="profile-text">Profile</span>
            </a>
        </div>
    </div>
    <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/logout.php';">Logout</button></li>
</div>

<!-- Bagian Navbar Mobile -->
<div class="navbar-mobile">
    <div class="logo-container">
        <h1 class="logo">CinePicks</h1>
        <div class="hamburger" onclick="toggleMobileMenu()">☰</div>
    </div>
    <div id="mobileMenu" class="menu-container-mobile">
        <ul class="menu-list-mobile">
            <li class="menu-list-item"><a href="#home">Home</a></li>
            <li class="menu-list-item"><a href="allmovies.php">All Movies</a></li>
            <li class="menu-list-item"><a href="#about">About Us</a></li>
            <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/logout.php';">Logout</button></li>
        </ul>
        <div class="search-container-mobile">
            <form method="GET" action="search.php">
                <input type="text" name="query" placeholder="Search..." aria-label="Search">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="profile-container">
            <img class="profile-picture" src="<?php echo $profile_pic; ?>" alt="Profile Picture">
            <div class="profile-text-container">
                <a href="MProfile.php" class="profile-text-link">
                    <span class="profile-text">Profile</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Bagian konten film baru -->
    <div class="content-container">
        <div class="featured-content" style="background: linear-gradient(to bottom, rgba(0,0,0,0), #151515), url('img/f-1.jpg');"></div>
        <div class="movie-list-container">
            <h1 class="movie-list-title">FILM RELEASES</h1>
            <div class="movie-list-wrapper">
                <i class="fas fa-chevron-left arrow left-arrow" onclick="scrollLeftList(this)"></i>
                <div class="movie-list">
                    <?php while ($film = mysqli_fetch_assoc($result)): ?>
                        <div class="movie-list-item">
                            <a href="film.php?filmID=<?php echo $film['filmID']; ?>">
                                <img class="movie-list-item-img" src="admin/MFilm/uploads/<?php echo htmlspecialchars($film['upPic']); ?>" alt="Movie Poster">
                            </a>
                            <span class="movie-list-item-title"><?php echo htmlspecialchars($film['title']); ?></span>
                            <a href="film.php?filmID=<?php echo $film['filmID']; ?>">
                                <button class="movie-list-item-button">Watch</button>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
                <i class="fas fa-chevron-right arrow right-arrow" onclick="scrollRightList(this)"></i>
            </div>
        </div>
    </div>

    <!-- Tentang Kami -->
    <div id="about" class="section about-us">
        <h2>About Us</h2>
        <div class="team-container">
            <div class="team-member">
                <img src="img/us.jpg" alt="Member team">
                <div class="team-member-info">
                    <h3>
                    We are a passionate team of movie enthusiasts who are dedicated to bringing you the best movie recommendations, reviews, and insights. Our mission is to create a community where movie lovers can connect, share their opinions, and discover new films from all around the world.
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tentang Website -->
    <div class="section about-website">
        <h2>About the Website</h2>
        <p>CinePicks is a platform designed to help you discover movies that match your taste. With an easy-to-navigate interface, we provide personalized movie recommendations, detailed reviews, and a vibrant community of movie enthusiasts. Whether you’re looking for the latest blockbuster or a hidden indie gem, CinePicks has something for everyone. Join us in exploring the vast world of cinema!</p>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 CinePicks. All rights reserved.</p>
    </footer>
</div>

<!-- Script JavaScript untuk mengelola menu mobile -->
<script src="scr.js">
</script>
</body>
</html>
