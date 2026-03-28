<?php
include 'admin_header.php'; // يتضمن التحقق من الجلسة والتصميم

// 1. الاتصال بقاعدة البيانات
$servername = "localhost"; $username = "root"; $password = "waad"; $dbname = "waad";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

// ===================================================================
//  الجزء الأول: معالجة الطلبات (إضافة، تعديل، حذف) - (تم نقله للأعلى)
// ===================================================================

$action = $_GET['action'] ?? 'view'; // القيمة الافتراضية هي 'view' لعرض الجدول
$trip_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// ---- معالجة حفظ (إضافة أو تعديل) رحلة ----
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_trip'])) {
    // جلب كل البيانات من النموذج
    $name = $_POST['name'];
    $tagline = $_POST['tagline'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $trip_date = $_POST['trip_date'];
    $max_participants = $_POST['max_participants'];
    $region = $_POST['region'];
    $activity = $_POST['activity'];
    $difficulty = $_POST['difficulty'];
    $trip_id_to_save = $_POST['trip_id'];
    $image_url = $_POST['existing_image_url'] ?? '';

    // معالجة رفع الصورة
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    // تحديد إذا كان الاستعلام إضافة أم تحديث
    if (empty($trip_id_to_save)) {
        // إضافة جديدة
        $sql = "INSERT INTO trips (name, tagline, description, price, location, trip_date, max_participants, image_url, region, activity, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdssissss", $name, $tagline, $description, $price, $location, $trip_date, $max_participants, $image_url, $region, $activity, $difficulty);
    } else {
        // تحديث موجود
        $sql = "UPDATE trips SET name=?, tagline=?, description=?, price=?, location=?, trip_date=?, max_participants=?, image_url=?, region=?, activity=?, difficulty=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdssissssi", $name, $tagline, $description, $price, $location, $trip_date, $max_participants, $image_url, $region, $activity, $difficulty, $trip_id_to_save);
    }
    $stmt->execute();
    header("Location: manage_trips.php?status=saved"); // إعادة توجيه بعد الحفظ
    exit;
}

// ---- حذف رحلة ----
if ($action == 'delete' && $trip_id) {
    // يمكنك إضافة كود لحذف الصورة من السيرفر هنا إذا أردت
    $stmt = $conn->prepare("DELETE FROM trips WHERE id = ?");
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    header("Location: manage_trips.php?status=deleted"); // إعادة توجيه بعد الحذف
    exit;
}


// ===================================================================
//  الجزء الثاني: عرض محتوى الصفحة (الجدول أو النموذج)
// ===================================================================
include 'admin_sidebar.php';
?>

