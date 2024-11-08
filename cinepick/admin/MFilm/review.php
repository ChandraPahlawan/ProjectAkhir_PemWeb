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

// Mendapatkan filmID dari parameter URL
$filmID = isset($_GET['filmID']) ? intval($_GET['filmID']) : 0;

// Ambil informasi film berdasarkan filmID
$filmQuery = "SELECT title FROM film WHERE filmID = $filmID";
$filmResult = mysqli_query($conn, $filmQuery);
$film = mysqli_fetch_assoc($filmResult);

// Ambil review untuk film yang dipilih
$reviewQuery = "SELECT review.*, akun.username FROM review 
                JOIN akun ON review.userID = akun.userID 
                WHERE review.filmID = $filmID";
$reviewResult = mysqli_query($conn, $reviewQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Film -<?php echo htmlspecialchars($film['title']); ?></title>
    <style>
        /* CSS styling */
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
        .container {
            max-width: 800px;
            width: 100%;
        }
        .left-button {
            color: #000;
            background: #4dbf00;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 20px;
            margin-right: 20px;
            transition: background 0.3s;
        }
        .left-button:hover {
            background-color: #3e9d00;
        }
        h2 {
            font-size: 24px;
            color: #4dbf00;
            margin-bottom: 20px;
            text-align: center;
        }
        .review-card {
            background-color: #111;
            border: 1px solid #4dbf00;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .review-card p {
            margin: 5px 0;
            color: #ccc;
        }
        .review-comment {
            max-width: 100%;
            word-wrap: break-word;
            color: #fff;
            overflow: hidden;
        }
        .collapsed {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .expanded {
            display: block;
        }
        .read-more {
            color: #4dbf00;
            font-weight: bold;
            cursor: pointer;
            margin-top: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="list.php" class="left-button">Kembali ke Daftar Film</a>
        <h2>Review untuk Film: <?php echo htmlspecialchars($film['title']); ?></h2>
        
        <div class="reviews">
            <?php if (mysqli_num_rows($reviewResult) > 0) { ?>
                <?php while ($review = mysqli_fetch_assoc($reviewResult)) { ?>
                    <div class="review-card">
                        <p><strong><?php echo htmlspecialchars($review['username']); ?></strong> (Rating: <?php echo htmlspecialchars($review['rating']); ?>)</p>
                        <p class="review-comment collapsed" id="comment-<?php echo $review['reviewID']; ?>" data-full-text="<?php echo htmlspecialchars($review['comment']); ?>" data-short-text="<?php echo htmlspecialchars(substr($review['comment'], 0, 50)) . (strlen($review['comment']) > 50 ? "..." : ""); ?>">
                            <?php echo htmlspecialchars(substr($review['comment'], 0, 50)) . (strlen($review['comment']) > 50 ? "..." : ""); ?>
                        </p>
                        <?php if (strlen($review['comment']) > 50) { ?>
                            <span class="read-more" onclick="toggleComment('<?php echo $review['reviewID']; ?>')">Read More</span>
                        <?php } ?>
                        <p>Date: <?php echo htmlspecialchars($review['review_date']); ?></p>
                        <a href="del_rev.php?id=<?php echo $review['reviewID']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus review ini?')" class="left-button">Delete Review</a>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>Tidak ada review untuk film ini.</p>
            <?php } ?>
        </div>
    </div>

    <script>
        function toggleComment(reviewID) {
            const comment = document.getElementById(`comment-${reviewID}`);
            const readMore = comment.nextElementSibling;

            if (comment.classList.contains('expanded')) {
                comment.classList.remove('expanded');
                comment.classList.add('collapsed');
                readMore.innerText = 'Read More';
                comment.innerText = comment.getAttribute('data-short-text');
            } else {
                comment.classList.remove('collapsed');
                comment.classList.add('expanded');
                readMore.innerText = 'Read Less';
                comment.innerText = comment.getAttribute('data-full-text');
            }
        }
    </script>
</body>
</html>
