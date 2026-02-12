<?php
/**
 * One-time migration: add user_id to bookings table.
 * Run this once in your browser (e.g. http://localhost/melmel/run_migration.php) then delete or ignore.
 */
require_once 'config.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo->query("SELECT user_id FROM bookings LIMIT 1");
    echo "Column 'user_id' already exists in bookings. Nothing to do.\n";
    exit;
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Unknown column') === false) {
        throw $e;
    }
}

try {
    $pdo->exec("ALTER TABLE `bookings` ADD COLUMN `user_id` int(11) DEFAULT NULL AFTER `hike_id`");
    $pdo->exec("ALTER TABLE `bookings` ADD KEY `idx_user_id` (`user_id`)");
    $pdo->exec("ALTER TABLE `bookings` ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL");
    echo "Migration completed. Column user_id added to bookings.\n";
} catch (PDOException $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
