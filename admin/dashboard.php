<?php
require_once '../config.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../index.php');
}

// Get statistics
$stats = [];

// Total hikes
$stmt = $pdo->query("SELECT COUNT(*) as count FROM hikes");
$stats['hikes'] = $stmt->fetch()['count'];

// Total bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
$stats['bookings'] = $stmt->fetch()['count'];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$stats['users'] = $stmt->fetch()['count'];

// Total revenue
$stmt = $pdo->query("SELECT SUM(total_price) as total FROM bookings WHERE status != 'cancelled'");
$stats['revenue'] = $stmt->fetch()['total'] ?? 0;

// Recent bookings
$stmt = $pdo->query("
    SELECT b.*, h.name as hike_name 
    FROM bookings b 
    JOIN hikes h ON b.hike_id = h.id 
    ORDER BY b.created_at DESC 
    LIMIT 10
");
$recent_bookings = $stmt->fetchAll();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $booking_id]);
    
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">
                <img src="../images/logo.png" alt="HikeBook Cebu Logo">
                <?php echo SITE_NAME; ?>
            </a>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../hikes.php">Hikes</a></li>
                <li><a href="../profile.php">Profile</a></li>
                <li><a href="dashboard.php">Admin</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <?php echo sanitize($_SESSION['username']); ?>!</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $stats['hikes']; ?></h3>
                <p>Total Hikes</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['bookings']; ?></h3>
                <p>Total Bookings</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['users']; ?></h3>
                <p>Registered Users</p>
            </div>
            <div class="stat-card">
                <h3>₱<?php echo number_format($stats['revenue']); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="table-container">
            <div style="padding: 1.5rem; background: var(--primary); color: white;">
                <h3 style="margin: 0;">Recent Bookings</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hike</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Guests</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $booking): ?>
                        <tr>
                            <td>#<?php echo $booking['id']; ?></td>
                            <td><?php echo sanitize($booking['hike_name']); ?></td>
                            <td>
                                <?php echo sanitize($booking['customer_name']); ?><br>
                                <small style="color: var(--text-light);"><?php echo sanitize($booking['customer_email']); ?></small>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($booking['date'])); ?></td>
                            <td><?php echo $booking['guests']; ?></td>
                            <td><strong>₱<?php echo number_format($booking['total_price']); ?></strong></td>
                            <td>
                                <span class="meta-badge" style="background: <?php 
                                    echo $booking['status'] === 'confirmed' ? '#d4edda' : 
                                         ($booking['status'] === 'cancelled' ? '#f8d7da' : '#fff3cd'); 
                                ?>; color: <?php 
                                    echo $booking['status'] === 'confirmed' ? '#155724' : 
                                         ($booking['status'] === 'cancelled' ? '#721c24' : '#856404'); 
                                ?>;">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <select name="status" class="form-control" style="width: auto; display: inline-block; padding: 0.3rem;" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="manage_hikes.php" class="btn btn-primary">Manage Hikes</a>
            <a href="manage_bookings.php" class="btn btn-secondary">Manage All Bookings</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
