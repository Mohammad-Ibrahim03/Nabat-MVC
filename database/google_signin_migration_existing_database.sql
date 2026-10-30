-- Apply only to an existing nabta_store database imported before Google sign-in support.
-- The updated database/nabta_store2.sql already includes this column and index.
SET @db_name = DATABASE();
SET @has_google_sub = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'users' AND COLUMN_NAME = 'google_sub'
);
SET @ddl = IF(@has_google_sub = 0,
  'ALTER TABLE `users` ADD COLUMN `google_sub` varchar(255) DEFAULT NULL AFTER `password`',
  'SELECT \'google_sub column already exists\' AS migration_status');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @has_google_index = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'users' AND INDEX_NAME = 'uq_users_google_sub'
);
SET @ddl = IF(@has_google_index = 0,
  'ALTER TABLE `users` ADD UNIQUE KEY `uq_users_google_sub` (`google_sub`)',
  'SELECT \'google_sub index already exists\' AS migration_status');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;


-- Repair the original placeholder hashes used by the local demo seed accounts only.
-- Existing real user passwords are never overwritten by these statements.
UPDATE `users`
SET `password` = '$2y$12$SISp9TNfNhELW9VRm2Qta.QqPZpGg3/5mCkkvjSpmjbMnSoGGtr3u'
WHERE `email` = 'mohammad@example.com' AND `password` = 'hashed_password_1';

UPDATE `users`
SET `password` = '$2y$12$J2nSugFb2f/LKAM//yFB3.0GSQXLbiYWhVapUY/OD1SXUsWhqxt4i'
WHERE (`email`, `password`) IN (
  ('sara@example.com', 'hashed_password_2'),
  ('layan@example.com', 'hashed_password_3'),
  ('omar@example.com', 'hashed_password_4'),
  ('noor@example.com', 'hashed_password_5'),
  ('yasmin@example.com', 'hashed_password_6'),
  ('kareem@example.com', 'hashed_password_7'),
  ('dana@example.com', 'hashed_password_8'),
  ('rami@example.com', 'hashed_password_9'),
  ('tala@example.com', 'hashed_password_10')
);
