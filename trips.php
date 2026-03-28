<?php
// ابدأ الجلسة للوصول إلى بيانات المستخدم (مهم للقائمة)
session_start();

// 1. الاتصال بقاعدة البيانات
$servername = "localhost"; $username = "root"; $password = "waad"; $dbname = "waad";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed."); }
$conn->set_charset("utf8mb4");

// 2. جلب كل الرحلات المستقبلية من جدول 'trips'
$sql = "SELECT * FROM trips WHERE trip_date >= NOW() ORDER BY trip_date ASC";
$trips_result = $conn->query($sql);
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Explore Our Trips - Wejhatna</title>
        <link rel="stylesheet" href="stylenewtrip.css" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css">
    </head>
    <body>

    <!-- ======================= Navigation ======================= -->
    <nav>
        <div class="nav-content">
            <a class="logo" href="homee.php" style="  text-decoration: none;"> Wejhatna</a>
            <div class="menu">
                <a href="homee.php">Home</a>
                <a href="#about">About</a>
                <a href="explore.php">Gallery</a>
                <a href="trips.php">Trips</a>
                <a href="contact.php">Contact</a>

                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?><!-- تم تبديل الأماكن وتغيير النص هنا -->

                <span style="color:  rgba(92, 115, 69, 0.86) ;    font-weight: bold;
    margin-left: 15px;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="sign.html">SignIn</a>
                <?php endif; ?>
            </div>

            <div class="menu-btn" id="mobile-menu-btn">☰</div>
        </div>

        <!-- قائمة الجوال - هي بالفعل صحيحة -->
        <div class="mobile-menu" id="mobile-menu">
            <a href="homee.php">Home</a>
            <a href="homee.php#about">About</a>
            <a href="explore.php">Gallery</a>
            <a href="trips.php">Trips</a>
            <a href="contact.php">Contact</a>

            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>

                <a href="logout.php">Logout</a>

                <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <?php else: ?>
                <a href="sign.html">SignIn</a>
            <?php endif; ?>
        </div>
    </nav>
    <main style="margin-top: 4rem">
        <section id="trips" class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Explore Our Trips</h2>
                    <p>Find your next adventure in Palestine's stunning nature.</p>
                </div>

                <!-- ======================= Filters Panel ======================= -->
                <div class="filters-panel-v2">
                    <div class="filter-item search-filter">
                        <i class="ri-search-line"></i>
                        <input type="text" id="filter-search" placeholder="Search by name or city..." />
                    </div>
                    <div class="filter-item">
                        <label for="filter-region">Region:</label>
                        <select id="filter-region" class="filter-select">
                            <option value="all">All</option>
                            <option value="north">North</option>
                            <option value="center">Center</option>
                            <option value="south">South</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="filter-activity">Activity:</label>
                        <select id="filter-activity" class="filter-select">
                            <option value="all">All</option>
                            <option value="hiking">Hiking</option>
                            <option value="biking">Biking</option>
                            <option value="camping">Camping</option>
                            <option value="cultural">Cultural Tour</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="filter-difficulty">Difficulty:</label>
                        <select id="filter-difficulty" class="filter-select">
                            <option value="all">All</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                </div>

                <!-- ======================= Trips Grid ======================= -->
                <div id="trips-grid" class="grid-container">
                    <?php if ($trips_result && $trips_result->num_rows > 0): ?>
                        <?php while ($trip = $trips_result->fetch_assoc()): ?>

                            <?php
                            // تجهيز بيانات الأيقونات
                            $activity_icon = 'ri-walk-line'; // Default
                            if ($trip['activity'] == 'biking') { $activity_icon = 'ri-bike-line'; }
                            elseif ($trip['activity'] == 'camping') { $activity_icon = 'ri-tent-line'; }
                            elseif ($trip['activity'] == 'cultural') { $activity_icon = 'ri-ancient-pavilion-line'; }
                            $difficulty_icon = 'ri-bar-chart-2-line';
                            ?>

                            <a href="trip-details.php?id=<?php echo $trip['id']; ?>" class="trip-card"
                               data-name="<?php echo strtolower(htmlspecialchars($trip['name'])); ?>"
                               data-location="<?php echo strtolower(htmlspecialchars($trip['location'])); ?>"
                               data-region="<?php echo strtolower(htmlspecialchars($trip['region'])); ?>"
                               data-activity="<?php echo strtolower(htmlspecialchars($trip['activity'])); ?>"
                               data-difficulty="<?php echo strtolower(htmlspecialchars($trip['difficulty'])); ?>">

                                <div class="card-image"><img src="<?php echo htmlspecialchars($trip['image_url']); ?>" alt="<?php echo htmlspecialchars($trip['name']); ?>" /></div>

                                <div class="card-content">
                                    <h3><?php echo htmlspecialchars($trip['name']); ?></h3>
                                    <div class="card-tags">
                                        <span><i class="<?php echo $activity_icon; ?>"></i><?php echo htmlspecialchars(ucfirst($trip['activity'])); ?></span>
                                        <span><i class="<?php echo $difficulty_icon; ?>"></i><?php echo htmlspecialchars(ucfirst($trip['difficulty'])); ?></span>
                                    </div>
                                    <p><?php echo htmlspecialchars($trip['tagline']); ?></p>
                                    <div class="card-footer">
                                        <span>₪<?php echo htmlspecialchars($trip['price']); ?></span>
                                        <span><?php echo htmlspecialchars($trip['max_participants']); ?> spots</span>
                                    </div>
                                </div>
                            </a>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; width: 100%;">No upcoming trips found. Please check back later!</p>
                    <?php endif; ?>
                </div>

                <div id="no-results" class="no-results hidden" style="display: none;">
                    <i class="ri-search-eye-line"></i>
                    <p>Sorry, no trips match your search criteria.</p>
                    <p>Try adjusting your filters.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- ======================= Footer ======================= -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section about">
                <h3>Wejhatna</h3>
                <p>Connecting people with Palestine's natural beauty through sustainable <br> and meaningful journeys.</p>
            </div>
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
            <div class="footer-section contact">
                <h4>Contact Us</h4>
                <a href="mailto:wejhatna@gmail.com" target="_blank" style="color: #ffffff; text-decoration: underline;">wejhatna@gmail.com</a>
                <p><br>Phone: +970 599 395 475</p>
                <p><br>Location: Nablus, Palestine</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2025 Wejhatna. All rights reserved.
        </div>
    </footer>

    <script src="scriptnew.js"></script>
    </body>
    </html>
<?php
// إغلاق الاتصال
$conn->close();
?>