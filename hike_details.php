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

// Ratings: summary + current user's rating
$rating_summary = [
    'avg' => null,
    'count' => 0,
    'avg_difficulty' => null,
    'avg_duration' => null,
];
$user_rating = null;
$user_difficulty_rating = null;
$user_duration_rating = null;
$rating_message = '';
$rating_error = '';

if (isLoggedIn() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    $rating = intval($_POST['rating']);
    $difficulty_rating = isset($_POST['difficulty_rating']) ? intval($_POST['difficulty_rating']) : 0;
    $duration_rating   = isset($_POST['duration_rating']) ? intval($_POST['duration_rating']) : 0;

    if ($rating < 1 || $rating > 5) {
        $rating_error = 'Please select an overall rating between 1 and 5 stars.';
    } else {
        $user_id = (int) $_SESSION['user_id'];

        // Normalize optional sub-ratings
        $difficulty_rating = ($difficulty_rating >= 1 && $difficulty_rating <= 5) ? $difficulty_rating : null;
        $duration_rating   = ($duration_rating >= 1 && $duration_rating <= 5) ? $duration_rating   : null;

        try {
            // Try update first
            $stmt = $pdo->prepare("
                UPDATE hike_ratings 
                SET rating = ?, difficulty_rating = ?, duration_rating = ?, updated_at = NOW() 
                WHERE hike_id = ? AND user_id = ?
            ");
            $stmt->execute([$rating, $difficulty_rating, $duration_rating, $hike_id, $user_id]);
            if ($stmt->rowCount() === 0) {
                // No existing rating, insert new
                $stmt = $pdo->prepare("
                    INSERT INTO hike_ratings (hike_id, user_id, rating, difficulty_rating, duration_rating) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$hike_id, $user_id, $rating, $difficulty_rating, $duration_rating]);
            }
            $rating_message = 'Thanks for rating this hike!';
        } catch (PDOException $e) {
            $rating_error = 'Unable to save your rating right now.';
        }
    }
}

