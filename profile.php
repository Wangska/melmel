<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Determine role
$is_admin = isAdmin();

// Get user info first
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user bookings using the user's email from database (for regular users only)
$bookings = [];
if (!$is_admin) {
    $stmt = $pdo->prepare("
        SELECT b.*, h.name as hike_name, h.location, h.image_url 
        FROM bookings b 
        JOIN hikes h ON b.hike_id = h.id 
        WHERE b.customer_email = ? 
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user['email']]);
    $bookings = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <img src="images/logo.png" alt="HikeBook Cebu Logo">
                <?php echo SITE_NAME; ?>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="hikes.php">Explore</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="profile.php">Profile</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="<?php echo base_url('admin/dashboard.php'); ?>">Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="section-title">
            <h2>My Profile</h2>
            <p>
                <?php echo $is_admin ? 'Admin account overview and quick tools' : 'Manage your account and upcoming hikes'; ?>
            </p>
        </div>

        <!-- User Info -->
        <div class="form-container" style="margin-top: 0; margin-bottom: 2rem;">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">Account Information</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div>
                    <strong style="color: var(--text-light);">Username</strong>
                    <p style="font-size: 1.1rem; margin-top: 0.3rem;"><?php echo sanitize($user['username']); ?></p>
                </div>
                <div>
                    <strong style="color: var(--text-light);">Email</strong>
                    <p style="font-size: 1.1rem; margin-top: 0.3rem;"><?php echo sanitize($user['email']); ?></p>
                </div>
                <div>
                    <strong style="color: var(--text-light);">Role</strong>
                    <p style="font-size: 1.1rem; margin-top: 0.3rem;"><?php echo ucfirst(sanitize($user['role'])); ?></p>
                </div>
                <div>
                    <strong style="color: var(--text-light);">Member Since</strong>
                    <p style="font-size: 1.1rem; margin-top: 0.3rem;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
        </div>

        <?php if ($is_admin): ?>
            <!-- Admin profile panel (informational only) -->
            <div class="form-container" style="max-width: 900px; margin-top: 0;">
                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: 0.25rem;">Admin account overview</h3>
                        <p style="color: var(--text-light); max-width: 520px; margin: 0;">
                            This account is used to manage hikes, bookings, and users. Admins cannot book or rate hikes directly.
                        </p>
                    </div>
                    <span class="meta-badge" style="background:#e3f2fd; color:#1976d2; font-size:0.9rem;">
                        Role: Admin
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1.25rem;">
                    <div style="background: var(--bg-light); border-radius: 12px; padding: 1.25rem;">
                        <h4 style="color: var(--primary); margin-bottom: 0.5rem;">📊 Platform management</h4>
                        <p style="color: var(--text-light); margin: 0;">
                            Use the Admin link in the top navigation to open the dashboard and manage hikes, bookings, and user roles.
                        </p>
                    </div>
                    <div style="background: var(--bg-light); border-radius: 12px; padding: 1.25rem;">
                        <h4 style="color: var(--primary); margin-bottom: 0.5rem;">🎒 Booking as a hiker</h4>
                        <p style="color: var(--text-light); margin: 0;">
                            To experience the booking flow like a customer, log out and sign in with a standard user account.
                        </p>
                    </div>
                    <div style="background: var(--bg-light); border-radius: 12px; padding: 1.25rem;">
                        <h4 style="color: var(--primary); margin-bottom: 0.5rem;">✅ Best practice</h4>
                        <p style="color: var(--text-light); margin: 0;">
                            Keep admin accounts for configuration and moderation only, and avoid mixing them with test or demo bookings.
                        </p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Bookings (regular user) -->
            <div class="form-container" style="margin-top: 0;">
                <h3 style="color: var(--primary); margin-bottom: 1.5rem;">My Bookings (<?php echo count($bookings); ?>)</h3>
                
                <?php if (count($bookings) > 0): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Hike</th>
                                    <th>Date</th>
                                    <th>Guests</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Booked</th>
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
                                        <td><?php echo date('M d, Y', strtotime($booking['date'])); ?></td>
                                        <td><?php echo $booking['guests']; ?> person(s)</td>
                                        <td><strong style="color: var(--accent);">₱<?php echo number_format($booking['total_price']); ?></strong></td>
                                        <td>
                                            <span class="meta-badge" style="background: <?php 
                                                echo $booking['status'] === 'confirmed' ? '#d4edda' : 
                                                     ($booking['status'] === 'cancelled' ? '#f8d7da' : '#fff3cd'); 
                                            ?>; color: <?php 
                                                echo $booking['status'] === 'confirmed' ? '#155724' : 
                                                     ($booking['status'] === 'cancelled' ? '#721c24' : '#856404'); 
                                            ?>;">
                                                <?php echo ucfirst(sanitize($booking['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <p style="font-size: 1.2rem; color: var(--text-light); margin-bottom: 1.5rem;">You haven't made any bookings yet.</p>
                        <a href="hikes.php" class="btn btn-primary">Browse Hikes</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
