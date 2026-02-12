<?php
require_once 'config.php';

// Get all hikes with optional filters
$difficulty = isset($_GET['difficulty']) ? $_GET['difficulty'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM hikes WHERE 1=1";
$params = [];

if ($difficulty) {
    $query .= " AND difficulty = ?";
    $params[] = $difficulty;
}

if ($search) {
    $query .= " AND (name LIKE ? OR location LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$query .= " ORDER BY name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$hikes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Hikes - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="explore-page">
    <!-- Navigation (transparent over hero, same as home) -->
    <nav class="navbar navbar-home">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <img src="images/logo.png" alt="HikeBook Cebu Logo">
                <?php echo SITE_NAME; ?>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="hikes.php">Explore</a></li>
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

    <!-- Hero (same style as home so nav is transparent) -->
    <section class="hero hero-explore">
        <div class="hero-inner">
            <h1 class="hero-title">Explore trails</h1>
            <p class="hero-subtitle">Discover <?php echo count($hikes); ?> amazing trails in Cebu</p>
        </div>
    </section>

    <div class="container">
        <div class="section-title">
            <h2>All Hiking Trails</h2>
            <p>Find the right trail for your day</p>
        </div>

        <!-- Filters -->
        <div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <form method="GET" action="hikes.php" id="filterForm" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
                <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Search hikes..." value="<?php echo sanitize($search); ?>" autocomplete="off">
                </div>
                <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                    <label for="difficulty">Difficulty</label>
                    <select id="difficulty" name="difficulty" class="form-control">
                        <option value="">All Difficulties</option>
                        <option value="Easy" <?php echo $difficulty === 'Easy' ? 'selected' : ''; ?>>Easy</option>
                        <option value="Easy to Moderate" <?php echo $difficulty === 'Easy to Moderate' ? 'selected' : ''; ?>>Easy to Moderate</option>
                        <option value="Moderate" <?php echo $difficulty === 'Moderate' ? 'selected' : ''; ?>>Moderate</option>
                        <option value="Challenging" <?php echo $difficulty === 'Challenging' ? 'selected' : ''; ?>>Challenging</option>
                    </select>
                </div>
                <a href="hikes.php" class="btn btn-secondary">Clear</a>
            </form>
        </div>

        <!-- Hikes Grid -->
        <?php if (count($hikes) > 0): ?>
            <div class="hikes-grid">
                <?php foreach ($hikes as $hike): ?>
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
                                <a href="#" class="btn btn-primary hike-detail-trigger" data-hike-id="<?php echo $hike['id']; ?>">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem;">
                <p style="font-size: 1.2rem; color: #7f8c8d;">No hikes found matching your criteria.</p>
                <a href="hikes.php" class="btn btn-primary" style="margin-top: 1rem;">View All Hikes</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
    <?php require_once 'includes/hike_detail_modal.php'; ?>
    <?php if (!isLoggedIn()): require_once 'includes/auth_modals.php'; endif; ?>

    <script>
        // Auto-filter functionality
        const searchInput = document.getElementById('search');
        const difficultySelect = document.getElementById('difficulty');
        const filterForm = document.getElementById('filterForm');
        
        let searchTimeout;
        
        // Auto-submit when typing in search (with debounce)
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                filterForm.submit();
            }, 500); // Wait 500ms after user stops typing
        });
        
        // Auto-submit when difficulty changes
        difficultySelect.addEventListener('change', function() {
            filterForm.submit();
        });
    </script>
</body>
</html>
