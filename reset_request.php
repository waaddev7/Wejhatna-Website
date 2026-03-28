<?php

// استدعاء مكتبة PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

// --- الاتصال بقاعدة البيانات ---
$servername = "localhost"; $username = "root"; $password = "waad"; $dbname = "waad";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Database connection failed."); }

// --- الرسالة الافتراضية للنجاح ---
// سنقوم بتصميم صفحة HTML كاملة للرد
$responseTitle = "Request Received";
$responseMessage = "If an account with that email exists, a password reset link has been sent. Please check your inbox and spam folder.";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // --- فقط إذا وجدنا الإيميل، نقوم بعملية الإرسال ---

        if (function_exists('random_bytes')) {
            $token = bin2hex(random_bytes(32));
        } else {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
        }// لنفترض أن إصدار PHP 7+ يعمل لديك الآن
        $token_hash = hash("sha256", $token);
        $expiry_date = date("Y-m-d H:i:s", time() + 3600);

        $update_stmt = $conn->prepare("UPDATE user SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?");
        $update_stmt->bind_param("sss", $token_hash, $expiry_date, $email);
        $update_stmt->execute();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'wejhatna@gmail.com'; //  <<< إيميلك الصحيح
            $mail->Password   = 'jttafdgykpsziyay';   //  <<< كلمة مرور التطبيقات الصحيحة
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('wejhatna@gmail.com', 'Wejhatna Support');
            $mail->addAddress($email);

            $reset_link = "http://localhost/reset-password.php?token=" . $token;
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body  = "Hello,<br><br>
Click the link below to reset your password:<br><a href='{$reset_link}'>
Reset Password</a>";

            $mail->send();

        } catch (Exception $e) {
            // إذا فشل الإرسال، نغير رسالة الرد
            $responseTitle = "Error";
            $responseMessage = "Message could not be sent. Please try again later.";
            // يمكنك إضافة $mail->ErrorInfo هنا إذا أردتِ رؤية الخطأ الفني
        }
    }
}

// --- طباعة صفحة الـ HTML النهائية كنتيجة ---
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$responseTitle</title>
    <link rel="stylesheet" href="stylesignin.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f6f5f7; font-family: 'Montserrat', sans-serif; }
        .response-container { background-color: #fff; padding: 40px 50px; border-radius: 10px; box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22); text-align: center; max-width: 500px; }
        .response-container h1 { font-weight: bold; margin-bottom: 15px; }
        .response-container p { margin-bottom: 25px; }
        .response-container a { color: #333; font-size: 14px; text-decoration: none; border: 1px solid #ddd; padding: 10px 20px; border-radius: 20px; }
    </style>
</head>
<body>
    <div class="response-container">
        <h1>$responseTitle</h1>
        <p>$responseMessage</p>
        <a href="sign.html">Back to Sign In</a>
    </div>
</body>
</html>
HTML;
?>