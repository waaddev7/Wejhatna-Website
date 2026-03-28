<?php
// تفعيل عرض الأخطاء للمساعدة في التصحيح
ini_set('display_errors', 1);
error_reporting(E_ALL);

// إخبار المتصفح أن الرد سيكون بصيغة JSON
header('Content-Type: application/json');

// مصفوفة لتخزين الرد
$response = [];

// إعدادات الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "waad";
$dbname = "waad";

// وضع الكود بأكمله داخل كتلة try...catch للتحكم الكامل بالأخطاء
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $password_from_form = isset($_POST['password']) ? $_POST['password'] : null;

        if (empty($name) || empty($email) || empty($password_from_form)) {
            throw new Exception('Please fill in all required fields.');
        }

        $hashed_password = password_hash($password_from_form, PASSWORD_DEFAULT);

        $insert_stmt = $conn->prepare("INSERT INTO user (name, email, password) VALUES (?, ?, ?)");

        // التحقق إذا كان تحضير الاستعلام قد فشل
        if ($insert_stmt === false) {
            throw new Exception("Error preparing statement.");
        }

        $insert_stmt->bind_param("sss", $name, $email, $hashed_password);

        // محاولة تنفيذ الاستعلام
        if ($insert_stmt->execute()) {
            // في حالة النجاح
            $response['status'] = 'success';
            $response['message'] = 'Account created successfully! Please sign in.';
        } else {
            // في حالة الفشل، نترك قاعدة البيانات تولد الخطأ وسنلتقطه في الـ catch
            // هذا هو أفضل مكان للتعرف على الخطأ الحقيقي
            throw new Exception($insert_stmt->error);
        }
        $insert_stmt->close();
    } else {
        throw new Exception('Invalid request method.');
    }
    $conn->close();

} catch (Exception $e) {
    // ==========================================================
    // هذا هو الجزء الأهم والمعدل
    // هنا نلتقط أي خطأ يحدث ونقرر ما هي الرسالة المناسبة
    // ==========================================================
    $response['status'] = 'error';

    // نستخدم strpos للبحث عن نص "Duplicate entry" في رسالة الخطأ
    // هذه الطريقة تعمل على جميع إصدارات PHP
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        // إذا وجدنا النص، فهذا يعني أن الإيميل مكرر
        $response['message'] = 'This email is already registered. Please use a different one.';
    } else {
        // لأي خطأ آخر، نعرض رسالة عامة
        $response['message'] = 'An unexpected error occurred. Please try again.';
        // يمكنك تسجيل الخطأ الحقيقي للمطورين هكذا:
        // error_log($e->getMessage());
    }
}

// طباعة الرد النهائي بصيغة JSON
echo json_encode($response);
?>