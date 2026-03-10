<?php
require_once '../config.php';

requireAdmin();

// Get all hikes
$stmt = $pdo->query("SELECT * FROM hikes ORDER BY name ASC");
$hikes = $stmt->fetchAll();

// Handle delete
if (isset($_GET['delete'])) {
    $hike_id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM hikes WHERE id = ?");
        $stmt->execute([$hike_id]);
        redirect('manage_hikes.php');
    } catch (PDOException $e) {
        $error = "Cannot delete hike with existing bookings.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hikes - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">
                <img src="../images/logo.png" alt="HikeBook Cebu Logo">
                <?php echo SITE_NAME; ?>
            </a>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../hikes.php">Explore</a></li>
                <li><a href="../profile.php">Profile</a></li>
                <li><a href="dashboard.php">Admin</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="section-title">
            <h2>Manage Hikes</h2>
            <p><?php echo count($hikes); ?> total hikes</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo sanitize($error); ?></div>
        <?php endif; ?>

        <div style="margin-bottom: 2rem;">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="add_hike.php" class="btn btn-primary">Add New Hike</a>
        </div>

        <div class="hikes-grid">
            <?php foreach ($hikes as $hike): ?>
                <div class="hike-card">
                    <img src="../<?php echo sanitize($hike['image_url']); ?>" alt="<?php echo sanitize($hike['name']); ?>" class="hike-image">
                    <div class="hike-content">
                        <h3 class="hike-title"><?php echo sanitize($hike['name']); ?></h3>
                        <div class="hike-location">
                            📍 <?php echo sanitize($hike['location']); ?>
                        </div>
                        <div class="hike-meta">
                            <span class="meta-badge" style="background: #e3f2fd; color: #1976d2;">
                                ⏱️ <?php echo $hike['duration_hours_min']; ?>-<?php echo $hike['duration_hours_max']; ?>h
                            </span>
                        </div>
                        <div class="hike-footer">
                            <span class="price">₱<?php echo number_format($hike['price']); ?></span>
                            <div>
                                <a href="edit_hike.php?id=<?php echo $hike['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Edit</a>
                                <a href="?delete=<?php echo $hike['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this hike?')"
                                   style="color: #dc3545; text-decoration: none; font-weight: 600; padding: 0.5rem 1rem;">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
