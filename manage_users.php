<?php
include 'admin_header.php';
$conn = new mysqli("localhost", "root", "waad", "waad");
$conn->set_charset("utf8mb4");

$action = $_GET['action'] ?? 'view';
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// معالجة الحذف
if ($action == 'delete' && $user_id) {
    // منع الأدمن من حذف حسابه بالخطأ
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
    header("Location: manage_users.php");
    exit;
}

// معالجة تغيير الدور (user <-> admin)
if ($action == 'toggle_role' && $user_id) {
    // منع الأدمن من تغيير دوره بنفسه
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("UPDATE user SET role = IF(role='admin', 'user', 'admin') WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
    header("Location: manage_users.php");
    exit;
}

$result = $conn->query("SELECT id, name, email, role FROM user ORDER BY id DESC");
include 'admin_sidebar.php';
?>
<main class="admin-main-content">
    <div class="content-header"><h1>Manage Users</h1><a href="logout.php" class="logout-link">Logout</a></div>

    <table class="content-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($row["role"])); ?></td>
                    <td>
                        <?php if ($row['id'] != $_SESSION['user_id']): // لا تظهر الأزرار للأدمن الحالي ?>
                            <a href="manage_users.php?action=toggle_role&id=<?php echo $row['id']; ?>" class="btn btn-blue">
                                Make <?php echo ($row['role'] == 'user' ? 'Admin' : 'User'); ?>
                            </a>
                            <a href="manage_users.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-red" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        <?php else: ?>
                            (Your Account)
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</div>
<?php $conn->close(); ?>
</body>
</html>