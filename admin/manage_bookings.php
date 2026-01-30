<?php
require_once '../config.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../index.php');
}

// Get all bookings
$stmt = $pdo->query("
    SELECT b.*, h.name as hike_name, h.location 
    FROM bookings b 
    JOIN hikes h ON b.hike_id = h.id 
    ORDER BY b.created_at DESC
");
$bookings = $stmt->fetchAll();

// Handle delete
if (isset($_GET['delete'])) {
    $booking_id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$booking_id]);
    redirect('manage_bookings.php');
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $booking_id]);
    
    redirect('manage_bookings.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - <?php echo SITE_NAME; ?></title>
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
        <div class="section-title">
            <h2>Manage All Bookings</h2>
            <p><?php echo count($bookings); ?> total bookings</p>
        </div>

        <div style="margin-bottom: 2rem;">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>

        <div class="table-container">
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
                        <th>Booked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><strong>#<?php echo $booking['id']; ?></strong></td>
                            <td>
                                <?php echo sanitize($booking['hike_name']); ?><br>
                                <small style="color: var(--text-light);">📍 <?php echo sanitize($booking['location']); ?></small>
                            </td>
                            <td>
                                <?php echo sanitize($booking['customer_name']); ?><br>
                                <small style="color: var(--text-light);"><?php echo sanitize($booking['customer_email']); ?></small>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($booking['date'])); ?></td>
                            <td><?php echo $booking['guests']; ?></td>
                            <td><strong style="color: var(--accent);">₱<?php echo number_format($booking['total_price']); ?></strong></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <select name="status" class="form-control" style="width: auto; padding: 0.3rem;" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                            <td>
                                <a href="?delete=<?php echo $booking['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this booking?')"
                                   style="color: #dc3545; text-decoration: none; font-weight: 600;">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
