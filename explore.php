<?php
// ابدأ الجلسة لكي تتمكن القائمة من معرفة حالة تسجيل الدخول
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Palestine Nature Journeys</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
</head>
<body>

<!-- ======================= Navigation (النسخة التي تعمل بـ PHP) ======================= -->
<nav>
    <div class="nav-content">
        <a class="logo" href="homee.php" style="text-decoration: none;">Wejhatna</a>
        <div class="menu">
            <a href="homee.php">Home</a>
            <a href="homee.php#about">About</a>
            <a href="explore.php">Gallery</a>
            <a href="trips.php">Trips</a>
            <a href="contact.php">Contact</a>

            <?php // هذا هو الجزء الديناميكي ?>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <span class="welcome-user" style="color: rgba(92, 115, 69, 0.86); font-weight: bold; margin: 0 15px; align-self: center;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="logout.php">Logout</a>
            <?php else: ?>
            <a href="sign.html">SignIn</a>
            <?php endif; ?>
        </div>
        <div class="menu-btn" id="mobile-menu-btn">☰</div>
    </div>
    <div class="mobile-menu" id="mobile-menu">
        <a href="homee.php">Home</a>
        <a href="homee.php#about">About</a>
        <a href="explore.php">Gallery</a>
        <a href="trips.php">Trips</a>
        <a href="contact.php">Contact</a>

        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <a href="logout.php">Logout</a>
        <?php else: ?>
        <a href="sign.html">SignIn</a>
        <?php endif; ?>
    </div>
</nav>

<main>
    <section id="gallery" class="section bg-white">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Journey Memories</h2>
                <p>Explore Our Journeys Across Palestine ,from the serene mountains and lush valleys to the hidden oases and timeless villages .</p>
            </div>
            <div class="gallery-filters">
                <button class="gallery-filter active" data-filter="all">All Locations</button>
            </div>

            <div class="gallery-grid">
                <!-- 1. Nablus Olive Groves -->
                <div class="gallery-item" data-category="mountains">
                    <a href="exploredetails.html?trip=nablus-groves" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/NablusOliveGroves.jpg" alt="Nablus Olive Groves" class="img-default">
                            <img src="image/Palestineoliveseason1.jpeg" alt="Nablus Olive Groves" class="img-hover">
                            <div class="gallery-overlay"><span>Nablus Olive Groves</span></div>
                        </div>
                    </a>
                </div>
                <!-- 2. سور عكا -->
                <div class="gallery-item" data-category="seas">
                    <a href="exploredetails.html?trip=akka-wall" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/akka.jpeg" alt="Akka Wall" class="img-default">
                            <img src="image/akka3.png" alt="Akka Wall " class="img-hover">
                            <div class="gallery-overlay"><span>Akka Wall</span></div>
                        </div>
                    </a>
                </div>
                <!-- 3. الكنائس -->
                <div class="gallery-item" data-category="buildings">
                    <a href="exploredetails.html?trip=holy-sepulchre" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/church.JPG" alt="Church of the Holy Sepulchre" class="img-default">
                            <img src="image/churchh.jpeg" alt="Church of the Holy Sepulchre" class="img-hover">
                            <div class="gallery-overlay"><span>Church of the Holy Sepulchre</span></div>
                        </div>
                    </a>
                </div>
                <!-- 4. Mountain Summit -->
                <div class="gallery-item" data-category="mountains">
                    <a href="exploredetails.html?trip=mountain-summit" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/mountains (2).jpeg" alt="Mountain Summit" class="img-default">
                            <img src="image/mountains.png" alt="Mountain Summit" class="img-hover">
                            <div class="gallery-overlay"><span>Mountain Summit</span></div>
                        </div>
                    </a>
                </div>
                <!-- 5. Traditional Village -->
                <div class="gallery-item" data-category="buildings">
                    <a href="exploredetails.html?trip=traditional-village" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/Traditional Village.jpeg" alt="Traditional Village" class="img-default">
                            <img src="image/traditionalvillage2.jpeg" alt="Village Alternative" class="img-hover">
                            <div class="gallery-overlay"><span>Traditional Village</span></div>
                        </div>
                    </a>
                </div>
                <!-- 6. Hidden Waterfall -->
                <div class="gallery-item" data-category="seas">
                    <a href="exploredetails.html?trip=hidden-waterfall" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/Hidden Waterfall.jpg" alt="Hidden Waterfall" class="img-default">
                            <img src="image/HiddenWaterfall2.jpg" alt="Hidden Waterfall" class="img-hover">
                            <div class="gallery-overlay"><span>Hidden Waterfall</span></div>
                        </div>
                    </a>
                </div>
                <!-- 7. Green Valley -->
                <div class="gallery-item" data-category="valleys">
                    <a href="exploredetails.html?trip=green-valley" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/Green Valley.jpg" alt="Green Valley" class="img-default">
                            <img src="image/valley.jpeg" alt="Green Valley" class="img-hover">
                            <div class="gallery-overlay"><span>Green Valley</span></div>
                        </div>
                    </a>
                </div>
                <!-- 8. Tiberias Lake -->
                <div class="gallery-item" data-category="seas">
                    <a href="exploredetails.html?trip=tiberias-lake" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/Tabaria.png" alt="Tiberias Lake" class="img-default">
                            <img src="image/lakes2.png" alt="Tiberias Lake" class="img-hover">
                            <div class="gallery-overlay"><span>Tiberias Lake</span></div>
                        </div>
                    </a>
                </div>
                <!-- 9. المسجد الأقصى -->
                <div class="gallery-item" data-category="buildings">
                    <a href="exploredetails.html?trip=al-aqsa" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/quds.jpeg" alt="Al Aqsa Mosque" class="img-default">
                            <img src="image/quds2.jpeg" alt="Al Aqsa Mosque " class="img-hover">
                            <div class="gallery-overlay"><span>Al Aqsa Mosque</span></div>
                        </div>
                    </a>
                </div>
                <!-- 10. بحر يافا -->
                <div class="gallery-item" data-category="seas">
                    <a href="exploredetails.html?trip=jaffa-sea" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/jaffa.png" alt="Jaffa Sea" class="img-default">
                            <img src="image/affa.png" alt="Jaffa Sea " class="img-hover">
                            <div class="gallery-overlay"><span>Jaffa Sea</span></div>
                        </div>
                    </a>
                </div>
                <!-- 11. القصور -->
                <div class="gallery-item" data-category="buildings">
                    <a href="exploredetails.html?trip=al-masry-palace" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/qasrmunip.png" alt="Munib Al-Masry Palace" class="img-default">
                            <img src="image/qasr.png" alt="Munib Al-Masry Palace" class="img-hover">
                            <div class="gallery-overlay"><span>Munib Al-Masry Palace</span></div>
                        </div>
                    </a>
                </div>
                <!-- 12. الحدائق المعلقة -->
                <div class="gallery-item" data-category="buildings">
                    <a href="exploredetails.html?trip=hanging-gardens" style="text-decoration: none; color: inherit;">
                        <div class="gallery-card">
                            <img src="image/haifa1.png" alt="Hanging Gardens" class="img-default">
                            <img src="image/haifa2.jpg" alt="Hanging Gardens" class="img-hover">
                            <div class="gallery-overlay"><span>Hanging Gardens</span></div>
                        </div>
                    </a>
                </div>
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

<script src="script.js"></script>
</body>
</html>