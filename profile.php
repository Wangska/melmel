<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user info first
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user bookings using the user's email from database
$stmt = $pdo->prepare("
    SELECT b.*, h.name as hike_name, h.location, h.image_url 
    FROM bookings b 
    JOIN hikes h ON b.hike_id = h.id 
    WHERE b.customer_email = ? 
    ORDER BY b.created_at DESC
");
$stmt->execute([$user['email']]);
$bookings = $stmt->fetchAll();
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
            <p>Manage your account and bookings</p>
        </div>

        <!-- User Info -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
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

        <!-- Bookings -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="color: var(--primary); margin-bottom: 1.5rem;">My Bookings (<?php echo count($bookings); ?>)</h3>
            
            <?php if (count($bookings) > 0): ?>
                <div style="display: grid; gap: 1.5rem;">
                    <?php foreach ($bookings as $booking): ?>
                        <div style="border: 2px solid #eee; border-radius: 10px; overflow: hidden; display: grid; grid-template-columns: 200px 1fr; gap: 1.5rem;">
                            <img src="<?php echo sanitize($booking['image_url']); ?>" alt="<?php echo sanitize($booking['hike_name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <div style="padding: 1.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div>
                                        <h4 style="color: var(--primary); margin-bottom: 0.3rem;"><?php echo sanitize($booking['hike_name']); ?></h4>
                                        <p style="color: var(--text-light);">📍 <?php echo sanitize($booking['location']); ?></p>
                                    </div>
                                    <span class="meta-badge" style="background: <?php 
                                        echo $booking['status'] === 'confirmed' ? '#d4edda' : 
                                             ($booking['status'] === 'cancelled' ? '#f8d7da' : '#fff3cd'); 
                                    ?>; color: <?php 
                                        echo $booking['status'] === 'confirmed' ? '#155724' : 
                                             ($booking['status'] === 'cancelled' ? '#721c24' : '#856404'); 
                                    ?>;">
                                        <?php echo ucfirst(sanitize($booking['status'])); ?>
                                    </span>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                                    <div>
                                        <strong style="color: var(--text-light); font-size: 0.9rem;">Date</strong>
                                        <p style="margin-top: 0.2rem;">📅 <?php echo date('M d, Y', strtotime($booking['date'])); ?></p>
                                    </div>
                                    <div>
                                        <strong style="color: var(--text-light); font-size: 0.9rem;">Guests</strong>
                                        <p style="margin-top: 0.2rem;">👥 <?php echo $booking['guests']; ?> person(s)</p>
                                    </div>
                                    <div>
                                        <strong style="color: var(--text-light); font-size: 0.9rem;">Total Price</strong>
                                        <p style="margin-top: 0.2rem; color: var(--accent); font-weight: bold;">₱<?php echo number_format($booking['total_price']); ?></p>
                                    </div>
                                    <div>
                                        <strong style="color: var(--text-light); font-size: 0.9rem;">Booked On</strong>
                                        <p style="margin-top: 0.2rem;"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem;">
                    <p style="font-size: 1.2rem; color: var(--text-light); margin-bottom: 1.5rem;">You haven't made any bookings yet.</p>
                    <a href="hikes.php" class="btn btn-primary">Browse Hikes</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
