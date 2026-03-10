-- Migration: Add difficulty and duration breakdown ratings for hikes

ALTER TABLE hike_ratings
  ADD COLUMN IF NOT EXISTS difficulty_rating TINYINT(1) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS duration_rating TINYINT(1) DEFAULT NULL;

