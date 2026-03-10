<?php
/**
 * PayMongo GCash failed/cancelled redirect URL.
 * Use this as the "failed" URL when creating a Source.
 */
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <img src="images/logo.png" alt="<?php echo SITE_NAME; ?>">
                <?php echo SITE_NAME; ?>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="hikes.php">Explore</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Log in</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="container">
        <div class="form-container" style="max-width: 560px;">
            <div class="alert alert-error">
                <h2 style="margin-bottom: 1rem;">Payment not completed</h2>
                <p>Your GCash payment was cancelled or failed. You can try again from your booking or choose another payment method.</p>
            </div>
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="profile.php" class="btn btn-primary">My Bookings</a>
                <a href="hikes.php" class="btn btn-secondary">Browse Hikes</a>
            </div>
        </div>
    </div>
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
    </footer>
</body>
</html>
