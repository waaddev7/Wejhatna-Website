<?php
// يتضمن الهيدر الذي يحتوي على التحقق من الأدمن والتصميم
include 'admin_header.php';

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "waad", "waad");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

// تحديد الإجراء المطلوب (عرض أو حذف)
$action = $_GET['action'] ?? 'view';
$contact_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// --- معالجة طلب الحذف ---
if ($action == 'delete' && $contact_id) {
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $contact_id);
    $stmt->execute();
    $stmt->close();
    // إعادة التوجيه لنفس الصفحة لتحديث الجدول بعد الحذف
    header("Location:manage_contacts.php");
    exit;
}

// --- جلب كل الرسائل من قاعدة البيانات لعرضها ---
$result = $conn->query("SELECT id, name, email, subject, message, received_at FROM contacts ORDER BY received_at DESC");

// استدعاء القائمة الجانبية
include 'admin_sidebar.php';
?>

<!-- ======================= المحتوى الرئيسي للصفحة ======================= -->
<main class="admin-main-content">
    <div class="content-header">
        <h1>Contact Messages</h1>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <table class="content-table">
        <thead>
        <tr>
            <th>From</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Received</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                    <td><a href="mailto:<?php echo htmlspecialchars($row["email"]); ?>"><?php echo htmlspecialchars($row["email"]); ?></a></td>
                    <td><?php echo htmlspecialchars($row["subject"]); ?></td>
                    <td style="max-width: 300px; white-space: pre-wrap; word-break: break-word;"><?php echo htmlspecialchars($row["message"]); ?></td>
                    <td><?php echo date('d M Y, h:i A', strtotime($row['received_at'])); ?></td>
                    <td>
                        <a href="manage_contacts.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-red" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No contact messages found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</div> <!-- إغلاق وسم .admin-layout من الهيدر -->

<?php $conn->close(); ?>
</body>
</html>