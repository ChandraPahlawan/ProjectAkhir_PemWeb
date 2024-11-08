<?php
include 'db.php'; // Pastikan Anda sudah menghubungkan ke database
session_start();


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
    
    $profile_pic = !empty($user['profile_pic']) ? "uploads/profile_pics" . htmlspecialchars($user['profile_pic']) : "uploads/profil.png";

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Movies</title>
    <link rel="stylesheet" href="style.css" class="">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Roboto", sans-serif;
            background-color: #1a1a1a;
            color: #4dbf00;
        }
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

        .team-member {
            padding: 20px;
        }

        .team-member-info h3 {
            font-size: 1rem;
        }

        .team-member-info p {
            font-size: 0.9rem;
        }

        .group-info {
            font-size: 0.9rem;
            text-align: center;
        }
        .search-container-mobile {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }
    }
        .movie-list-container {
            margin: 20px 0px;
            position: relative;
            overflow: hidden;
        }
        .movie-list-title {
            margin-bottom: 10px;
            font-size: 24px;
            padding: 0 20px;
        }

        .movie-list-wrapper {
            display: flex;
            overflow-x: scroll;
            scroll-behavior: smooth;
            padding: 20px;
            gap: 20px;
            background: linear-gradient(135deg, #1a1a1a 30%, #333 100%);
            border-radius: 15px;
        }

        .arrow {
            font-size: 100px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: lightgray;
            opacity: 3;
            cursor: pointer;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            padding: 30px;
            z-index: 10; /* Memastikan panah berada di atas konten lainnya */
            transition: opacity 3s ease;
        }

        .arrow:hover {
            opacity: 1;
        }

        .left-arrow {
            left: 0;
        }

        .right-arrow {
            right: 0;
        }

        .movie-list {
            display: flex;
            align-items: center;
            height: 300px;
            overflow-x: hidden;
            scroll-behavior: smooth;
            padding-bottom: 10px; /* Tambahkan padding bawah untuk ruang scroll */
        }

        .movie-list-item {
            width: 270px;
            flex: 0 0 auto; 
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;/* Menjaga ukuran tetap agar tidak menyusut */
        }

        .movie-list-item:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 10px 20px rgba(255, 75, 92, 0.3);
        }

        .movie-list-item:hover .movie-list-item-img {
            transform: scale(1.1);
        }

        .movie-list-item:hover .movie-list-item-title,
        .movie-list-item:hover .movie-list-item-desc,
        .movie-list-item:hover .movie-list-item-button {
            opacity: 1;
        }

        .movie-list-item-title,
        .movie-list-item-desc {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px;
            position: absolute;
            left: 10px;
            border-radius: 10px;
            transition: opacity 0.5s ease-in-out;
            opacity: 0;
        }

        .movie-list-item-img {
            transition: transform 0.5s ease-in-out;
            width: 270px;
            height: 200px;
            object-fit: cover;
            border-radius: 20px;
            transition: transform 0.4s ease-in-out;
        }

        .movie-list-item-title {
            position: absolute;
            bottom: 20px;
            left: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #ff4b5c;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
            background: rgba(0, 0, 0, 0.6);
            padding: 5px 10px;
            border-radius: 8px;
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
        }

        .movie-list-item-desc {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 14px;
            color: #eaeaea;
            background: rgba(0, 0, 0, 0.6);
            padding: 5px 10px;
            border-radius: 8px;
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
        }
        .movie-list-item:hover .movie-list-item-title,
        .movie-list-item:hover .movie-list-item-desc {
            opacity: 1;
        }

        .movie-list-wrapper::-webkit-scrollbar {
  display: none; /* Hide scrollbar for Chrome, Safari, and Opera */
}

