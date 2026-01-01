CREATE TABLE IF NOT EXISTS comments (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    devotion_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    parent_comment_id INT UNSIGNED NULL,
    depth TINYINT UNSIGNED NOT NULL,
    body TEXT NOT NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    edited_at TIMESTAMP NULL,
    FOREIGN KEY fk_comment_devotion (devotion_id) REFERENCES devotions(id) ON DELETE CASCADE,
    FOREIGN KEY fk_comment_user (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY fk_comment_parent (parent_comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_devotion_comments (devotion_id, is_deleted, created_at),
    INDEX idx_parent_depth (parent_comment_id, depth),
    CHECK (depth BETWEEN 0 AND 2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

