<?php
session_start();
// حارس الأمن: تأكد من أن المستخدم هو أدمن وأن الطلب هو POST
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: sign.html');
    exit;
}

// 1. الاتصال بقاعدة البيانات
$servername = "localhost"; $username = "root"; $password = "waad"; $dbname = "waad";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. معالجة رفع الصورة
$target_dir = "uploads/"; // المجلد الذي أنشأناه
// ننشئ اسماً فريداً للصورة لمنع تكرار الأسماء
$image_name = uniqid() . '_' . basename($_FILES["trip_image"]["name"]);
$target_file = $target_dir . $image_name;

// محاولة نقل الملف المرفوع إلى مجلد uploads
if (move_uploaded_file($_FILES["trip_image"]["tmp_name"], $target_file)) {
    // نجح رفع الصورة، لنكمل حفظ البيانات

    // 3. جلب البيانات من النموذج
    $trip_name = $_POST['trip_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $trip_date = $_POST['trip_date'];
    $max_participants = $_POST['max_participants'];
    $image_url = $target_file; // مسار الصورة الذي سيتم حفظه في القاعدة

    // 4. استخدام Prepared Statements لمنع SQL Injection (أكثر أماناً)
    $sql = "INSERT INTO trips (name, description, price, location, trip_date, max_participants, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // "ssdssis" -> s: string, d: double, i: integer
    $stmt->bind_param("ssdssis", $trip_name, $description, $price, $location, $trip_date, $max_participants, $image_url);

    // 5. تنفيذ الاستعلام وعرض رسالة
    if ($stmt->execute()) {
        echo "<h1>Success!</h1>";
        echo "<p>The new trip '" . htmlspecialchars($trip_name) . "' has been added successfully.</p>";
        echo '<a href="admin_header.php">Add another trip</a> or <a href="admin_dashboard.php">Go to Dashboard</a>';
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

} else {
    // فشل رفع الصورة
    echo "<h1>Error!</h1>";
    echo "<p>Sorry, there was an error uploading your file. Please try again.</p>";
    echo '<a href="admin_header.php">Try again</a>';
}

$conn->close();
?>