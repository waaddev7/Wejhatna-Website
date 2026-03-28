<?php
include 'admin_header.php';

// --- جلب الإحصائيات من قاعدة البيانات ---
$conn = new mysqli("localhost", "root", "waad", "waad");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// دالة مساعدة لتجنب تكرار الكود
function get_table_count($conn, $table_name) {
    // التحقق من وجود الجدول أولاً
    $check_table = $conn->query("SHOW TABLES LIKE '$table_name'");
    if ($check_table->num_rows > 0) {
        $result = $conn->query("SELECT COUNT(*) as total FROM `$table_name`");
        return $result->fetch_assoc()['total'];
    }
    return 0; // إرجاع صفر إذا لم يكن الجدول موجوداً
}

// جلب عدد كل شيء باستخدام الدالة
$user_count = get_table_count($conn, 'user');
$trip_count = get_table_count($conn, 'trips');
$booking_count = get_table_count($conn, 'bookings');
// يمكنك إضافة المزيد هنا مستقبلاً
 $review_count = get_table_count($conn, 'reviews');
 $contact_count = get_table_count($conn, 'contacts');

$conn->close();

include 'admin_sidebar.php';
?>

<!-- Main Content -->
<main class="admin-main-content">
    <div class="content-header">
        <h1>Dashboard</h1>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <p style="font-size: 1.1em; color: #555; margin-bottom: 30px;">
        Welcome back, <strong><?php echo
            htmlspecialchars($_SESSION['user_name']); ?></strong>!
        Here's a summary of your website.
    </p>

    <!-- بطاقات الإحصائيات -->
    <div class="stats-container">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?php echo $user_count; ?></p>
        </div>
        <div class="stat-card">
            <h3>Available Trips</h3>
            <p><?php echo $trip_count; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Bookings</h3>
            <p><?php echo $booking_count; ?></p>
        </div>
        <div class="stat-card">
            <h3>Pending Reviews</h3>
            <p><?php echo $review_count; ?></p>
        </div>
        <div class="stat-card">
            <h3>Contact Messages</h3>
            <p><?php echo $contact_count; ?></p>
        </div>
    </div>

    <!-- قسم الروابط السريعة -->
    <div class="quick-actions">
        <a href="manage_trips.php?action=add" class="btn btn-green">Add New Trip</a>
    </div>

</main>
</div> <!-- Close admin-layout -->

<!-- ▼▼▼ أضيفي هذا التنسيق إلى ملف admin_header.php داخل وسم <style> ▼▼▼ -->
<style>
    .stats-container {
       display: flex;
        justify-content: center;
        flex-wrap: wrap;
gap: 25px;
        margin-bottom: 30px;
    }
    .stat-card {
        background-color: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        text-align: center;
        border: 1px solid #eef0f2;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .stat-card h3 {
        margin-top: 0;
        color: #6c757d;
        font-size: 1em;
        font-weight: 500;
        text-transform: uppercase;
    }
    .stat-card p {
        margin-bottom: 0;
        font-size: 2.8em;
        font-weight: 700;
        color: #2c3e50;
    }
    .quick-actions {
        margin-top: 20px;
    }
</style>

</body>
</html>