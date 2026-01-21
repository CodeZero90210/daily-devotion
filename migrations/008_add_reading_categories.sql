-- Migration: Add category column to readings table
-- Maps existing reading_order values to categories:
-- 1 -> Old Testament
-- 2 -> New Testament
-- 3 -> Psalms
-- 4 -> Proverbs
-- Any readings with order > 4 will be deleted (keeping only first 4 per devotion)

-- Delete any readings beyond order 4 (keep only first 4 per devotion)
DELETE r1 FROM readings r1
INNER JOIN readings r2 
WHERE r1.devotion_id = r2.devotion_id 
  AND r1.reading_order > 4;

-- Add category column (nullable first, then we'll backfill)
ALTER TABLE readings ADD COLUMN category VARCHAR(50) NULL AFTER reading_order;

-- Backfill existing data based on reading_order
UPDATE readings SET category = 'Old Testament' WHERE reading_order = 1;
UPDATE readings SET category = 'New Testament' WHERE reading_order = 2;
UPDATE readings SET category = 'Psalms' WHERE reading_order = 3;
UPDATE readings SET category = 'Proverbs' WHERE reading_order = 4;

-- Delete any readings that couldn't be categorized (shouldn't happen now, but just in case)
DELETE FROM readings WHERE category IS NULL;

-- Drop old unique key on (devotion_id, reading_order)
ALTER TABLE readings DROP INDEX uk_reading_order;

-- Add new unique key on (devotion_id, category) to ensure one reading per category per devotion
ALTER TABLE readings ADD UNIQUE KEY uk_reading_category (devotion_id, category);

-- Add index on category for queries
ALTER TABLE readings ADD INDEX idx_reading_category (category);

-- Make category NOT NULL after backfill
ALTER TABLE readings MODIFY COLUMN category VARCHAR(50) NOT NULL;
