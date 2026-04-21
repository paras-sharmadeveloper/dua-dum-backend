-- Check if status column exists in users table
SET @columnExists = 0;
SELECT COUNT(*) INTO @columnExists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'status';

-- Add status column if it doesn't exist
SET @query = IF(@columnExists = 0, 
    'ALTER TABLE users ADD COLUMN status ENUM("Active", "In Active") DEFAULT "Active" AFTER password;',
    'SELECT "Status column already exists in users table" AS message;');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update all existing users to have 'Active' status if the column was just added
UPDATE users SET status = 'Active' WHERE status IS NULL; 