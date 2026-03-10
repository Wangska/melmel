<?php
require_once 'config.php';

// Featured hikes (first 3) with rating summary
$stmt = $pdo->query("
    SELECT 
        h.*,
        COALESCE(r.avg_rating, 0) AS avg_rating,
        COALESCE(r.rating_count, 0) AS rating_count
    FROM hikes h
    LEFT JOIN (
        SELECT hike_id, AVG(rating) AS avg_rating, COUNT(*) AS rating_count
        FROM hike_ratings
        GROUP BY hike_id
    ) r ON r.hike_id = h.id
    ORDER BY h.id ASC
    LIMIT 3
");
$featured_hikes = $stmt->fetchAll();

// Counts for stats (AllTrails-style)
$stmt = $pdo->query("SELECT COUNT(*) as c FROM hikes");
$trails_count = $stmt->fetch()['c'];
$stmt = $pdo->query("SELECT COUNT(*) as c FROM users WHERE is_active = 1");
$explorers_count = $stmt->fetch()['c'];

// Distinct difficulties for "Browse by difficulty"
$stmt = $pdo->query("SELECT DISTINCT difficulty FROM hikes ORDER BY FIELD(difficulty, 'Easy', 'Easy to Moderate', 'Moderate', 'Challenging')");
$difficulties = $stmt->fetchAll(PDO::FETCH_COLUMN);
if (empty($difficulties)) {
    $difficulties = ['Easy', 'Easy to Moderate', 'Moderate', 'Challenging'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Find Your Trail in Cebu</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="home-page">
    <!-- Navigation -->
    <nav class="navbar navbar-home">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <img src="images/logo.png" alt="<?php echo SITE_NAME; ?> Logo">
                <?php echo SITE_NAME; ?>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="hikes.php">Explore</a></li>
                <li><a href="about.php">About</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?php echo base_url('admin/dashboard.php'); ?>">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="#" class="auth-modal-trigger" data-modal="login">Log in</a></li>
                    <li><a href="#" class="auth-modal-trigger" data-modal="register">Sign up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero (AllTrails-style: Find your outside + search) -->
    <section class="hero hero-home">
        <div class="hero-inner">
            <h1 class="hero-title">Find your outside</h1>
            <p class="hero-subtitle">Search by trail name or location</p>
            <form class="hero-search" action="hikes.php" method="GET" role="search">
                <input type="text" name="search" class="hero-search-input" placeholder="Begin typing to search..." autocomplete="off" aria-label="Search trails">
                <button type="submit" class="btn btn-hero">Explore trails</button>
            </form>
            <a href="hikes.php" class="hero-link">Explore nearby trails</a>
        </div>
    </section>

    <!-- Stats bar (AllTrails-style: X trails, X explorers, etc.) -->
    <section class="home-stats">
        <div class="container">
            <div class="stats-row">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $trails_count; ?></span>
                    <span class="stat-label">curated trails</span>
                    <p class="stat-desc">Handpicked trails across Cebu's mountains.</p>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $explorers_count; ?>+</span>
                    <span class="stat-label">fellow explorers</span>
                    <p class="stat-desc">Join our community of adventurers.</p>
                </div>
                <div class="stat-item">
                    <span class="stat-number">Easy</span>
                    <span class="stat-label">booking</span>
                    <p class="stat-desc">Book your hike in seconds.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pick the right trail for your day -->
    <section class="home-section home-trails">
        <div class="container">
            <div class="section-title">
                <h2>Pick the right trail for your day</h2>
                <p>All our trails are curated for Cebu—from easy walks to challenging climbs.</p>
            </div>
            <div class="hikes-grid home-hikes-grid">
                <?php foreach ($featured_hikes as $hike): ?>
                    <a href="#" class="hike-card hike-card-link hike-detail-trigger" data-hike-id="<?php echo $hike['id']; ?>">
                        <img src="<?php echo sanitize($hike['image_url']); ?>" alt="<?php echo sanitize($hike['name']); ?>" class="hike-image">
                        <div class="hike-content">
                            <h3 class="hike-title"><?php echo sanitize($hike['name']); ?></h3>
                            <div class="hike-location"><?php echo sanitize($hike['location']); ?></div>
                            <?php
                                $avg = isset($hike['avg_rating']) ? (float) $hike['avg_rating'] : 0;
                                $count = isset($hike['rating_count']) ? (int) $hike['rating_count'] : 0;
                                $rounded = $avg ? round($avg * 2) / 2 : 0;
                            ?>
                            <div class="card-rating-row">
                                <div class="rating-stars-sm" aria-hidden="true">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php
                                            $class = '';
                                            if ($rounded >= $i) {
                                                $class = 'filled';
                                            } elseif ($rounded >= $i - 0.5) {
                                                $class = 'half';
                                            }
                                        ?>
                                        <span class="rating-star <?php echo $class; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                                <span class="card-rating-text">
                                    <?php if ($count > 0): ?>
                                        <?php echo number_format($avg, 1); ?> (<?php echo $count; ?>)
                                    <?php else: ?>
                                        No ratings yet
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="hike-meta">
                                <span class="meta-badge meta-duration"><?php echo $hike['duration_hours_min']; ?>–<?php echo $hike['duration_hours_max']; ?>h</span>
                            </div>
                            <div class="hike-footer">
                                <span class="price">₱<?php echo number_format($hike['price']); ?></span>
                                <span class="hike-cta">View trail →</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="section-cta">
                <a href="hikes.php" class="btn btn-primary">View all trails</a>
            </div>
        </div>
    </section>

    <!-- Why HikeBook (compact) -->
    <section class="home-section home-why">
        <div class="container">
            <div class="section-title">
                <h2>Why <?php echo SITE_NAME; ?></h2>
                <p>Your guide to Cebu's best hiking</p>
            </div>
            <div class="features features-compact">
                <div class="feature-card">
                    <span class="feature-icon">🗺️</span>
                    <h3>Curated trails</h3>
                    <p>Handpicked from easy walks to challenging climbs</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">📅</span>
                    <h3>Easy booking</h3>
                    <p>Book your hike in seconds</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">⭐</span>
                    <h3>Expert guides</h3>
                    <p>Safe and memorable experiences</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
    <?php require_once 'includes/hike_detail_modal.php'; ?>
    <?php if (!isLoggedIn()): require_once 'includes/auth_modals.php'; endif; ?>
</body>
</html>
