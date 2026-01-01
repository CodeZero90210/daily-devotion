CREATE TABLE IF NOT EXISTS readings (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    devotion_id INT UNSIGNED NOT NULL,
    reading_order INT UNSIGNED NOT NULL,
    scripture_reference VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY fk_reading_devotion (devotion_id) REFERENCES devotions(id) ON DELETE CASCADE,
    UNIQUE KEY uk_reading_order (devotion_id, reading_order),
    INDEX idx_devotion_readings (devotion_id, reading_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

