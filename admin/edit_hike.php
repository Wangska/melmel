<?php
require_once '../config.php';

requireAdmin();

$hike_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($hike_id === 0) {
    redirect('manage_hikes.php');
}

// Get hike details
$stmt = $pdo->prepare("SELECT * FROM hikes WHERE id = ?");
$stmt->execute([$hike_id]);
$hike = $stmt->fetch();

if (!$hike) {
    redirect('manage_hikes.php');
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = trim($_POST['slug']);
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $difficulty = $_POST['difficulty'];
    $duration_min = intval($_POST['duration_min']);
    $duration_max = intval($_POST['duration_max']);
    $price = intval($_POST['price']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    
    // Validation
    if (empty($slug) || empty($name) || empty($location) || empty($difficulty) || empty($description) || empty($image_url)) {
        $error = 'Please fill in all fields.';
    } elseif ($duration_min < 1 || $duration_max < $duration_min) {
        $error = 'Invalid duration values.';
    } elseif ($price < 0) {
        $error = 'Price must be a positive number.';
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE hikes 
                SET slug = ?, name = ?, location = ?, difficulty = ?, 
                    duration_hours_min = ?, duration_hours_max = ?, 
                    price = ?, description = ?, image_url = ? 
                WHERE id = ?
            ");
            $stmt->execute([$slug, $name, $location, $difficulty, $duration_min, $duration_max, $price, $description, $image_url, $hike_id]);
            $success = true;
            
            // Refresh hike data
            $stmt = $pdo->prepare("SELECT * FROM hikes WHERE id = ?");
            $stmt->execute([$hike_id]);
            $hike = $stmt->fetch();
        } catch (PDOException $e) {
            $error = 'Failed to update hike.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hike - <?php echo SITE_NAME; ?></title>
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
        <div class="form-container" style="max-width: 700px;">
            <h2 style="text-align: center; color: var(--primary); margin-bottom: 2rem;">Edit Hike</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success">Hike updated successfully!</div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo sanitize($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="slug">Slug * (unique identifier)</label>
                    <input type="text" id="slug" name="slug" class="form-control" required value="<?php echo sanitize($hike['slug']); ?>">
                </div>

                <div class="form-group">
                    <label for="name">Hike Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?php echo sanitize($hike['name']); ?>">
                </div>

                <div class="form-group">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-control" required value="<?php echo sanitize($hike['location']); ?>">
                </div>

                <div class="form-group">
                    <label for="difficulty">Difficulty *</label>
                    <select id="difficulty" name="difficulty" class="form-control" required>
                        <option value="Easy" <?php echo $hike['difficulty'] === 'Easy' ? 'selected' : ''; ?>>Easy</option>
                        <option value="Easy to Moderate" <?php echo $hike['difficulty'] === 'Easy to Moderate' ? 'selected' : ''; ?>>Easy to Moderate</option>
                        <option value="Moderate" <?php echo $hike['difficulty'] === 'Moderate' ? 'selected' : ''; ?>>Moderate</option>
                        <option value="Challenging" <?php echo $hike['difficulty'] === 'Challenging' ? 'selected' : ''; ?>>Challenging</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="duration_min">Min Duration (hours) *</label>
                        <input type="number" id="duration_min" name="duration_min" class="form-control" required min="1" value="<?php echo $hike['duration_hours_min']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="duration_max">Max Duration (hours) *</label>
                        <input type="number" id="duration_max" name="duration_max" class="form-control" required min="1" value="<?php echo $hike['duration_hours_max']; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="price">Price (₱) *</label>
                    <input type="number" id="price" name="price" class="form-control" required min="0" value="<?php echo $hike['price']; ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required><?php echo sanitize($hike['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image_url">Image URL *</label>
                    <input type="text" id="image_url" name="image_url" class="form-control" required value="<?php echo sanitize($hike['image_url']); ?>">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Hike</button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="manage_hikes.php" class="btn btn-secondary">Back to Hikes</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
