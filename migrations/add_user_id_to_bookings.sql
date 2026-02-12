-- Add user_id to bookings for RBAC: link booking requests to logged-in users
-- Run this if your bookings table was created before RBAC was added.

ALTER TABLE `bookings`
  ADD COLUMN `user_id` int(11) DEFAULT NULL AFTER `hike_id`,
  ADD KEY `idx_user_id` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
