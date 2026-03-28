<?php
header('Content-Type: application/json');
session_start();

$servername = "localhost";
$username = "root";
$password = "waad";
$dbname = "waad";

$response = [];

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed.");
    }
    $conn->set_charset("utf8mb4");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // --- تحديد نوع العملية: تسجيل دخول أم إنشاء حساب ---
        // سنعتمد على وجود حقل 'name' للتمييز
        if (isset($_POST['name'])) {
            // ===========================================
            //      هذا هو قسم إنشاء حساب (Sign Up)
            // ===========================================
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password_plain = trim($_POST['password']);

            if (empty($name) || empty($email) || empty($password_plain)) {
                throw new Exception("Please fill in all fields for sign up.");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format.");
            }
            if (strlen($password_plain) < 6) {
                throw new Exception("Password must be at least 6 characters.");
            }

            // التحقق إذا كان الإيميل مستخدماً
            $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("This email is already registered.");
            }

            // تشفير كلمة المرور وإدخال المستخدم
            $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password_hashed);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Account created successfully! You can now sign in.';
            } else {
                throw new Exception("Error creating account.");
            }

        } else {
            // ===========================================
            //      هذا هو قسم تسجيل الدخول (Sign In)
            // ===========================================
            if (empty($_POST['email']) || empty($_POST['password'])) {
                throw new Exception("Please fill in all fields for sign in.");
            }
            $email = $_POST['email'];
            $password_from_form = $_POST['password'];

            $stmt = $conn->prepare("SELECT id, name, email, password, role FROM user WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password_from_form, $user['password'])) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];

                    $response['status'] = 'success';
                    $response['message'] = 'Login successful! Welcome back, ' . htmlspecialchars($user['name']);

                    if (isset($_SESSION['pending_booking'])) {
                        $response['redirectUrl'] = 'book_trip.php';
                    } else if ($user['role'] === 'admin') {
                        $response['redirectUrl'] = 'admin_dashboard.php';
                    } else {
                        $response['redirectUrl'] = 'homee.php';
                    }
                } else {
                    throw new Exception("Incorrect email or password.");
                }
            } else {
                throw new Exception("Incorrect email or password.");
            }
        }
        $stmt->close();
    } else {
        throw new Exception("Invalid request method.");
    }
    $conn->close();
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>