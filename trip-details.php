<?php
session_start();
// 1. التحقق من وجود ID في الرابط
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header("Location: trips.php"); exit; }
$trip_id = (int)$_GET['id'];

// 2. الاتصال بقاعدة البيانات
$servername = "localhost"; $username = "root"; $password = "waad"; $dbname = "waad";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed."); }
$conn->set_charset("utf8mb4");

// 3. جلب بيانات الرحلة المحددة
$sql = "SELECT * FROM trips WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trip_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) { header("Location: trips.php?error=notfound"); exit; }
$trip = $result->fetch_assoc();

// *** الإضافة الأولى: حساب الأماكن المتبقية ***
$current_trip_name = $trip['name'];

// حساب إجمالي عدد الأشخاص المحجوزين لهذه الرحلة
$booking_sql = "SELECT SUM(num_people) AS total_booked FROM bookings WHERE trip_name = ?";
$booking_stmt = $conn->prepare($booking_sql);
$booking_stmt->bind_param("s", $current_trip_name); // "s" لأن اسم الرحلة هو نص (string)
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result()->fetch_assoc();
$total_booked = (int)($booking_result['total_booked'] ?? 0); // نضمن أن تكون القيمة رقماً صحيحاً

// حساب الأماكن المتاحة
$available_spots = $trip['max_participants'] - $total_booked;
// *** نهاية الإضافة ***


// 4. تنسيق التاريخ والوقت
$timestamp = strtotime($trip['trip_date']);
$formatted_date = date('l, F j, Y', $timestamp);
$formatted_time = date('g:i A', $timestamp);

// 5. فك ترميز بيانات JSON
$what_to_bring = json_decode($trip['what_to_bring'] ?? '[]', true);
$what_is_included = json_decode($trip['what_is_included'] ?? '[]', true);
$transport_options = json_decode($trip['transport_options'] ?? '[]', true);

// 6. إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet" />
    <meta charset="UTF-8" />
    <title><?php echo htmlspecialchars($trip['name']); ?> - Wejhatna</title>
    <link rel="stylesheet" href="stylenewtrip.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css" />
    <style> .trip-hero { background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('<?php echo htmlspecialchars($trip['image_url']); ?>'); } </style>
</head>
<body>

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

<main class="trip-details-page">
    <section class="trip-hero">
        <div class="trip-hero-overlay"></div>
        <div class="container trip-hero-content">
            <h1><?php echo htmlspecialchars($trip['name']); ?></h1>
            <p><?php echo htmlspecialchars($trip['tagline']); ?></p>
        </div>
    </section>

    <section class="trip-details-section">
        <div class="container trip-details-container">
            <!-- Left Column: Trip Info -->
            <div class="trip-main-content">
                <h2>About the Journey</h2>
                <p><?php echo nl2br(htmlspecialchars($trip['description'])); ?></p>
                <div class="details-grid">
                    <div class="detail-item">
                        <i class="ri-tools-line"></i><h4>What to Bring</h4>
                        <ul><?php foreach ($what_to_bring as $item): ?><li><?php echo htmlspecialchars($item); ?></li><?php endforeach; ?></ul>
                    </div>
                    <div class="detail-item">
                        <i class="ri-list-check"></i><h4>What's Included</h4>
                        <ul><?php foreach ($what_is_included as $item): ?><li><?php echo htmlspecialchars($item); ?></li><?php endforeach; ?></ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Booking Form -->
            <aside class="trip-sidebar">
                <div class="booking-card">
                    <h3>Book Your Adventure</h3>
                    <div class="info-tag-group">
                        <span class="info-tag"><i class="ri-walk-line"></i> <?php echo htmlspecialchars(ucfirst($trip['activity'])); ?></span>
                        <span class="info-tag"><i class="ri-bar-chart-line"></i> <?php echo htmlspecialchars(ucfirst($trip['difficulty'])); ?></span>
                        <span class="info-tag"><i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($trip['location']); ?></span>
                        <span class="info-tag"><i class="ri-calendar-line"></i> <?php echo $formatted_date; ?></span>
                        <span class="info-tag"><i class="ri-time-line"></i> <?php echo $formatted_time; ?></span>
                        <span class="info-tag"><i class="ri-group-line"></i> Up to <?php echo htmlspecialchars($trip['max_participants']); ?> participants</span>
                    </div>

                    <!-- *** الإضافة الثانية: عرض النموذج أو رسالة اكتمال العدد *** -->
                    <?php if ($available_spots > 0): ?>

                        <p style="text-align: center; font-weight: bold; color: #4CAF50; margin-top: 15px; margin-bottom: 10px;">
                            Only <?php echo $available_spots; ?> spots left!
                        </p>

                        <form action="book_trip.php" method="POST">
                            <input type="hidden" name="trip_name" value="<?php echo htmlspecialchars($trip['name']); ?>">
                            <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($trip['id']); ?>">
                            <input type="hidden" name="total_price" id="hidden_total_price">
                            <input type="hidden" id="base_price_per_person" value="<?php echo $trip['price']; ?>">
                            <div class="form-group">
                                <label for="num-people">Number of People</label>
                                <input type="number" id="num-people" name="num_people" value="1" min="1" max="<?php echo $available_spots; ?>">
                            </div>
                            <div class="form-group">
                                <label for="transport-option">Meeting & Transportation</label>
                                <select id="transport-option" name="meeting_point" class="filter-select">
                                    <?php foreach ($transport_options as $option): ?>
                                        <option value="<?php echo htmlspecialchars($option['text']); ?>" data-cost="<?php echo htmlspecialchars($option['value']); ?>"><?php echo htmlspecialchars($option['text']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="price-summary"><span>Total Price:</span><span id="total-price" class="total-price-value">₪<?php echo $trip['price']; ?></span></div>
                            <button type="submit" class="btn btn-primary btn-full">Confirm Booking</button>
                        </form>

                    <?php else: ?>

                        <div class="fully-booked-message" style="text-align: center; padding: 20px; margin-top: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0;">Sorry, this trip is fully booked!</h4>
                            <p style="margin: 0;">Please check our other amazing trips.</p>
                        </div>

                    <?php endif; ?>
                    <!-- *** نهاية الإضافة *** -->

                </div>
            </aside>
        </div>
    </section>
</main>

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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const numPeopleInput = document.getElementById('num-people');
        const transportSelect = document.getElementById('transport-option');
        if(numPeopleInput && transportSelect) {
            const basePrice = parseFloat(document.getElementById('base_price_per_person').value);
            const totalPriceSpan = document.getElementById('total-price');
            const hiddenTotalPriceInput = document.getElementById('hidden_total_price');

            function updateTotalPrice() {
                const numPeople = parseInt(numPeopleInput.value) || 1;
                const selectedTransportOption = transportSelect.options[transportSelect.selectedIndex];
                const transportCostPerPerson = parseFloat(selectedTransportOption.dataset.cost) || 0;
                const total = (basePrice + transportCostPerPerson) * numPeople;
                totalPriceSpan.textContent = `₪${total.toFixed(2)}`;
                hiddenTotalPriceInput.value = total.toFixed(2);
            }

            numPeopleInput.addEventListener('input', updateTotalPrice);
            transportSelect.addEventListener('change', updateTotalPrice);
            updateTotalPrice();
        }
    });
</script>
</body>
</html>