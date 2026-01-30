<?php
require_once 'config.php';

// Get featured hikes (first 3)
$stmt = $pdo->query("SELECT * FROM hikes ORDER BY id ASC LIMIT 3");
$featured_hikes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Discover Cebu's Best Hiking Trails</title>
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

    <!-- Hero Section -->
    <section class="hero">
        <h1>Discover Cebu's Most Beautiful Hiking Trails</h1>
        <p>Explore breathtaking peaks, stunning views, and unforgettable adventures</p>
        <a href="hikes.php" class="btn btn-primary">Explore Hikes</a>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-secondary">Get Started</a>
        <?php endif; ?>
    </section>

    <!-- Features Section -->
    <div class="container">
        <div class="section-title">
            <h2>Why Choose HikeBook Cebu?</h2>
            <p>Your ultimate guide to exploring Cebu's mountains</p>
        </div>
        
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">🗺️</div>
                <h3>Curated Trails</h3>
                <p>Handpicked hiking trails from easy walks to challenging climbs</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Easy Booking</h3>
                <p>Book your hike in seconds with our simple booking system</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3>Expert Guides</h3>
                <p>Professional guides to ensure a safe and memorable experience</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Best Prices</h3>
                <p>Competitive pricing with transparent fees and no hidden costs</p>
            </div>
        </div>
    </div>

    <!-- Featured Hikes -->
    <div class="container">
        <div class="section-title">
            <h2>Featured Hikes</h2>
            <p>Start your adventure with these popular trails</p>
        </div>
        
        <div class="hikes-grid">
            <?php foreach ($featured_hikes as $hike): ?>
                <div class="hike-card">
                    <img src="<?php echo sanitize($hike['image_url']); ?>" alt="<?php echo sanitize($hike['name']); ?>" class="hike-image">
                    <div class="hike-content">
                        <h3 class="hike-title"><?php echo sanitize($hike['name']); ?></h3>
                        <div class="hike-location">
                            📍 <?php echo sanitize($hike['location']); ?>
                        </div>
                        <div class="hike-meta">
                            <span class="meta-badge difficulty-<?php echo strtolower(str_replace(' ', '-', $hike['difficulty'])); ?>">
                                <?php echo sanitize($hike['difficulty']); ?>
                            </span>
                            <span class="meta-badge" style="background: #e3f2fd; color: #1976d2;">
                                ⏱️ <?php echo $hike['duration_hours_min']; ?>-<?php echo $hike['duration_hours_max']; ?>h
                            </span>
                        </div>
                        <p class="hike-description">
                            <?php echo substr(sanitize($hike['description']), 0, 100) . '...'; ?>
                        </p>
                        <div class="hike-footer">
                            <span class="price">₱<?php echo number_format($hike['price']); ?></span>
                            <a href="hike_details.php?id=<?php echo $hike['id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="hikes.php" class="btn btn-success">View All Hikes</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        <p>Explore the beauty of Cebu's mountains safely and responsibly.</p>
    </footer>
</body>
</html>