<main class="admin-main-content">
    <div class="content-header">
        <h1>Manage Trips</h1>
    </div>

    <?php
    // إظهار رسالة نجاح إذا تم الحفظ أو الحذف
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'saved') {
            echo '<div class="alert alert-success">Trip has been saved successfully.</div>';
        } elseif ($_GET['status'] == 'deleted') {
            echo '<div class="alert alert-success">Trip has been deleted successfully.</div>';
        }
    }
    ?>

    <?php if ($action == 'add' || ($action == 'edit' && $trip_id)): ?>

        <?php
        // جلب بيانات الرحلة في حالة التعديل
        $trip = null;
        if ($action == 'edit') {
            $stmt = $conn->prepare("SELECT * FROM trips WHERE id = ?");
            $stmt->bind_param("i", $trip_id);
            $stmt->execute();
            $trip = $stmt->get_result()->fetch_assoc();
        }
        ?>
        <div class="form-container">
            <h3><?php echo $action == 'edit' ? 'Edit Trip' : 'Add New Trip'; ?></h3>
            <form action="manage_trips.php" method="POST" enctype="multipart/form-data" class="content-form">
                <input type="hidden" name="trip_id" value="<?php echo $trip['id'] ?? ''; ?>">
                <input type="hidden" name="existing_image_url" value="<?php echo $trip['image_url'] ?? ''; ?>">

                <div class="form-group"><label>Trip Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($trip['name'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Tagline (Short description)</label><input type="text" name="tagline" value="<?php echo htmlspecialchars($trip['tagline'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Full Description</label><textarea name="description" required><?php echo htmlspecialchars($trip['description'] ?? ''); ?></textarea></div>
                <div class="form-group"><label>Price</label><input type="number" name="price" step="0.01" value="<?php echo $trip['price'] ?? ''; ?>" required></div>
                <div class="form-group"><label>Location</label><input type="text" name="location" value="<?php echo htmlspecialchars($trip['location'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Date & Time</label><input type="datetime-local" name="trip_date" value="<?php echo !empty($trip['trip_date']) ? date('Y-m-d\TH:i', strtotime($trip['trip_date'])) : ''; ?>" required></div>
                <div class="form-group"><label>Max Participants</label><input type="number" name="max_participants" value="<?php echo $trip['max_participants'] ?? ''; ?>" required></div>

                <div class="form-group"><label>Region</label><select name="region" required><option value="" disabled selected>Select Region</option><option value="north" <?php echo ($trip['region'] ?? '') == 'north' ? 'selected' : ''; ?>>North</option><option value="center" <?php echo ($trip['region'] ?? '') == 'center' ? 'selected' : ''; ?>>Center</option><option value="south" <?php echo ($trip['region'] ?? '') == 'south' ? 'selected' : ''; ?>>South</option></select></div>
                <div class="form-group"><label>Activity</label><select name="activity" required><option value="" disabled selected>Select Activity</option><option value="hiking" <?php echo ($trip['activity'] ?? '') == 'hiking' ? 'selected' : ''; ?>>Hiking</option><option value="biking" <?php echo ($trip['activity'] ?? '') == 'biking' ? 'selected' : ''; ?>>Biking</option><option value="camping" <?php echo ($trip['activity'] ?? '') == 'camping' ? 'selected' : ''; ?>>Camping</option><option value="cultural" <?php echo ($trip['activity'] ?? '') == 'cultural' ? 'selected' : ''; ?>>Cultural</option></select></div>
                <div class="form-group"><label>Difficulty</label><select name="difficulty" required><option value="" disabled selected>Select Difficulty</option><option value="easy" <?php echo ($trip['difficulty'] ?? '') == 'easy' ? 'selected' : ''; ?>>Easy</option><option value="medium" <?php echo ($trip['difficulty'] ?? '') == 'medium' ? 'selected' : ''; ?>>Medium</option><option value="hard" <?php echo ($trip['difficulty'] ?? '') == 'hard' ? 'selected' : ''; ?>>Hard</option></select></div>

                <div class="form-group">
                    <label>Image (Leave empty to keep current image)</label>
                    <input type="file" name="image" accept="image/*">
                    <?php if (!empty($trip['image_url'])): ?><img src="<?php echo htmlspecialchars($trip['image_url']); ?>" alt="Current Image" style="max-width: 100px; margin-top: 10px;"><?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" name="save_trip" class="btn btn-green">Save Trip</button>
                    <a href="manage_trips.php" class="btn btn-grey">Cancel</a>
                </div>
            </form>
        </div>

    <?php else: // هذا هو الجزء الذي يعرض الجدول ?>

        <a href="manage_trips.php?action=add" class="btn btn-green" style="margin-bottom: 20px;">+ Add New Trip</a>
        <table class="content-table">
            <thead>
            <tr><th>ID</th><th>Image</th><th>Name</th><th>Location</th><th>Date</th><th>Price</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php $result = $conn->query("SELECT * FROM trips ORDER BY id DESC"); ?>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Trip" width="80"></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($row['trip_date'])); ?></td>
                        <td>₪<?php echo htmlspecialchars($row['price']); ?></td>
                        <td>
                            <a href="manage_trips.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-blue">Edit</a>
                            <a href="manage_trips.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-red" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No trips found. Add one!</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

    <?php endif; ?>
</main>
</div>
</body>
</html>