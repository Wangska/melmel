<?php
require_once 'config.php';

// Get hike ID from URL
$hike_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($hike_id === 0) {
    redirect('hikes.php');
}

// Fetch hike details
$stmt = $pdo->prepare("SELECT * FROM hikes WHERE id = ?");
$stmt->execute([$hike_id]);
$hike = $stmt->fetch();

if (!$hike) {
    redirect('hikes.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($hike['name']); ?> - <?php echo SITE_NAME; ?></title>
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
                <li><a href="hikes.php">Hikes</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="hike-detail">
            <img src="<?php echo sanitize($hike['image_url']); ?>" alt="<?php echo sanitize($hike['name']); ?>" class="hike-detail-image">
            
            <div class="hike-detail-content">
                <h1 class="hike-title"><?php echo sanitize($hike['name']); ?></h1>
                <div class="hike-location" style="font-size: 1.2rem; margin-bottom: 2rem;">
                    📍 <?php echo sanitize($hike['location']); ?>
                </div>

                <div class="detail-grid">
                    <div class="detail-item">
                        <strong>Difficulty</strong>
                        <span class="meta-badge difficulty-<?php echo strtolower(str_replace(' ', '-', $hike['difficulty'])); ?>">
                            <?php echo sanitize($hike['difficulty']); ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <strong>Duration</strong>
                        ⏱️ <?php echo $hike['duration_hours_min']; ?>-<?php echo $hike['duration_hours_max']; ?> hours
                    </div>
                    <div class="detail-item">
                        <strong>Price per Person</strong>
                        <span class="price">₱<?php echo number_format($hike['price']); ?></span>
                    </div>
                </div>

                <div style="margin: 2rem 0;">
                    <h3 style="color: var(--primary); margin-bottom: 1rem;">About This Hike</h3>
                    <p style="line-height: 1.8; color: var(--text-dark); font-size: 1.1rem;">
                        <?php echo nl2br(sanitize($hike['description'])); ?>
                    </p>
                </div>

                <div style="text-align: center; padding: 2rem; background: var(--bg-light); border-radius: 10px;">
                    <h3 style="color: var(--primary); margin-bottom: 1rem;">Ready to Start Your Adventure?</h3>
                    <?php if (isLoggedIn()): ?>
                        <a href="book.php?hike_id=<?php echo $hike['id']; ?>" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 3rem;">
                            Book This Hike
                        </a>
                    <?php else: ?>
                        <p style="margin-bottom: 1rem;">Please login to book this hike</p>
                        <a href="login.php?redirect=hike_details.php?id=<?php echo $hike['id']; ?>" class="btn btn-primary">Login to Book</a>
                        <a href="register.php" class="btn btn-secondary">Create Account</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="hikes.php" class="btn btn-secondary">← Back to All Hikes</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
