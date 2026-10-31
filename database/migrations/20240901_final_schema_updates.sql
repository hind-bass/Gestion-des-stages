-- Add any missing indexes (using procedures to avoid errors if they exist)
SET @dbname = DATABASE();
SET @table = 'stagiaires';
SET @indexname = 'idx_stagiaires_responsable';
SET @prepStatement = CONCAT(
    'SELECT COUNT(*) INTO @indexexists FROM information_schema.STATISTICS WHERE ',
    'TABLE_SCHEMA = "', @dbname, '" AND ',
    'TABLE_NAME = "', @table, '" AND ',
    'INDEX_NAME = "', @indexname, '"'
);
PREPARE stmt FROM @prepStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    @indexexists = 0,
    CONCAT('ALTER TABLE `', @table, '` ADD INDEX `', @indexname, '` (`responsable_id`)'),
    'SELECT "Index already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add other indexes
SET @indexname = 'idx_stagiaires_statut';
SET @prepStatement = CONCAT(
    'SELECT COUNT(*) INTO @indexexists FROM information_schema.STATISTICS WHERE ',
    'TABLE_SCHEMA = "', @dbname, '" AND ',
    'TABLE_NAME = "', @table, '" AND ',
    'INDEX_NAME = "', @indexname, '"'
);
PREPARE stmt FROM @prepStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    @indexexists = 0,
    CONCAT('ALTER TABLE `', @table, '` ADD INDEX `', @indexname, '` (`statut`)'),
    'SELECT "Index already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for responsables email
SET @table = 'responsables';
SET @indexname = 'idx_responsables_email';
SET @prepStatement = CONCAT(
    'SELECT COUNT(*) INTO @indexexists FROM information_schema.STATISTICS WHERE ',
    'TABLE_SCHEMA = "', @dbname, '" AND ',
    'TABLE_NAME = "', @table, '" AND ',
    'INDEX_NAME = "', @indexname, '"'
);
PREPARE stmt FROM @prepStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    @indexexists = 0,
    CONCAT('ALTER TABLE `', @table, '` ADD UNIQUE `', @indexname, '` (`email`)'),
    'SELECT "Index already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for administrateurs email
SET @table = 'administrateurs';
SET @indexname = 'idx_administrateurs_email';
SET @prepStatement = CONCAT(
    'SELECT COUNT(*) INTO @indexexists FROM information_schema.STATISTICS WHERE ',
    'TABLE_SCHEMA = "', @dbname, '" AND ',
    'TABLE_NAME = "', @table, '" AND ',
    'INDEX_NAME = "', @indexname, '"'
);
PREPARE stmt FROM @prepStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    @indexexists = 0,
    CONCAT('ALTER TABLE `', @table, '` ADD UNIQUE `', @indexname, '` (`email`)'),
    'SELECT "Index already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure foreign key constraints exist
SET @dbname = DATABASE();
SET @constraintName = 'fk_stagiaires_responsable';
SET @constraintExists = (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = @dbname 
    AND TABLE_NAME = 'stagiaires' 
    AND CONSTRAINT_NAME = @constraintName
);

SET @query = IF(
    @constraintExists = 0,
    'ALTER TABLE `stagiaires` 
    ADD CONSTRAINT `fk_stagiaires_responsable` 
    FOREIGN KEY (`responsable_id`) REFERENCES `responsables`(`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT "Constraint already exists" AS message'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure soft delete columns exist
ALTER TABLE `stagiaires` MODIFY COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `responsables` MODIFY COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `administrateurs` MODIFY COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL;
