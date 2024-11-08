<?php
include 'db.php'; // Pastikan Anda sudah menghubungkan ke database
session_start();

// Cek role user, jika admin maka akses ditolak dan sesi diakhiri
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    echo "Akses ditolak. Halaman ini tidak tersedia untuk admin.";
    session_destroy();
    exit();
}

// Ambil path foto profil dari database berdasarkan userID yang ada dalam sesi
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
    $stmt = $conn->prepare("SELECT profile_pic FROM akun WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Tentukan path default untuk foto profil jika tidak ada
    $profile_pic = !empty($user['profile_pic']) ? "uploads/profile_pics" . htmlspecialchars($user['profile_pic']) : "uploads/profil.png";
}

// Cek apakah ada data pencarian yang dimasukkan
$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';

// Buat kueri SQL untuk pencarian berdasarkan title, genre, atau releaseYear
$query = "SELECT * FROM film WHERE title LIKE ? OR genre LIKE ? OR releaseYear LIKE ?";
$stmt = $conn->prepare($query);
$searchTerm = "%" . $searchQuery . "%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <title>Hasil Pencarian</title>
    <link rel="stylesheet" href="style.css" class="rel">

    <!-- Style tambahan untuk elemen khusus -->
    <style>
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .section h2 {
                font-size: 2rem;
            }
            .team-member img {
                width: 120px;
                height: 120px;
            }
            .about-us, .about-website {
                padding: 20px;
                margin: 10px 15px;
            }
            .search-container-mobile {
                display: flex;
                justify-content: center;
                margin-top: 10px;
            }
        }

        @media (max-width: 480px) {
            .section h2 {
                font-size: 1.8rem;
            }
            .about-us, .about-website {
                padding: 20px;
                font-size: 0.9rem;
                margin: 10px 10px;
            }
            .about-us p, .about-website p {
                font-size: 0.9rem;
                text-align: left;
            }
            .team-member-info h3 {
                font-size: 1rem;
            }
            .group-info {
                font-size: 0.9rem;
                text-align: center;
            }
        }

        /* Menu styling */
        .menu-list-item a {
            color: white; 
            text-decoration: none !important;
            padding: 10px 20px;
            font-weight: bold;
            display: block;
        }

        .menu-list-item a:hover {
            color: #ffcc00; 
            transition: color 0.3s ease;
        }

        .menu-list-item.active a {
            color: #ffffff; 
        }

        /* Movie List Styling */
        .movie-list-container {
            padding: 50px;
            text-align: center;
            z-index: 1;
        }

        .movie-list-title {
            font-size: 2em;
            color: #4dbf00;
            margin-bottom: 20px;
        }

        .movie-list-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 30px; /* Memberikan jarak antara kotak film */
            justify-content: center;
        }

        .movie-list-item {
            background-color: #222;
            width: 250px;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s;
        }

        .movie-list-item:hover {
            transform: scale(1.05);
        }

        .movie-list-item-img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .movie-list-item-title {
            display: block;
            font-size: 1.3em;
            margin: 10px 0;
            font-weight: bold;
        }

        .movie-list-item-genre,
        .movie-list-item-year,
        .movie-list-item-desc {
            font-size: 0.95em;
            margin: 8px 0;
            color: #bbb;
        }

        .movie-list-item-button {
            margin-top: 15px;
            background-color: #4dbf00;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .movie-list-item-button:hover {
            background-color: #3a9400;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .navbar-container {
                padding: 0 20px;
            }
            .movie-list-wrapper {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Desktop Navbar -->
    <div class="navbar-desktop">
        <div class="logo-container">
            <h1 class="logo">CinePicks</h1>
        </div>
        <div class="menu-container">
            <ul class="menu-list">
                <li class="menu-list-item"><a href="<?php echo isset($_SESSION['userID']) ? 'user_dash.php' : 'index.php'; ?>">Home</a></li>
                <li class="menu-list-item"><a href="allmovies.php">All Movies</a></li>
                <li class="menu-list-item"><a href="<?php echo isset($_SESSION['userID']) ? 'user_dash.php' : 'index.php'; ?>#about">About Us</a></li>
            </ul>
        </div>
        <div class="search-container">
            <form method="GET" action="search.php">
                <input type="text" name="query" placeholder="Search..." aria-label="Search">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="profile-container">
            <?php if (isset($_SESSION['userID'])): ?>
                <img class="profile-picture" src="<?php echo $profile_pic; ?>" alt="Profile Picture">
                <div class="profile-text-container">
                    <a href="MProfile.php" class="profile-text-link">
                        <span class="profile-text">Profile</span>
                    </a>
                </div>
                <div class="toggle-login">
                    <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='logout.php';">Logout</button></li>
                </div>
            <?php else: ?>
                <div class="toggle-login">
                    <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='login.php';">Login</button></li>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Navbar -->
    <div class="navbar-mobile">
        <div class="logo-container">
            <h1 class="logo">CinePicks</h1>
            <div class="hamburger" onclick="toggleMobileMenu()">☰</div>
        </div>
        <div id="mobileMenu" class="menu-container-mobile">
            <ul class="menu-list-mobile">
                <li class="menu-list-item"><a href="<?php echo isset($_SESSION['userID']) ? 'user_dash.php' : 'index.php'; ?>">Home</a></li>
                <li class="menu-list-item"><a href="<?php echo isset($_SESSION['userID']) ? 'user_dash.php' : 'index.php'; ?>#about">About Us</a></li>
                <li class="menu-list-item"><a href="allmovies.php">All Movies</a></li>
                <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/logout.php';">Logout</button></li>
            </ul>
            <div class="search-container-mobile">
                <form method="GET" action="search.php">
                    <input type="text" name="query" placeholder="Search..." aria-label="Search">
                    <button type="submit">Search</button>
                </form>
            </div>
            <div class="profile-container">
                <?php if (isset($_SESSION['userID'])): ?>
                    <img class="profile-picture" src="<?php echo $profile_pic; ?>" alt="Profile Picture">
                    <div class="profile-text-container">
                        <a href="MProfile.php" class="profile-text-link">
                            <span class="profile-text">Profile</span>
                        </a>
                    </div>
                    <div class="toggle-login">
                        <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/logout.php';">Logout</button></li>
                    </div>
                <?php else: ?>
                    <div class="toggle-login">
                        <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/login.php';">Login</button></li>
                    </div>
                <?php endif; ?>
            </div>    
        </div>
    </div>

    <!-- Daftar Film -->
    <div class="movie-list-container">
        <h2 class="movie-list-title">Daftar Film</h2>
        <div class="movie-list-wrapper">
            <?php while ($film = $result->fetch_assoc()): ?>
                <div class="movie-list-item">
                    <img src="admin/MFilm/uploads/<?php echo htmlspecialchars($film['upPic']); ?>" alt="Poster <?php echo htmlspecialchars($film['title']); ?>" class="movie-list-item-img">
                    <span class="movie-list-item-title"><?php echo htmlspecialchars($film['title']); ?></span>
                    <span class="movie-list-item-genre">Genre: <?php echo htmlspecialchars($film['genre']); ?></span>
                    <span class="movie-list-item-year">Release Year: <?php echo htmlspecialchars($film['releaseYear']); ?></span>
                    <span class="movie-list-item-desc"><?php echo htmlspecialchars($film['description']); ?></span>
                    <a href="film.php?filmID=<?php echo htmlspecialchars($film['filmID']); ?>" class="movie-list-item-button">Lihat Detail</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <script src="scr.js"></script>
</body>
</html>
