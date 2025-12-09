-- SQL script to add branchId column to existing users table
-- Run this if you already have the database set up

ALTER TABLE `users` ADD COLUMN `branchId` int(11) DEFAULT NULL AFTER `role`;

-- Add foreign key constraint (optional, for data integrity)
-- ALTER TABLE `users` ADD CONSTRAINT `fk_users_branch` FOREIGN KEY (`branchId`) REFERENCES `branch` (`branchId`) ON DELETE SET NULL;