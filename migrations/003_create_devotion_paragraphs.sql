CREATE TABLE IF NOT EXISTS devotion_paragraphs (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    devotion_id INT UNSIGNED NOT NULL,
    paragraph_number INT UNSIGNED NOT NULL,
    content TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY fk_paragraph_devotion (devotion_id) REFERENCES devotions(id) ON DELETE CASCADE,
    UNIQUE KEY uk_paragraph_order (devotion_id, paragraph_number),
    INDEX idx_devotion_paragraphs (devotion_id, paragraph_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

