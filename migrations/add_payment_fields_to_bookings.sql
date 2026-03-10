-- Add payment tracking fields to bookings for PayMongo/GCash integration
-- Run this if your bookings table was created before payment fields were added.

ALTER TABLE `bookings`
  ADD COLUMN `payment_method` varchar(32) DEFAULT 'pay_on_arrival' AFTER `status`,
  ADD COLUMN `payment_status` varchar(32) DEFAULT 'unpaid' AFTER `payment_method`,
  ADD COLUMN `payment_source_id` varchar(64) DEFAULT NULL AFTER `payment_status`,
  ADD COLUMN `payment_id` varchar(64) DEFAULT NULL AFTER `payment_source_id`,
  ADD KEY `idx_payment_method` (`payment_method`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_payment_source_id` (`payment_source_id`),
  ADD KEY `idx_payment_id` (`payment_id`);

