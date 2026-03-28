<?php
session_start();

// --- 1. Database Connection Settings ---
$servername = "localhost";
$username = "root";
$password = "waad";
$dbname = "waad";

// Establish database connection with error handling
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Show a user-friendly error message if the database connection fails
    die("<h1>Database Connection Error</h1><p>We are sorry, but we cannot connect to the database at the moment. Please try again later.</p>");
}

// --- 2. Fetch Review Data ---
$rating_counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
$total_reviews = 0;
$average_rating = 0.0;
$recent_reviews_result = null;

try {
    // Get count for each rating
    $sql_rating_counts = "SELECT rating, COUNT(*) as count FROM reviews GROUP BY rating";
    $counts_result = $conn->query($sql_rating_counts);
    if ($counts_result) {
        while ($row = $counts_result->fetch_assoc()) {
            if (isset($rating_counts[$row['rating']])) {
                $rating_counts[$row['rating']] = (int)$row['count'];
            }
        }
    }

    // Calculate total and average
    $total_reviews = array_sum($rating_counts);
    if ($total_reviews > 0) {
        $total_score = ($rating_counts[5] * 5) + ($rating_counts[4] * 4) + ($rating_counts[3] * 3) + ($rating_counts[2] * 2) + ($rating_counts[1] * 1);
        $average_rating = round($total_score / $total_reviews, 1);
    }

    // Get recent reviews
    $sql_recent_reviews = "SELECT user_name, comment, rating FROM reviews ORDER BY created_at DESC LIMIT 3";
    $recent_reviews_result = $conn->query($sql_recent_reviews);

} catch (mysqli_sql_exception $e) {
    // Gracefully ignore error if the 'reviews' table doesn't exist yet
}

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Wejhatna - Palestine Nature Journeys</title>
        <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    </head>
    <body>
    <!-- ======================= Navigation ======================= -->
    <nav>
        <div class="nav-content">
            <a class="logo" href="homee.php">Wejhatna</a>
            <div class="menu">
                <a href="homee.php">Home</a>
                <a href="#about">About</a>
                <a href="explore.php">Gallery</a>
                <a href="trips.php">Trips</a>
                <a href="contact.php">Contact</a>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="sign.html">Sign In</a>
                <?php endif; ?>
            </div>
            <div class="menu-btn" id="mobile-menu-btn">☰</div>
        </div>
        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobile-menu">
            <a href="homee.php">Home</a>
            <a href="#about">About</a>
            <a href="explore.php">Gallery</a>
            <a href="trips.php">Trips</a>
            <a href="contact.php">Contact</a>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <a href="logout.php">Logout</a>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <?php else: ?>
                <a href="sign.html">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="overlay">
            <h1>Discover Palestine’s hidden paradise <br>where nature tells its story</h1>
            <p>Discover the hidden gems of Palestine's natural beauty</p>
            <a href="trips.php" class="btn">Explore Trips</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about">
        <div class="container">
            <div class="about-text">
                <h2>Our Story</h2>
                <p>Founded in 2019, Palestine Nature Journeys began as a passion project to showcase the incredible natural beauty of our homeland.</p>
                <p>Our mission is to create meaningful connections between travelers and the land, while supporting local communities and preserving our natural heritage.</p>
                <div class="feature">
                    <div class="icon">🌿</div>
                    <div>
                        <strong>Sustainable Tourism</strong>
                        <p class="small-text">Protecting nature for future generations</p>
                    </div>
                </div>
            </div>
            <div class="about-images">
                <div>
                    <img class="img-tall" src="image/about1.jpg" alt="Nature 1">
                    <img class="img-tall" src="image/about22.jpeg" alt="Nature 2">
                </div>
                <div class="second-column">
                    <img class="img-tall" src="image/about3.jpg" alt="Nature 3">
                    <img class="img-tall" src="image/about4.jpg" alt="Nature 4">
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="reviews-section-v2">
        <div class="container">
            <div class="reviews-grid">
                <!-- Column 1: Summary -->
                <div class="reviews-summary-col">
                    <div class="section-header-left">
                        <h2>What Our<br>Adventurers Say</h2>
                        <p>Real feedback from people who have journeyed with us.</p>
                    </div>
                    <div class="ratings-breakdown">
                        <?php for ($i = 5; $i >= 1; $i--):
                            $percentage = ($total_reviews > 0) ? ($rating_counts[$i] / $total_reviews) * 100 : 0;
                            $star_word = ["ONE", "TWO", "THREE", "FOUR", "FIVE"][$i-1];
                            ?>
                            <div class="rating-row">
                                <span><?php echo $star_word; ?></span>
                                <div class="progress-bar"><div class="progress" style="width: <?php echo $percentage; ?>%;"></div></div>
                                <span class="count"><?php echo $rating_counts[$i]; ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="average-rating">
                        <div class="average-score"><?php echo number_format($average_rating, 1); ?></div>
                        <div class="stars">
                            <?php
                            $full_stars = floor($average_rating);
                            $half_star = round($average_rating - $full_stars);
                            $empty_stars = 5 - $full_stars - $half_star;

                            for ($i = 0; $i < $full_stars; $i++) echo '<i class="ri-star-s-fill"></i>';
                            if ($half_star) echo '<i class="ri-star-half-s-line"></i>';
                            for ($i = 0; $i < $empty_stars; $i++) echo '<i class="ri-star-s-line"></i>';
                            ?>
                        </div>
                        <div class="total-ratings">Based on <?php echo $total_reviews; ?> Ratings</div>
                    </div>
                </div>

                <!-- Column 2: Feedbacks -->
                <div class="recent-feedbacks-col">
                    <h3>Recent Feedbacks</h3>
                    <?php if ($recent_reviews_result && $recent_reviews_result->num_rows > 0): ?>
                        <?php while($review = $recent_reviews_result->fetch_assoc()): ?>
                            <div class="feedback-card">
                                <img src="image/revg.jpg" alt="User Avatar" class="avatar">
                                <div class="feedback-content">
                                    <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Be the first to leave a review!</p>
                    <?php endif; ?>
                </div>

                <!-- Column 3: Add Review Form -->
                <div class="add-review-col">
                    <h3>Add a Review</h3>
                    <form id="review-form" action="submit_review.php" method="POST">
                        <div class="form-group rating-input">
                            <label>Your Rating</label>
                            <div class="star-rating">
                                <input type="radio" id="5-stars" name="rating" value="5" required /><label for="5-stars"><i class="ri-star-s-fill"></i></label>
                                <input type="radio" id="4-stars" name="rating" value="4" /><label for="4-stars"><i class="ri-star-s-fill"></i></label>
                                <input type="radio" id="3-stars" name="rating" value="3" /><label for="3-stars"><i class="ri-star-s-fill"></i></label>
                                <input type="radio" id="2-stars" name="rating" value="2" /><label for="2-stars"><i class="ri-star-s-fill"></i></label>
                                <input type="radio" id="1-star" name="rating" value="1" /><label for="1-star"><i class="ri-star-s-fill"></i></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="review-name">Name</label>
                            <input type="text" id="review-name" name="name" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label for="review-email">Email</label>
                            <input type="email" id="review-email" name="email" placeholder="john.doe@email.com" required>
                        </div>
                        <div class="form-group">
                            <label for="review-text">Write Your Review</label>
                            <textarea id="review-text" name="comment" rows="4" placeholder="Share your experience..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <!-- Footer Section -->
    <footer class="footer">
        <!-- محتوى الفوتر يبقى كما هو -->
        <div class="footer-container">
            <!-- About -->
            <div class="footer-section about">
                <h3>Wejhatna</h3>
                <p>Connecting people with Palestine's natural beauty through sustainable <br> and meaningful journeys.</p>
            </div>
            <!-- Quick Links -->
            <div class="footer-section links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="homee.php">Home</a></li>
                    <li><a href="homee.php#about">About</a></li>
                    <li><a href="explore.php">Gallery</a></li>
                    <li><a href="trips.php">Trips</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <!-- Destinations -->
            <div class="footer-section destinations">
                <h4>Destinations</h4>
                <ul>
                    <li><a href="trips.php?search=nablus">Nablus </a></li>
                    <li><a href="trips.php?search=jericho">Jericho </a></li>
                    <li><a href="trips.php?search=haifa">Haifa </a></li>
                    <li><a href="trips.php?search=bethlehem">Bethlehem </a></li>
                    <li><a href="trips.php?search=hebron">Hebron </a></li>
                    <li><a href="trips.php?search=galilee">Galilee</a></li>
                </ul>
            </div>
            <!-- Contact -->
            <div class="footer-section contact">
                <h4>Contact Us</h4>
                <a href="https://mail.google.com/mail/?view=cm&to=wejhatna@gmail.com" target="_blank"
                   style="color: #ffffff; text-decoration: underline;">
                    wejhatna@gmail.com
                </a>
                <p> <br>Phone: +970 599 395 475</p>
                <p>  Location: Nablus, Palestine</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2025 Wejhatna. All rights reserved.
        </div>
    </footer>

    <!-- الربط بملف الجافاسكريبت الخارجي -->
    <script src="scriptnew.js?v=<?php echo time(); ?>"></script>
    </body>
    </html>
<?php
$conn->close();
?>