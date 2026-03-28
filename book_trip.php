<?php
session_start();

// ===================================================================================
//  الجزء الأول: بدء الحجز والتحقق من توفر الأماكن
// ===================================================================================

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['trip_name'])) {

    // ▼▼▼ كود التحقق من توفر الأماكن (الإضافة الجديدة) ▼▼▼

    // الخطوة 1: جلب البيانات اللازمة للتحقق
    $trip_id = $_POST['trip_id'];
    $trip_name = $_POST['trip_name'];
    $num_people_requested = (int)$_POST['num_people'];

    // الخطوة 2: الاتصال بقاعدة البيانات للتحقق
    $servername = "localhost"; $username = "root"; $password = "waad"; $dbname = "waad";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) { die("Connection failed."); }
    $conn->set_charset("utf8mb4");

    // أولاً: جلب الحد الأقصى للمشاركين من جدول الرحلات
    $trip_sql = "SELECT max_participants FROM trips WHERE id = ?";
    $trip_stmt = $conn->prepare($trip_sql);
    $trip_stmt->bind_param("i", $trip_id);
    $trip_stmt->execute();
    $trip_result = $trip_stmt->get_result()->fetch_assoc();
    $max_participants = $trip_result['max_participants'];

    // ثانياً: حساب إجمالي عدد الأشخاص المحجوزين لهذه الرحلة
    $booking_sql = "SELECT SUM(num_people) AS total_booked FROM bookings WHERE trip_name = ?";
    $booking_stmt = $conn->prepare($booking_sql);
    $booking_stmt->bind_param("s", $trip_name);
    $booking_stmt->execute();
    $booking_result = $booking_stmt->get_result()->fetch_assoc();
    $total_booked = (int)($booking_result['total_booked'] ?? 0);

    $conn->close();

    // ثالثاً: حساب الأماكن المتاحة واتخاذ القرار
    $available_spots = $max_participants - $total_booked;

    if ($num_people_requested <= $available_spots) {
        // **الحالة الناجحة: توجد أماكن كافية**
        // نكمل الكود الأصلي: تخزين البيانات في الجلسة والتوجيه للدفع

        $_SESSION['booking_details'] = [
            'trip_name' => $_POST['trip_name'],
            'num_people' => $_POST['num_people'],
            'meeting_point' => $_POST['meeting_point'],
            'total_price' => $_POST['total_price'],
        ];

        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("Location: sign.html?redirect=pay.php");
            exit;
        } else {
            header("Location: pay.php");
            exit;
        }

    } else {
        // **الحالة الفاشلة: لا توجد أماكن كافية**
        // إعادة توجيه المستخدم لصفحة التفاصيل مع رسالة خطأ
        header("Location: trip-details.php?id=" . $trip_id . "&error=not_enough_spots&spots=" . $available_spots);
        exit;
    }
    // ▲▲▲ نهاية كود التحقق ▲▲▲
}

// ===================================================================================
//  الجزء الثاني: إتمام الدفع والحجز (يبقى كما هو)
// ===================================================================================

elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cardholder-name'])) {

    if (!isset($_SESSION['loggedin']) || !isset($_SESSION['booking_details'])) {
        header("Location: trips.php?error=session_expired");
        exit;
    }

    $booking_data = $_SESSION['booking_details'];
    $user_id = $_SESSION['user_id'];
    $user_email = $_SESSION['user_email'];
    $user_name = $_SESSION['user_name'];

    $trip_name = $booking_data['trip_name'];
    $num_people = $booking_data['num_people'];
    $meeting_point = $booking_data['meeting_point'];
    $total_price = $booking_data['total_price'];

    $cardholder_name = htmlspecialchars($_POST['cardholder-name']);
    $phone_number = htmlspecialchars($_POST['phone-number']);
    $card_last_four = substr(str_replace(' ', '', $_POST['card-number']), -4);
    $card_expiry = $_POST['expiry-month'] . '/' . $_POST['expiry-year'];

    $servername = "localhost"; $username = "root"; $password = "waad"; $dbname = "waad";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) { die("Connection failed."); }
    $conn->set_charset("utf8mb4");

    $sql = "INSERT INTO bookings (user_id, trip_name, num_people, meeting_point, total_price, cardholder_name, phone_number, card_last_four, card_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isissssss", $user_id, $trip_name, $num_people, $meeting_point, $total_price, $cardholder_name, $phone_number, $card_last_four, $card_expiry);

    if ($stmt->execute()) {
        $pageTitle = "Booking Confirmed!";
        $message = "Thank you, " . htmlspecialchars($user_name) . "! Your adventure to '" . htmlspecialchars($trip_name) . "' is confirmed. Get ready!";
        unset($_SESSION['booking_details']);

        require 'src/Exception.php'; require 'src/PHPMailer.php'; require 'src/SMTP.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $masked_card_number = "XXXX XXXX XXXX " . $card_last_four;

        try {
            $mail->isSMTP(); $mail->Host = 'smtp.gmail.com'; $mail->SMTPAuth = true;
            $mail->Username = 'wejhatna@gmail.com'; $mail->Password = 'jttafdgykpsziyay';
            $mail->SMTPSecure = 'tls'; $mail->Port = 587;
            $mail->setFrom('wejhatna@gmail.com', 'Wejhatna Bookings');
            $mail->addAddress($user_email, $user_name);
            $mail->isHTML(true); $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Your Wejhatna Adventure is Confirmed!';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
                    <h2>Hello " . htmlspecialchars($user_name) . ",</h2>
                    <p>Your booking for the <strong>" . htmlspecialchars($trip_name) . "</strong> trip is confirmed. Here are your details:</p>
                    <h3>Booking Details:</h3>
                    <ul>
                        <li><strong>Trip:</strong> " . htmlspecialchars($trip_name) . "</li>
                        <li><strong>Number of People:</strong> " . htmlspecialchars($num_people) . "</li>
                        <li><strong>Meeting Point:</strong> " . htmlspecialchars($meeting_point) . "</li>
                        <li><strong>Total Price:</strong> ₪" . htmlspecialchars($total_price) . "</li>
                    </ul>
                    <h3>Payment Information:</h3>
                    <ul>
                        <li><strong>Cardholder Name:</strong> " . $cardholder_name . "</li>
                        <li><strong>Phone Number:</strong> " . $phone_number . "</li>
                        <li><strong>Card Used:</strong> " . $masked_card_number . "</li>
                        <li><strong>Expiry Date:</strong> " . $card_expiry . "</li>
                    </ul>
                    <p>Happy trails,<br><strong>The Wejhatna Team</strong></p>
                </div>";
            $mail->send();
            $message .= "<br>A confirmation email with all details has been sent to you.";
        } catch (Exception $e) {
            $message .= "<br><br><strong style='color:red;'>WARNING: Your booking was saved, but the email could not be sent. Mailer Error: {$mail->ErrorInfo}</strong>";
        }

    } else {
        $pageTitle = "Booking Failed";
        $message = "Sorry, there was an error saving your booking. Error: " . $stmt->error;
    }

    $stmt->close(); $conn->close();

    echo <<<HTML
    <!DOCTYPE html><html><head><title>$pageTitle</title>
    <style>body { font-family: 'Roboto', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; margin: 0; } .response-container { background-color: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); text-align: center; max-width: 450px; width: 90%; } h1 { font-size: 2.2em; font-weight: 900; color: #1c1e21; margin-top: 0; margin-bottom: 15px; } p { color: #606770; line-height: 1.6; font-size: 1.1em; margin-bottom: 30px; } a { background-color: rgba(92, 115, 69, 0.86); color: #FFFFFF; text-decoration: none; padding: 14px 30px; border-radius: 8px; font-weight: 700; transition: background-color 0.3s; } a:hover { background-color: rgba(61,83,48,0.86); }</style>
    </head><body><div class="response-container"><h1>$pageTitle</h1><p>$message</p><a href="trips.php">Back to Trips</a></div></body></html>
HTML;
    exit;
}

// ===================================================================================
//  الجزء الثالث: حالة الطوارئ (يبقى كما هو)
// ===================================================================================
else {
    header("Location: trips.php");
    exit;
}
?>