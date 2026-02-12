<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?php echo SITE_NAME; ?></title>
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
    <section class="hero" style="padding: 5rem 2rem;">
        <h1>About HikeBook Cebu</h1>
        <p>Your trusted partner for hiking adventures in Cebu</p>
    </section>

    <div class="container">
        <!-- Our Story -->
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="background: white; padding: 3rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h2 style="color: var(--primary); margin-bottom: 1.5rem;">Our Story</h2>
                <p style="line-height: 1.8; color: var(--text-dark); margin-bottom: 1rem;">
                    HikeBook Cebu was born from a passion for exploring the beautiful mountains and peaks of Cebu, Philippines. 
                    We understand that discovering and booking hiking adventures can be challenging, which is why we created 
                    a platform that makes it simple and accessible for everyone.
                </p>
                <p style="line-height: 1.8; color: var(--text-dark);">
                    Whether you're a seasoned hiker or just starting your outdoor journey, we provide curated trails, 
                    expert guides, and seamless booking experiences to help you explore Cebu's natural wonders safely 
                    and responsibly.
                </p>
            </div>

            <!-- Our Mission -->
            <div style="background: white; padding: 3rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h2 style="color: var(--primary); margin-bottom: 1.5rem;">Our Mission</h2>
                <p style="line-height: 1.8; color: var(--text-dark);">
                    To make hiking accessible, safe, and enjoyable for everyone while promoting environmental 
                    conservation and sustainable tourism in Cebu. We believe that everyone should have the opportunity 
                    to experience the beauty of nature and create lasting memories on the trails.
                </p>
            </div>

            <!-- What We Offer -->
            <div style="background: white; padding: 3rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h2 style="color: var(--primary); margin-bottom: 1.5rem;">What We Offer</h2>
                <div style="display: grid; gap: 1.5rem;">
                    <div style="border-left: 4px solid var(--primary); padding-left: 1rem;">
                        <h3 style="color: var(--primary); margin-bottom: 0.5rem;">📍 Curated Trails</h3>
                        <p style="color: var(--text-light);">
                            Handpicked hiking destinations from easy walks to challenging climbs, each with detailed 
                            information about difficulty, duration, and what to expect.
                        </p>
                    </div>
                    <div style="border-left: 4px solid var(--primary); padding-left: 1rem;">
                        <h3 style="color: var(--primary); margin-bottom: 0.5rem;">👥 Expert Guides</h3>
                        <p style="color: var(--text-light);">
                            Professional and knowledgeable guides who prioritize your safety while sharing insights 
                            about local culture, history, and ecology.
                        </p>
                    </div>
                    <div style="border-left: 4px solid var(--primary); padding-left: 1rem;">
                        <h3 style="color: var(--primary); margin-bottom: 0.5rem;">🎒 Easy Booking</h3>
                        <p style="color: var(--text-light);">
                            Simple, transparent booking system with instant confirmation and flexible scheduling to 
                            fit your plans.
                        </p>
                    </div>
                    <div style="border-left: 4px solid var(--primary); padding-left: 1rem;">
                        <h3 style="color: var(--primary); margin-bottom: 0.5rem;">🌿 Eco-Friendly</h3>
                        <p style="color: var(--text-light);">
                            Committed to Leave No Trace principles and supporting local communities through 
                            responsible tourism practices.
                        </p>
                    </div>
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
        <p>Explore the beauty of Cebu's mountains safely and responsibly.</p>
    </footer>
    <?php if (!isLoggedIn()): require_once 'includes/auth_modals.php'; endif; ?>
</body>
</html>
