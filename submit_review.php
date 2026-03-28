<?php
// ابدأ الجلسة للوصول إلى بيانات المستخدم المسجل إن وجدت
session_start();

// إعداد الاستجابة كـ JSON للتعامل معها في الواجهة الأمامية
header('Content-Type: application/json');
$response = [];

// التحقق من أن الطلب هو POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. جلب البيانات من النموذج وتنظيفها ---
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = trim($_POST['comment'] ?? '');

    // الحصول على ID المستخدم من الجلسة إذا كان مسجلاً دخوله
    $user_id = $_SESSION['user_id'] ?? null;

    // --- 2. التحقق من صحة البيانات ---
    if (empty($name) || empty($email) || empty($rating) || empty($comment)) {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all the required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = 'Please provide a valid email address.';
    } elseif ($rating < 1 || $rating > 5) {
        $response['status'] = 'error';
        $response['message'] = 'Please select a rating between 1 and 5.';
    } else {
        // --- 3. إذا كانت البيانات صالحة، قم بالحفظ في قاعدة البيانات ---
        try {
            $servername = "localhost"; $username = "root"; $password = "waad"; $dbname = "waad";
            $conn = new mysqli($servername, $username, $password, $dbname);
            $conn->set_charset("utf8mb4");

            // جملة SQL المحدثة لتشمل كل الأعمدة الجديدة
            $sql = "INSERT INTO reviews (user_id, user_name, email, rating, comment) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            // ربط المتغيرات (i for integer, s for string)
            // لاحظ أن user_id يمكن أن يكون null، وهو أمر مسموح به في قاعدة البيانات
            $stmt->bind_param("issis", $user_id, $name, $email, $rating, $comment);

            if ($stmt->execute()) {
                // نجح الحفظ
                $response['status'] = 'success';
                $response['message'] = 'Thank you for your review! It will be published after moderation.';
            } else {
                // فشل التنفيذ
                throw new Exception("Database execution failed.");
            }
            $stmt->close();
            $conn->close();

        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = 'An unexpected error occurred. Please try again later.';
            // للمطور: يمكنك تسجيل الخطأ الفعلي لرؤيته لاحقاً
            // error_log("Review submission error: " . $e->getMessage());
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

// --- 4. طباعة الاستجابة بصيغة JSON ---
echo json_encode($response);
?>