// Load rating summary
try {
    $stmt = $pdo->prepare("
        SELECT 
            AVG(rating) as avg_rating, 
            AVG(difficulty_rating) as avg_difficulty,
            AVG(duration_rating) as avg_duration,
            COUNT(*) as rating_count 
        FROM hike_ratings 
        WHERE hike_id = ?
    ");
    $stmt->execute([$hike_id]);
    $row = $stmt->fetch();
    if ($row) {
        $rating_summary['avg'] = $row['avg_rating'] !== null ? (float) $row['avg_rating'] : null;
        $rating_summary['avg_difficulty'] = $row['avg_difficulty'] !== null ? (float) $row['avg_difficulty'] : null;
        $rating_summary['avg_duration'] = $row['avg_duration'] !== null ? (float) $row['avg_duration'] : null;
        $rating_summary['count'] = (int) $row['rating_count'];
    }
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("SELECT rating, difficulty_rating, duration_rating FROM hike_ratings WHERE hike_id = ? AND user_id = ?");
        $stmt->execute([$hike_id, (int) $_SESSION['user_id']]);
        $r = $stmt->fetch();
        if ($r) {
            $user_rating = isset($r['rating']) ? (int) $r['rating'] : null;
            $user_difficulty_rating = isset($r['difficulty_rating']) ? (int) $r['difficulty_rating'] : null;
            $user_duration_rating = isset($r['duration_rating']) ? (int) $r['duration_rating'] : null;
        }
    }
} catch (PDOException $e) {
    // fail quietly; keep defaults
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

    <div class="container">
        <div class="hike-detail">
            <img src="<?php echo sanitize($hike['image_url']); ?>" alt="<?php echo sanitize($hike['name']); ?>" class="hike-detail-image">
            
            <div class="hike-detail-content">
                <h1 class="hike-title"><?php echo sanitize($hike['name']); ?></h1>
                <div class="rating-summary">
                    <?php
                        $avg = $rating_summary['avg'];
                        $count = $rating_summary['count'];
                        $rounded = $avg !== null ? round($avg * 2) / 2 : 0;
                    ?>
                    <div class="rating-stars" aria-label="<?php echo $avg ? 'Average rating ' . number_format($avg, 1) . ' out of 5' : 'Not yet rated'; ?>">
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
                    <div class="rating-meta">
                        <?php if ($avg && $count > 0): ?>
                            <span class="rating-score"><?php echo number_format($avg, 1); ?></span>
                            <span class="rating-count">(<?php echo $count; ?> rating<?php echo $count === 1 ? '' : 's'; ?>)</span>
                        <?php else: ?>
                            <span class="rating-empty">Not rated yet</span>
                        <?php endif; ?>
                    </div>
                    <?php
                        $avgDiff = $rating_summary['avg_difficulty'];
                        $avgDur = $rating_summary['avg_duration'];
                    ?>
                    <?php if ($count > 0 && $avgDur): ?>
                        <?php
                            // Map average duration rating (1–3) to label
                            $durLabel = 'Moderate';
                            if ($avgDur < 1.5) {
                                $durLabel = 'Easy';
                            } elseif ($avgDur > 2.5) {
                                $durLabel = 'Hard';
                            }
                        ?>
                        <div style="font-size: 0.9rem; color: var(--text-light); margin-top: 0.25rem;">
                            <span>Typical duration feel: <?php echo $durLabel; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="hike-location" style="font-size: 1.2rem; margin-bottom: 2rem;">
                    📍 <?php echo sanitize($hike['location']); ?>
                </div>

                <div class="detail-grid">
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

                <div class="rating-section">
                    <h3 style="color: var(--primary); margin-bottom: 0.75rem;">Rate this hike</h3>
                    <?php if ($rating_message): ?>
                        <div class="alert alert-success" style="margin-bottom: 0.75rem;"><?php echo sanitize($rating_message); ?></div>
                    <?php elseif ($rating_error): ?>
                        <div class="alert alert-error" style="margin-bottom: 0.75rem;"><?php echo sanitize($rating_error); ?></div>
                    <?php endif; ?>

                    <?php if (isLoggedIn() && !isAdmin()): ?>
                        <form method="POST" action="" class="rating-form">
                            <div class="rating-row">
                                <div class="rating-stars-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="rating-<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo ($user_rating === $i) ? 'checked' : ''; ?>>
                                        <label for="rating-<?php echo $i; ?>" title="<?php echo $i; ?> star<?php echo $i === 1 ? '' : 's'; ?>">★</label>
                                    <?php endfor; ?>
                                </div>
                                <div>
                                    <label for="duration_rating" style="display:block; font-size:0.9rem; color:var(--text-light); margin-bottom:0.25rem;">Duration feel</label>
                                    <select id="duration_rating" name="duration_rating" class="form-control" style="max-width: 220px; padding:0.4rem 0.6rem; height:auto;">
                                        <option value="">Choose…</option>
                                        <option value="1" <?php echo ($user_duration_rating === 1) ? 'selected' : ''; ?>>Easy</option>
                                        <option value="2" <?php echo ($user_duration_rating === 2) ? 'selected' : ''; ?>>Moderate</option>
                                        <option value="3" <?php echo ($user_duration_rating === 3) ? 'selected' : ''; ?>>Hard</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary rating-submit-btn">
                                <?php echo $user_rating ? 'Update rating' : 'Submit rating'; ?>
                            </button>
                        </form>
                        <p class="rating-note">You can update your rating at any time. Duration feel is optional but helps other hikers.</p>
                    <?php elseif (!isLoggedIn()): ?>
                        <p class="rating-note">Please log in to rate this hike.</p>
                    <?php else: ?>
                        <p class="rating-note">Admins cannot rate hikes. Switch to a user account to leave a rating.</p>
                    <?php endif; ?>
                </div>

                <div style="text-align: center; padding: 2rem; background: var(--bg-light); border-radius: 10px;">
                    <h3 style="color: var(--primary); margin-bottom: 1rem;">Ready to Start Your Adventure?</h3>
                    <?php if (!isLoggedIn()): ?>
                        <p style="margin-bottom: 1rem;">Please login to book this hike</p>
                        <a href="#" class="btn btn-primary auth-modal-trigger" data-modal="login">Login to Book</a>
                        <a href="#" class="btn btn-secondary auth-modal-trigger" data-modal="register">Create Account</a>
                    <?php elseif (isAdmin()): ?>
                        <p style="margin-bottom: 0.75rem; color: var(--text-light);">
                            Admin accounts cannot book hikes. Use a regular user account to place a booking.
                        </p>
                        <a href="<?php echo base_url('admin/dashboard.php'); ?>" class="btn btn-secondary">Go to Admin Dashboard</a>
                    <?php else: ?>
                        <a href="book.php?hike_id=<?php echo $hike['id']; ?>" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 3rem;">
                            Book This Hike
                        </a>
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
    <?php if (!isLoggedIn()): require_once 'includes/auth_modals.php'; endif; ?>
</body>
</html>
