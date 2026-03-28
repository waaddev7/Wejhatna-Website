<?php
// تم حذف session_start() من هنا لأنها موجودة في admin_header.php

// تضمين ملف الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "waad", "waad");
$conn->set_charset("utf8mb4");

// -------------------------------------------------------------------
// الخطوة 1: معالجة الإجراءات (مثل الحذف) قبل طباعة أي شيء
// -------------------------------------------------------------------
$action = $_GET['action'] ?? 'view';
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// معالجة الحذف
if ($action == 'delete' && $booking_id) {
    // قم بإعداد وتنفيذ جملة الحذف
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    // بعد الحذف، أعد التوجيه ثم أوقف تنفيذ السكريبت
    header("Location: manage_bookings.php?status=deleted");
    exit; // مهم جداً لإيقاف الكود هنا
}


// -------------------------------------------------------------------
// الخطوة 2: إذا لم يتم الخروج من السكريبت، ابدأ بعرض الصفحة
// -------------------------------------------------------------------
include 'admin_header.php'; // تضمين الهيدر الآن آمن

// جملة SQL لجلب البيانات للعرض
$sql = "SELECT 
            b.id, b.trip_name, b.num_people, b.total_price, b.meeting_point, b.booking_date,
            u.name as user_name,
            b.cardholder_name, 
            b.phone_number, 
            b.card_last_four, 
            b.card_expiry
        FROM bookings b
        JOIN user u ON b.user_id = u.id
        ORDER BY b.booking_date DESC";
$result = $conn->query($sql);

include 'admin_sidebar.php';
?>

<main class="admin-main-content">
    <div class="content-header">
        <h1>Manage Bookings</h1>
    </div>

    <?php
    // إظهار رسالة نجاح إذا تم الحذف بنجاح
    if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
        echo '<div class="alert alert-success">Booking has been deleted successfully.</div>';
    }
    ?>

    <table class="content-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>User Name</th>
            <th>Trip Name</th>
            <th>People</th>
            <th>Price</th>
            <th>Meeting Point</th>
            <th>Booking Date</th>
            <th>Cardholder Name</th>
            <th>Phone</th>
            <th>Card (Last 4)</th>
            <th>Expiry</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row["user_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["trip_name"]); ?></td>
                    <td><?php echo $row['num_people']; ?></td>
                    <td>₪<?php echo htmlspecialchars($row['total_price']); ?></td>
                    <td><?php echo htmlspecialchars($row['meeting_point']); ?></td>
                    <td><?php echo date('d M Y, h:i A', strtotime($row['booking_date'])); ?></td>
                    <td><?php echo htmlspecialchars($row["cardholder_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["phone_number"]); ?></td>
                    <td><?php echo htmlspecialchars($row["card_last_four"]); ?></td>
                    <td><?php echo htmlspecialchars($row["card_expiry"]); ?></td>
                    <td>
                        <a href="manage_bookings.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-red" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="12">No bookings found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</div>
<?php $conn->close(); ?>
</body>
</html>