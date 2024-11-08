<?php
// Sertakan file koneksi database
include 'db.php';

// Ambil data film dari database
$query = "SELECT * FROM film";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Link ke file CSS untuk styling -->
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    
    <title>CinePicks</title>
</head>

<body>

<style>
    .movie-list-title {
        color: #fff;
    }
    /* Styling umum untuk section */
    .section {
        padding: 50px 20px;
        width: 100%;
        max-width: 900px;
        margin: auto;
    }

    /* Header dan paragraph di about */
    .section h2 {
        font-size: 2.5rem;
        color: #4dbf00;
        margin-bottom: 20px;
        text-align: center;
    }

    /* About Us dan About Website styling */
    .about-us, .about-website {
        background-color: #1d1d1d;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(77, 191, 0, 0.3);
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
    
    /* Styling kontainer team */
    .team-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 20px;
    }

    /* Responsif untuk mobile */
    @media (max-width: 768px) {
        .section h2 { font-size: 2rem; }
        .about-us, .about-website { padding: 20px; margin: 10px 15px; }
    }
</style>

    <!-- Navbar desktop -->
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
        <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/login.php';">Sign In</button></li>
    </div>

    <!-- Navbar mobile -->
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
                <li class="menu-list-item"><button class="login-btn" onclick="window.location.href='auth/login.php';">Sign In</button></li>
            </ul>
            <div class="search-container-mobile">
                <form method="GET" action="search.php">
                    <input type="text" name="query" placeholder="Search..." aria-label="Search">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Konten unggulan -->
    <div class="content-container">
        <div class="featured-content" style="background: linear-gradient(to bottom, rgba(0,0,0,0), #151515), url('img/f-1.jpg');"></div>
    <!-- Daftar film baru -->
    <div class="movie-list-container">
        <h1 class="movie-list-title">FILM RELEASES</h1>
        <div class="movie-list-wrapper">
            <i class="fas fa-chevron-left arrow left-arrow" onclick="scrollLeftList(this)"></i>

            <!-- Loop untuk daftar film -->
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

    <!-- Tentang kami -->
    <div id="about" class="section about-us">
        <h2>About Us</h2>
        <div class="team-container">
            <div class="team-member">
                <img src="img/us.jpg" alt="Member team">
                <div class="team-member-info">
                    <h3>We are a passionate team of movie enthusiasts who are dedicated to bringing you the best movie recommendations,
                        reviews, and insights. Our mission is to create a community where movie lovers can connect,
                        share their opinions, and discover new films from all around the world.
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="section about-website">
        <h2>About the Website</h2>
        <p>CinePicks is a platform designed to help you discover movies that match your taste. 
            With an easy-to-navigate interface, we provide personalized movie recommendations, detailed reviews,
            and a vibrant community of movie enthusiasts. Whether you're looking for the latest blockbuster or a hidden indie gem,
            CinePicks has something for everyone. Join us in exploring the vast world of cinema!</p>
        </div>

<footer>
    &copy; 2024 CinePicks. All rights reserved.
</footer>
<script src="scr.js"></script>

</body>
</html>
