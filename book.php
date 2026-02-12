<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get hike ID
$hike_id = isset($_GET['hike_id']) ? intval($_GET['hike_id']) : 0;

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

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$success = false;
$error = '';

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $date = $_POST['date'];
    $guests = intval($_POST['guests']);
    
    // Validate
    if (empty($customer_name) || empty($customer_email) || empty($date) || $guests < 1) {
        $error = 'Please fill in all fields correctly.';
    } elseif (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strtotime($date) < strtotime('today')) {
        $error = 'Please select a future date.';
    } else {
        // Calculate total price
        $total_price = $hike['price'] * $guests;
        
        // Insert booking (user_id links request to user for admin panel)
        $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
        try {
            $stmt = $pdo->prepare("
                INSERT INTO bookings (hike_id, user_id, customer_name, customer_email, date, guests, total_price, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$hike_id, $user_id, $customer_name, $customer_email, $date, $guests, $total_price]);
            $success = true;
        } catch (PDOException $e) {
            $error = 'Booking failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo sanitize($hike['name']); ?> - <?php echo SITE_NAME; ?></title>
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
        <?php if ($success): ?>
            <div class="form-container" style="max-width: 600px;">
                <div class="alert alert-success">
                    <h2 style="margin-bottom: 1rem;">🎉 Booking Confirmed!</h2>
                    <p>Your booking has been successfully submitted. We'll send you a confirmation email shortly.</p>
                </div>
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="profile.php" class="btn btn-primary">View My Bookings</a>
                    <a href="hikes.php" class="btn btn-secondary">Book Another Hike</a>
                </div>
            </div>
        <?php else: ?>
            <div class="section-title">
                <h2>Book Your Hike</h2>
                <p><?php echo sanitize($hike['name']); ?></p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; max-width: 1000px; margin: 0 auto;">
                <!-- Booking Form -->
                <div class="form-container" style="margin: 0;">
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo sanitize($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="customer_name">Full Name *</label>
                            <input type="text" id="customer_name" name="customer_name" class="form-control" required value="<?php echo sanitize($user['username']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="customer_email">Email Address *</label>
                            <input type="email" id="customer_email" name="customer_email" class="form-control" required value="<?php echo sanitize($user['email']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="date">Hike Date *</label>
                            <input type="date" id="date" name="date" class="form-control" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>

                        <div class="form-group">
                            <label for="guests">Number of Guests *</label>
                            <input type="number" id="guests" name="guests" class="form-control" required min="1" max="20" value="1" onchange="calculateTotal()">
                        </div>

                        <div class="form-group">
                            <label>Total Price</label>
                            <div style="font-size: 2rem; color: var(--accent); font-weight: bold;" id="total_price">
                                ₱<?php echo number_format($hike['price']); ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Confirm Booking</button>
                    </form>
                </div>

                <!-- Hike Summary -->
                <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="color: var(--primary); margin-bottom: 1rem;">Booking Summary</h3>
                    <img src="<?php echo sanitize($hike['image_url']); ?>" alt="<?php echo sanitize($hike['name']); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 1rem;">
                    
                    <h4 style="margin-bottom: 0.5rem;"><?php echo sanitize($hike['name']); ?></h4>
                    <p style="color: var(--text-light); margin-bottom: 1rem;">📍 <?php echo sanitize($hike['location']); ?></p>
                    
                    <div style="background: var(--bg-light); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Difficulty:</span>
                            <span class="meta-badge difficulty-<?php echo strtolower(str_replace(' ', '-', $hike['difficulty'])); ?>">
                                <?php echo sanitize($hike['difficulty']); ?>
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Duration:</span>
                            <strong><?php echo $hike['duration_hours_min']; ?>-<?php echo $hike['duration_hours_max']; ?> hours</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Price per Person:</span>
                            <strong>₱<?php echo number_format($hike['price']); ?></strong>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" style="font-size: 0.9rem;">
                        <strong>Important:</strong> Please arrive 30 minutes before the scheduled time. Bring appropriate hiking gear, water, and snacks.
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const pricePerPerson = <?php echo $hike['price']; ?>;
        
        function calculateTotal() {
            const guests = parseInt(document.getElementById('guests').value) || 1;
            const total = pricePerPerson * guests;
            document.getElementById('total_price').textContent = '₱' + total.toLocaleString();
        }
    </script>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
