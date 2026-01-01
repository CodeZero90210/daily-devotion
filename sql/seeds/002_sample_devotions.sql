-- Sample devotions (copyright-safe: dates and references only, no text content)

INSERT INTO devotions (devotion_date, scripture_references) VALUES
('2024-01-15', 'John 3:16, Romans 8:28'),
('2024-01-16', 'Matthew 5:3-12'),
('2024-01-17', 'Psalm 23:1-6')
ON DUPLICATE KEY UPDATE devotion_date=devotion_date;

-- Sample readings for first devotion
INSERT INTO readings (devotion_id, reading_order, scripture_reference)
SELECT d.id, 1, 'John 3:16-17'
FROM devotions d
WHERE d.devotion_date = '2024-01-15'
ON DUPLICATE KEY UPDATE reading_order=reading_order;

INSERT INTO readings (devotion_id, reading_order, scripture_reference)
SELECT d.id, 2, 'Romans 8:28-30'
FROM devotions d
WHERE d.devotion_date = '2024-01-15'
ON DUPLICATE KEY UPDATE reading_order=reading_order;

