<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<aside class="admin-sidebar">
    <div class="sidebar-header" style="font-family: 'Pacifico', cursive;" ><h2>Wejhatna</h2></div>
    <nav><ul>
            <li><a href="admin_dashboard.php" class="<?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="manage_trips.php" class="<?php echo ($current_page == 'manage_trips.php')
                    ? 'active' : ''; ?>">Manage Trips</a></li>
            <li><a href="manage_bookings.php" class="<?php echo
                ($current_page == 'manage_bookings.php') ? 'active' : ''; ?>">
                    Manage Bookings</a></li>
            <li><a href="manage_users.php" class="<?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>">Manage Users</a></li>
            <li><a href="manage_reviews.php" class="<?php echo ($current_page == 'manage_reviews.php') ? 'active' : ''; ?>">Manage Reviews</a></li>
            <li><a href="manage_contacts.php" class="<?php echo ($current_page == 'manage_contacts.php') ? 'active' : ''; ?>">Contact Messages</a></li>
        </ul></nav>
</aside>