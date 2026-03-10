-- Migration: Add hike_ratings table for 1–5 star user ratings

CREATE TABLE IF NOT EXISTS hike_ratings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  hike_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  rating TINYINT(1) NOT NULL,
  difficulty_rating TINYINT(1) DEFAULT NULL,
  duration_rating TINYINT(1) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_hike_ratings_hike FOREIGN KEY (hike_id) REFERENCES hikes(id) ON DELETE CASCADE,
  CONSTRAINT fk_hike_ratings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT uq_hike_ratings_user_hike UNIQUE KEY uq_user_hike (hike_id, user_id),
  KEY idx_hike_ratings_hike (hike_id),
  KEY idx_hike_ratings_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

