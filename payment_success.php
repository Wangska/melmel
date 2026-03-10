<?php
/**
 * PayMongo GCash success redirect URL.
 * Use this as the "success" URL when creating a Source, e.g. PAYMONGO_SITE_URL . '/payment_success.php'
 */
require_once 'config.php';

$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - <?php echo SITE_NAME; ?></title>
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
            <div class="alert alert-success">
                <h2 style="margin-bottom: 1rem;">Payment successful</h2>
                <p>Your GCash payment has been received. We'll confirm your booking shortly.</p>
            </div>
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="profile.php" class="btn btn-primary">View My Bookings</a>
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
    </footer>
</body>
</html>
