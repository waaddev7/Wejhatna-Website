<?php
// ابدأ الجلسة أولاً لعرض القائمة بشكل صحيح
session_start();

// --- الجزء الأول: معالجة إرسال النموذج (يعمل فقط مع طلبات POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // إعداد الاستجابة كـ JSON (مهم لـ JavaScript)
    header('Content-Type: application/json');
    $response = [];

    // جلب البيانات وتنظيفها
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // التحقق من صحة البيانات
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = 'Please enter a valid email address.';
    } else {
        // حفظ الرسالة في قاعدة البيانات
        try {
            $conn = new mysqli("localhost", "root", "waad", "waad");
            $conn->set_charset("utf8mb4");
            $sql = "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $email, $subject, $message);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Thank you! Your message has been sent.';
            } else { throw new Exception("Failed to save message."); }
            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = 'An unexpected error occurred.';
        }
    }

    // طباعة الرد بصيغة JSON ثم إيقاف التنفيذ. لن يتم عرض أي HTML بعد هذا.
    echo json_encode($response);
    exit;
}

// --- الجزء الثاني: عرض صفحة HTML (يعمل فقط عند زيارة الصفحة العادية) ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - Wejhatna</title>
    <link rel="stylesheet" href="styles.css" />
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
<main class="contact-page">
    <section class="contact-hero">
        <div class="container">
            <h1>Contact Us</h1>
            <p>Ready to embark on your Palestinian nature journey? Contact us to book your trip or ask any questions.</p>
        </div>
    </section>

    <section class="contact-content-section">
        <div class="container">
            <div class="form-grid">
                <form id="contact-form" class="contact-form-v2" action="contact.php" method="POST">
                    <div class="form-row">
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="text" name="subject" placeholder="Subject" required>
                    </div>
                    <div class="form-row">
                        <input type="text" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-row">
                        <textarea name="message" placeholder="Message" rows="6" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Send Message</button>
                </form>

                <aside class="video-box">
                    <!--                    <h3>Watch Our Journey</h3>-->
                    <video width="100%" autoplay loop muted playsinline>
                        <source src="videos/contact.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </aside>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-icon"><i class="ri-phone-fill"></i></div>
                    <h4>+970 599 395 475</h4>
                    <p>Call us for direct inquiries and urgent matters. We are available during business hours.</p>
                </div>
                <div class="info-box">
                    <div class="info-icon"><i class="ri-mail-fill"></i></div>
                    <h4>wejhatna@gmail.com</h4>
                    <p>Email us for booking details, questions, or collaboration proposals. We reply within 24 hours.</p>
                </div>
                <div class="info-box">
                    <div class="info-icon"><i class="ri-map-pin-2-fill"></i></div>
                    <h4>Nablus, Palestine</h4>
                    <p>Our main office is located in the heart of Nablus, the starting point for many of our adventures.</p>
                </div>
            </div>

            <div class="map-section">
                <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d54021.98684279589!2d35.22428511477051!3d32.22384992923143!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151ce12434592315%3A0x629c15392f41857!2sNablus!5e0!3m2!1sen!2sps!4v1672345678901!5m2!1sen!2sps"
                        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
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