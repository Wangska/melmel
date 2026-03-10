<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - <?php echo SITE_NAME; ?></title>
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

    <!-- Hero Section -->
    <section class="hero hero-simple">
        <h1>About <?php echo SITE_NAME; ?></h1>
        <p>Discover how this website helps you explore and book hikes around Cebu.</p>
    </section>

    <div class="container">
        <!-- Main About card -->
        <div style="max-width: 880px; margin: 0 auto;">
            <div class="form-container" style="max-width: 100%; margin-top: 0; margin-bottom: 2rem;">
                <h2 style="color: var(--primary); margin-bottom: 1rem;">What is <?php echo SITE_NAME; ?>?</h2>
                <p style="line-height: 1.8; color: var(--text-dark); margin-bottom: 0.75rem;">
                    <?php echo SITE_NAME; ?> is a simple hiking companion website for Cebu. It lets you discover curated trails,
                    see important details like difficulty, duration, and price, and book guided hikes online.
                </p>
                <p style="line-height: 1.8; color: var(--text-dark); margin: 0;">
                    The goal is to make planning a hike as easy as possible—from finding a trail, to reserving a slot,
                    to managing your upcoming adventures in one place.
                </p>
            </div>

            <!-- How it works -->
            <div class="form-container" style="max-width: 100%; margin-top: 0; margin-bottom: 2rem;">
                <h2 style="color: var(--primary); margin-bottom: 1rem;">How the site works</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: 0.5rem;">1. Browse hikes</h3>
                        <p style="color: var(--text-light); margin: 0;">
                            Use the Explore page to see all available hikes, each with photos and key information.
                        </p>
                    </div>
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: 0.5rem;">2. Check details & ratings</h3>
                        <p style="color: var(--text-light); margin: 0;">
                            Open a hike to view difficulty, duration, price, and average 5‑star ratings from other hikers.
                        </p>
                    </div>
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: 0.5rem;">3. Book your spot</h3>
                        <p style="color: var(--text-light); margin: 0;">
                            After logging in, choose your date and group size and confirm your booking with cash or GCash.
                        </p>
                    </div>
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: 0.5rem;">4. Manage everything in your profile</h3>
                        <p style="color: var(--text-light); margin: 0;">
                            View all your bookings, dates, and total prices from your profile page.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Key features -->
            <div class="form-container" style="max-width: 100%; margin-top: 0; margin-bottom: 2rem;">
                <h2 style="color: var(--primary); margin-bottom: 1rem;">Key features</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>For</th>
                                <th>Features</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Hikers</strong></td>
                                <td>
                                    <ul style="color: var(--text-light); line-height: 1.8; padding-left: 1.2rem; margin: 0;">
                                        <li>Clean list of curated Cebu hikes.</li>
                                        <li>Trail details: duration and price.</li>
                                        <li>5‑star rating system on each hike.</li>
                                        <li>Simple, mobile‑friendly booking flow.</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Admins</strong></td>
                                <td>
                                    <ul style="color: var(--text-light); line-height: 1.8; padding-left: 1.2rem; margin: 0;">
                                        <li>Dashboard with stats for hikes, bookings, users, and revenue.</li>
                                        <li>Manage hikes and photos from a dedicated admin area.</li>
                                        <li>Review and update booking statuses.</li>
                                        <li>Role‑based access so only admins see admin tools.</li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Why Choose Us -->
            <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 3rem; border-radius: 15px; color: white; text-align: center;">
                <h2 style="margin-bottom: 1.5rem;">Why Choose HikeBook Cebu?</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-top: 2rem;">
                    <div>
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🏆</div>
                        <h3 style="margin-bottom: 0.5rem;">Trusted</h3>
                        <p style="opacity: 0.9;">Years of experience guiding hikers safely</p>
                    </div>
                    <div>
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⚡</div>
                        <h3 style="margin-bottom: 0.5rem;">Fast</h3>
                        <p style="opacity: 0.9;">Quick booking with instant confirmation</p>
                    </div>
                    <div>
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">💯</div>
                        <h3 style="margin-bottom: 0.5rem;">Quality</h3>
                        <p style="opacity: 0.9;">Top-rated guides and experiences</p>
                    </div>
                    <div>
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🤝</div>
                        <h3 style="margin-bottom: 0.5rem;">Support</h3>
                        <p style="opacity: 0.9;">Dedicated customer service team</p>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div style="text-align: center; margin: 3rem 0;">
                <h2 style="color: var(--primary); margin-bottom: 1rem;">Ready to Start Your Adventure?</h2>
                <p style="color: var(--text-light); margin-bottom: 2rem;">Join thousands of hikers who have explored Cebu's mountains with us.</p>
                <a href="hikes.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 3rem;">Browse Hiking Trails</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
    <?php if (!isLoggedIn()): require_once 'includes/auth_modals.php'; endif; ?>
</body>
</html>
