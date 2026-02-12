<?php
require_once '../config.php';

requireAdmin();

// Get all users
$stmt = $pdo->query("
    SELECT u.id, u.username, u.email, u.role, u.is_active, u.created_at,
           (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.id) as booking_count
    FROM users u
    ORDER BY u.role DESC, u.username ASC
");
$users = $stmt->fetchAll();

// Count admins (prevent removing last admin)
$stmt = $pdo->query("SELECT COUNT(*) as c FROM users WHERE role = 'admin' AND is_active = 1");
$admin_count = (int) $stmt->fetch()['c'];

$message = '';
$error = '';

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_role'])) {
        $target_id = (int) $_POST['user_id'];
        $new_role = $_POST['role'] === ROLE_ADMIN ? ROLE_ADMIN : ROLE_USER;

        if ($target_id === $_SESSION['user_id'] && $new_role === ROLE_USER && $admin_count <= 1) {
            $error = 'You cannot demote yourself; at least one admin is required.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$new_role, $target_id]);
            $message = 'Role updated successfully.';
            redirect('manage_users.php?updated=1');
        }
    }
    if (isset($_POST['toggle_active'])) {
        $target_id = (int) $_POST['user_id'];
        if ($target_id === $_SESSION['user_id']) {
            $error = 'You cannot deactivate your own account.';
        } else {
            $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
            $stmt->execute([$target_id]);
            $row = $stmt->fetch();
            if ($row) {
                $new_active = $row['is_active'] ? 0 : 1;
                $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
                $stmt->execute([$new_active, $target_id]);
                $message = $new_active ? 'User activated.' : 'User deactivated.';
                redirect('manage_users.php?updated=1');
            }
        }
    }
}

if (isset($_GET['updated'])) {
    $message = 'Changes saved successfully.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users (RBAC) - <?php echo SITE_NAME; ?></title>
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
            <h2>Manage Users (RBAC)</h2>
            <p>Control roles and access: Admin can manage hikes, bookings, and users.</p>
        </div>

        <div style="margin-bottom: 2rem;">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo sanitize($error); ?></div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo sanitize($message); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Bookings</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>#<?php echo $u['id']; ?></td>
                            <td><strong><?php echo sanitize($u['username']); ?></strong></td>
                            <td><?php echo sanitize($u['email']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <select name="role" class="form-control" style="width: auto; padding: 0.3rem;" onchange="this.form.submit()"
                                        <?php if ($u['id'] == $_SESSION['user_id'] && $admin_count <= 1) echo ' disabled'; ?>>
                                        <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <input type="hidden" name="update_role" value="1">
                                </form>
                            </td>
                            <td>
                                <?php if ($u['is_active']): ?>
                                    <span class="meta-badge" style="background: #d4edda; color: #155724;">Active</span>
                                <?php else: ?>
                                    <span class="meta-badge" style="background: #f8d7da; color: #721c24;">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo (int) $u['booking_count']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="toggle_active" value="1">
                                        <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">
                                            <?php echo $u['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: var(--text-light);">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