.movie-list-wrapper {
  -ms-overflow-style: none; /* Hide scrollbar for IE and Edge */
  scrollbar-width: none; /* Hide scrollbar for Firefox */
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
                    <li class="menu-list-item"><a href="<?php echo isset($_SESSION['userID']) ? 'user_dash.php' : 'index.php'; ?>#about">About Us</a></li>
                    <li class="menu-list-item"><a href="allmovies.php">All Movies</a></li>
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
                    <!-- Tampilkan gambar profil pengguna -->
                    <img class="profile-picture" src="<?php echo $profile_pic; ?>" alt="Profile Picture">
                    <div class="profile-text-container">
                        <!-- Link ke halaman pengaturan profil -->
                        <a href="MProfile.php" class="profile-text-link">
                            <span class="profile-text">Profile</span>
                        </a>
                    </div>
                    <div class="toggle-login">
                        <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/logout.php';">Logout</button></li>
                    </div>
                <?php else: ?>
                    <!-- User not logged in: show Login button only -->
                    <div class="toggle-login">
                        <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/login.php';">Login</button></li>
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
                    <!-- Tampilkan gambar profil pengguna -->
                    <img class="profile-picture" src="<?php echo $profile_pic; ?>" alt="Profile Picture">
                    <div class="profile-text-container">
                        <!-- Link ke halaman pengaturan profil -->
                        <a href="MProfile.php" class="profile-text-link">
                            <span class="profile-text">Profile</span>
                        </a>
                    </div>
                    <div class="toggle-login">
                        <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/logout.php';">Logout</button></li>
                    </div>
                <?php else: ?>
                    <!-- User not logged in: show Login button only -->
                    <div class="toggle-login">
                        <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/login.php';">Login</button></li>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="movie-list-container">
        <h1 class="movie-list-title">ROMANCE</h1>
        <div class="movie-list-wrapper">
            <i class="fas fa-chevron-left arrow left-arrow" onclick="scrollLeftList(this)"></i>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/r1.jpg" alt=""><br>
                <p class="movie-list-item-desc">Habibie & Ainun</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/r2.jpg" alt=""><br>
                <p class="movie-list-item-desc">Nikah Yuk</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/r3.jpg" alt=""><br>
                <p class="movie-list-item-desc"> Film Milea: Suara Dari Dilan</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/r4.jpg" alt=""><br>
                <p class="movie-list-item-desc">Toko Barang Mantan</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/r5.jpg" alt=""><br>
                <p class="movie-list-item-desc">Teman Tapi Menikah</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/r6.jpg" alt=""><br>
                <p class="movie-list-item-desc">Cinta Dalam Ikhlas</p>
            </div>
        </div>
        <i class="fas fa-chevron-right arrow right-arrow" onclick="scrollRightList(this)"></i>
    </div>
    <div class="movie-list-container">
        <h1 class="movie-list-title">HORROR</h1>
        <div class="movie-list-wrapper">
            <i class="fas fa-chevron-left arrow left-arrow" onclick="scrollLeftList(this)"></i>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/h1.jpg" alt=""><br>
                <p class="movie-list-item-desc">Pemukiman Setan</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/h2.jpg" alt=""><br>
                <p class="movie-list-item-desc">TRINIL: KEMBALIKAN TUBUHKU</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/h3.jpg" alt=""><br>
                <p class="movie-list-item-desc">LAMPIR</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/h4.jpg" alt=""><br>
                <p class="movie-list-item-desc">MUNKAR</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/h5.jpg" alt=""><br>
                <p class="movie-list-item-desc">PEMANDI JENAZAH</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/9.jpg" alt=""><br>
                <p class="movie-list-item-desc">KERETA BERDARAH</p>
            </div>
        </div>
        <i class="fas fa-chevron-right arrow right-arrow" onclick="scrollRightList(this)"></i>
    </div>
    <div class="movie-list-container">
        <h1 class="movie-list-title">ACTION</h1>
        <div class="movie-list-wrapper">
            <i class="fas fa-chevron-left arrow left-arrow" onclick="scrollLeftList(this)"></i>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/a1.jpg" alt=""><br>
                <p class="movie-list-item-desc">FREEEDOM</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/a2.jpg" alt=""><br>
                <p class="movie-list-item-desc">HYPENOTIC</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/a3.jpg" alt=""><br>
                <p class="movie-list-item-desc">RAMBO</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/a4.jpg" alt=""><br>
                <p class="movie-list-item-desc">13 Bom di Jakart</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/a5.jpg" alt=""><br>
                <p class="movie-list-item-desc">Java Heata</p>
            </div>
        </div>
        <i class="fas fa-chevron-right arrow right-arrow" onclick="scrollRightList(this)"></i>
    </div>
    <div class="movie-list-container">
        <h1 class="movie-list-title">COMEDY</h1>
        <div class="movie-list-wrapper">
            <i class="fas fa-chevron-left arrow left-arrow" onclick="scrollLeftList(this)"></i>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/c-1.jpg" alt=""><br>
                <p class="movie-list-item-desc">Kaka Boss</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/c-2.jpg" alt=""><br>
                <p class="movie-list-item-desc">Agak Laen</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/c-3.jpg" alt=""><br>
                <p class="movie-list-item-desc">Ugal Ugalan</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/c-4.jpg" alt=""><br>
                <p class="movie-list-item-desc">Mama Mama Jagoan</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/c-5.jpg" alt=""><br>
                <p class="movie-list-item-desc">Sekalwan Limo</p>
            </div>
            <div class="movie-list-item">
                <img class="movie-list-item-img" src="img/c-6.jpg" alt=""><br>
                <p class="movie-list-item-desc">Cek Toko Sebelah</p>
            </div>
        </div>
        <i class="fas fa-chevron-right arrow right-arrow" onclick="scrollRightList(this)"></i>
    </div>
  
  

    <script src="scr.js"></script>
</body>

</html>

                