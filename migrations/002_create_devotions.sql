CREATE TABLE IF NOT EXISTS devotions (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    devotion_date DATE UNIQUE NOT NULL,
    scripture_references TEXT NULL,
    verse_text TEXT NULL,
    author_paragraphs_enabled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_devotion_date (devotion_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

