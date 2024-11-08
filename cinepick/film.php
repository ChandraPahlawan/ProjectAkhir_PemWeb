<?php
include 'db.php';
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
    
    $profile_pic = !empty($user['profile_pic']) ? "uploads/" . htmlspecialchars($user['profile_pic']) : "uploads/profil.png";

}
// Initialize message variable
$message = '';

// Fetch film data by filmID from URL
if (isset($_GET['filmID'])) {
    $filmID = $_GET['filmID'];

    $query = "SELECT title, genre, releaseYear, description, upPic, videoURL FROM film WHERE filmID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $filmID);
    $stmt->execute();
    $result = $stmt->get_result();
    $film = $result->fetch_assoc();

    // Convert YouTube URL to embed format
    function convertToEmbedURL($url) {
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        return isset($params['v']) ? "https://www.youtube.com/embed/" . $params['v'] : $url;
    }

    $embedURL = isset($film['videoURL']) ? convertToEmbedURL($film['videoURL']) : '';
} else {
    echo "Film ID tidak ditemukan.";
    exit;
}

// Process review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating']) && isset($_POST['comment'])) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $userID = $_SESSION['userID'] ?? null;

    if ($userID && $filmID) {
        $stmt = $conn->prepare("INSERT INTO review (filmID, userID, rating, comment, review_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $filmID, $userID, $rating, $comment);

        if ($stmt->execute()) {
            // echo "<p>Review berhasil disimpan!</p>";
        } else {
            // echo "<p>Gagal menyimpan review. Silakan coba lagi.</p>";
        }
    } else {
        echo "<p>Anda harus login untuk memberikan review.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <title><?php echo isset($film['title']) ? htmlspecialchars($film['title']) : 'Film Tidak Ditemukan'; ?></title>
    <link rel="stylesheet" href="style.css" class="href">
</head>
<body>
    <style>
    .video-container {
    text-align: center;
    color: #ffffff;
    margin-top: 20px;
    }
    .video-frame {
    width: 80%;
    max-width: 700px;
    margin: 0 auto;
    padding-bottom: 20px;
    border-bottom: 5px solid #4dbf00;
    width: 47%;
    }
    .video-frame iframe {
    width: 100%;
    height: 400px;
    border: none;
    }
    .video-details {
    background-color: #333;          /* Warna latar belakang yang terang */
    padding: 20px;                      /* Jarak dalam */
    border-radius: 10px;                /* Membuat sudut membulat */
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);  /* Bayangan lembut */
    max-width: 800px;                   /* Lebar maksimum kontainer */
    margin: 20px auto;                  /* Margin untuk tengah */
    color: #333;                        /* Warna teks utama */
    }
    .video-details h1, .video-details p {
    color: #ffffff;
    margin: 10px 0;
    /* margin-left: 405px; */
    text-align: left;
    }
    .review-heading {
        color: #4dbf00;
        font-size: 24px;
        margin-top: 30px;
        margin-left: 405px;
    }
    .login-message {
        text-align: center;
        margin-top: 15px;
        padding: 15px;
        border-radius: 10px;
        color: #fff;
    }
    .login-message a {
        color: #4dbf00;
    }
    .review-form, .review-list {
        margin-top: 20px;
        background-color: #151515;
        padding: 20px;
        border-radius: 10px;
    }
    .review-form {
        max-width: 500px; /* Lebar maksimal form */
        margin: 0 auto; /* Agar form berada di tengah */
        padding: 20px 200px;
        background-color: #222; /* Warna latar belakang form */
        border-radius: 8px; /* Membuat border membulat */
        margin-top: 20px;
    }
    #comment {
    width: 100%;                       
    padding: 10px;                    
    font-size: 1rem;                  
    color: #333;                       
    background-color: #f9f9f9;        
    border: 1px solid #ccc;          
    border-radius: 5px;               
    resize: vertical;                  
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1); 
    }
    label[for="comment"] {
        color: #fff;                   
    }
    .rating {
        margin-top: 20px;
        text-align: center;
    }
    .rating label {
        font-size: 18px;
        color: #4dbf00;
        font-weight: 600;
    }
    #rating {
        width: 100%;
        padding: 10px;
        background-color: #111;
        color: #4dbf00;
        border: 1px solid #4dbf00;
        border-radius: 5px;
        font-size: 16px;
        margin-top: 10px;
    }
    #rating:focus {
        outline: none;
        box-shadow: 0 0 5px #4dbf00;
    }
    .review-item {
        background-color: #222;
        padding: 15px 200px;
        margin: 0 auto;
        border-radius: 8px;
        max-width: 500px; /* Pastikan kotak review menempati seluruh lebar kontainer */
        word-wrap: break-word; /* Pecah kata panjang */
        overflow-wrap: break-word;
        color: #fff;
        box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.5); /* Tambahkan bayangan untuk kesan kedalaman */
    }
    .review-heading {
    font-size: 2rem;                  /* Ukuran teks untuk heading */
    color: #4dbf00;                   /* Warna teks hijau */
    text-align: center;               /* Menempatkan teks di tengah */
    margin: 30px 0 20px;              /* Margin atas dan bawah untuk ruang */
    font-weight: 700;                 /* Membuat teks lebih tebal */
    letter-spacing: 1px;              /* Memberi jarak antar huruf */
    }
    .review-header label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .review-username {
        font-weight: bold;
        color: #4dbf00; /* Warna username agar lebih menonjol */
    }
    .review-date {
        font-size: 12px;
        color: #aaa;
    }
    .review-rating {
        color: #ffcc00;
        font-size: 18px;
        margin-bottom: 5px;
    }
    .review-comment {
        font-size: 14px;
        line-height: 1.5; /* Tambahkan spasi antarbaris untuk keterbacaan */
        white-space: pre-wrap; /* Menjaga format teks agar sesuai dengan input */
    }
    .submit-review {
        background-color: #4dbf00;
        font-size: 20px;
        box-shadow: #4dbf00;
    }
     /* Responsive Styles */
     @media (max-width: 768px) {
            .video-frame iframe {
                height: 250px;
            }
            .navbar-desktop, .navbar-mobile .profile-container {
                display: none;
            }
            .navbar-mobile {
                display: block;
            }
            .menu-container-mobile ul {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .review-form, .review-item {
                padding: 15px;
                max-width: 100%;
            }
            .review-heading {
                font-size: 20px;
                margin-top: 20px;
            }
            .video-details {
                padding: 15px;
                max-width: 100%;
            }
        }
        
        /* Very Small Screen Styles */
        @media (max-width: 480px) {
            .video-frame iframe {
                height: 200px;
            }
            .review-heading {
                font-size: 18px;
            }
            .review-form {
                padding: 10px;
            }
            .video-details, .review-item {
                padding: 10px;
            }
        }
    </style>
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
                <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='logout.php';">Logout</button></li>
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
    <!-- Trailer Section -->
    <div class="video-container">
        <?php if (!empty($embedURL)): ?>
            <div class="video-frame">
                <iframe src="<?php echo htmlspecialchars($embedURL); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        <?php else: ?>
            <p class="no-trailer-message">Trailer tidak tersedia.</p>
        <?php endif; ?>

        <div class="video-details">
            <h1><?php echo htmlspecialchars($film['title'] ?? 'Film Tidak Ditemukan'); ?></h1>
            <p>Genre: <?php echo htmlspecialchars($film['genre'] ?? 'Tidak tersedia'); ?></p>
            <p>Tahun Rilis: <?php echo htmlspecialchars($film['releaseYear'] ?? 'Tidak tersedia'); ?></p>
            <p>Deskripsi: <?php echo htmlspecialchars($film['description'] ?? 'Deskripsi tidak tersedia'); ?></p>
        </div>
    </div>

    <!-- Review Form Section -->
    <h2 class="review-heading">Tambahkan Review</h2>
    <?php if (isset($_SESSION['userID'])): ?>
        <form action="" method="post" class="review-form">
            <div class="rating">
            <label for="rating">Rating (1-5):</label>
            <select name="rating" id="rating" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select><br><br>
            </div>
            <label for="comment">Komentar:</label><br>
            <textarea name="comment" id="comment" rows="4" maxlength="1000" required></textarea><br><br>
            <button type="submit" class="submit-review">Kirim Review</button>
        </form>
    <?php else: ?>
        <p class="login-message">Silakan <a href="auth/login.php">login</a> untuk memberikan review.</p>
    <?php endif; ?>

    <!-- Display Reviews Section -->
    <h2 class="review-heading">Review</h2>
    <div class="review-list">
    <?php
    $query = "SELECT r.rating, r.comment, a.username, r.review_date
              FROM review r
              JOIN akun a ON r.userID = a.userID
              WHERE r.filmID = ?
              ORDER BY r.review_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $filmID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $rating = $row['rating'];
        $comment        = $row['comment'];
        $username = $row['username'];
        $review_date = $row['review_date'];

        echo "<div class='review-item'>";
        echo "<div class='review-header'>";
        echo "<strong class='review-username'>$username</strong>";
        echo "<em class='review-date'>$review_date</em>";
        echo "</div>";
        echo "<div class='review-rating'>" . str_repeat("★", $rating) . str_repeat("☆", 5 - $rating) . "</div>";
        echo "<p class='review-comment'>$comment</p>";
        echo "</div>";
    }
    ?>
    </div>
    <footer>
        &copy; 2024 CinePicks. All rights reserved.
    </footer>
    <script src="scr.js"></script>
</body>
</html>
