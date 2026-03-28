<?php
// تضمين الهيدر الذي يبدأ الجلسة
include 'admin_header.php';

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "waad", "waad");
$conn->set_charset("utf8mb4");

// -------------------------------------------------------------------
// 1. معالجة الإجراءات (فقط الحذف الآن)
// -------------------------------------------------------------------
$action = $_GET['action'] ?? 'view';
$review_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// معالجة الحذف
if ($action == 'delete' && $review_id) {
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review_id);
    $stmt->execute();
    header("Location: manage_reviews.php?message=deleted"); // إعادة توجيه
    exit; // إيقاف التنفيذ
}

// -------------------------------------------------------------------
// 2. الآن نبدأ بعرض الصفحة
// -------------------------------------------------------------------

// جلب البيانات للعرض (تم حذف is_approved من هنا لأنه لم يعد ضرورياً)
$result = $conn->query("SELECT id, user_name, rating, comment, created_at FROM reviews ORDER BY created_at DESC");
include 'admin_sidebar.php';
?>
<main class="admin-main-content">
    <div class="content-header"><h1>Manage Reviews</h1><a href="logout.php" class="logout-link">Logout</a></div>

    <?php
    // إظهار رسالة نجاح إذا تم الحذف
    if (isset($_GET['message']) && $_GET['message'] == 'deleted') {
        echo '<div class="alert alert-success">Review has been deleted successfully.</div>';
    }
    ?>

    <table class="content-table">
        <thead>
        <tr>
            <th>Author</th>
            <th>Rating</th>
            <th>Comment</th>
            <!-- تم حذف عمود الحالة "Status" -->
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["user_name"]); ?></td>
                    <td><?php echo str_repeat('⭐', $row['rating']); ?></td>
                    <td style="max-width: 400px;"><?php echo htmlspecialchars($row["comment"]); ?></td>
                    <!-- تم حذف خانة الحالة "Status" من هنا -->
                    <td>
                        <!-- تم حذف زر الموافقة/إلغاء الموافقة -->
                        <a href="manage_reviews.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-red" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <!-- تم تحديث colspan ليناسب عدد الأعمدة الجديد (4) -->
            <tr><td colspan="4">No reviews found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</div>
<?php $conn->close(); ?>
</body>
</html>