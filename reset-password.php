<?php
// --- الخطوة 1: الاتصال بقاعدة البيانات ---
$servername = "localhost"; $username = "root"; $password = "waad"; $dbname = "waad";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Database connection failed."); }

// --- الخطوة 2: التحقق من وجود الـ Token في الرابط ---
if (!isset($_GET['token'])) {
    die("Token not found. Please use the link from the email.");
}

$token = $_GET['token'];
$token_hash = hash("sha256", $token);

$stmt = $conn->prepare("SELECT id, reset_token_expires_at FROM user WHERE reset_token_hash = ?");
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("Invalid token. It may have already been used or expired.");
}

if (strtotime($user['reset_token_expires_at']) <= time()) {
    die("Token has expired. Please request a new one.");
}

// --- الخطوة 3: معالجة النموذج عند إدخال كلمة مرور جديدة ---
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['password']) || empty($_POST['password_confirm'])) {
        $message = '<p style="color: red;">Please fill in both password fields.</p>';
    } elseif ($_POST['password'] !== $_POST['password_confirm']) {
        $message = '<p style="color: red;">Passwords do not match.</p>';
    } else {
        $new_password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $update_stmt = $conn->prepare("UPDATE user SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?");
        $update_stmt->bind_param("si", $new_password_hash, $user['id']);
        $update_stmt->execute();

        // عرض رسالة نجاح جميلة بدلاً من النموذج
        echo <<<HTML
            <!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Success</title>
            <link rel="stylesheet" href="stylesignin.css">
            <style>body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f6f5f7; font-family: 'Montserrat', sans-serif; } 
            .response-container { background-color: #fff; padding: 40px 50px; border-radius: 10px; box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22); text-align: center; 
            max-width: 500px; } .response-container 
            h1 { font-weight: bold; } .response-container a {
             color: #333; font-size: 14px; 
            text-decoration: none; border: 1px solid #ddd;
             padding: 10px 20px; border-radius: 20px; 
             margin-top: 20px; display: inline-block; }</style>
            </head><body><div class="response-container"><h1>Success!</h1>
            <p>Your password has been updated. You can now 
            <a style="  background-color:   rgba(92, 115, 69, 0.86);;
    color: #fff;" href='sign.html'>Back to sign in
             with your new password</a>.</p></div></body></html>
HTML;
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password</title>
    <link rel="stylesheet" href="stylesignin.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f6f5f7; }
        .reset-container { background-color: #fff; padding: 40px 50px; border-radius: 10px; box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22); width: 100%; max-width: 480px; text-align: center; font-family: 'Montserrat', sans-serif; }
        .reset-container h1 { font-weight: bold; margin-bottom: 20px; }
        .reset-container input { background-color: #eee; border: none; padding: 12px 15px; margin: 8px 0; width: 100%; box-sizing: border-box; border-radius: 5px; }
        .reset-container button { border-radius: 20px;
            border: 1px solid #5d7a57; background-color: #5d7a57;
            color: #FFFFFF; font-size: 12px; font-weight: bold;
            padding: 12px 45px; letter-spacing: 1px;
            text-transform: uppercase; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>
<div class="reset-container">
    <h1>Set a New Password</h1>
    <?php echo $message; // لعرض رسائل الخطأ ?>
    <form method="post">
        <div><input type="password"
                    name="password" placeholder="New Password"
                    required></div>
        <div><input type="password" name="password_confirm"
                    placeholder="Confirm New Password" required></div>
        <button type="submit">Update Password</button>
    </form>
</div>
</body>
</html>