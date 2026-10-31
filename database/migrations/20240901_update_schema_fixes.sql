-- Skip if these columns already exist
SET @dbname = DATABASE();
SET @tablename = 'stagiaires';
SET @columnname = 'created_at';
SET @prepStatement = CONCAT(
    'SELECT COUNT(*) INTO @exists FROM information_schema.COLUMNS WHERE ',
    'TABLE_SCHEMA = "', @dbname, '" AND ',
    'TABLE_NAME = "', @tablename, '" AND ',
    'COLUMN_NAME = "', @columnname, '"'
);

PREPARE stmt FROM @prepStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add timestamps and soft delete to stagiaires if they don't exist
SET @query = IF(
    @exists = 0,
    CONCAT('ALTER TABLE `', @tablename, '` 
        ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL'
    ),
    'SELECT "Columns already exist" AS message'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add timestamps and soft delete to responsables if they don't exist
SET @tablename = 'responsables';
SET @prepStatement = CONCAT(
    'SELECT COUNT(*) INTO @exists FROM information_schema.COLUMNS WHERE ',
    'TABLE_SCHEMA = "', @dbname, '" AND ',
    'TABLE_NAME = "', @tablename, '" AND ',
    'COLUMN_NAME = "created_at"'
);

PREPARE stmt FROM @prepStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    @exists = 0,
    CONCAT('ALTER TABLE `', @tablename, '` 
        ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL'
    ),
    'SELECT "Columns already exist" AS message'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add timestamps and soft delete to administrateurs if they don't exist
SET @tablename = 'administrateurs';
SET @prepStatement = CONCAT(
    'SELECT COUNT(*) INTO @exists FROM information_schema.COLUMNS WHERE ',
    'TABLE_SCHEMA = "', @dbname, '" AND ',
    'TABLE_NAME = "', @tablename, '" AND ',
    'COLUMN_NAME = "created_at"'
);

PREPARE stmt FROM @prepStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    @exists = 0,
    CONCAT('ALTER TABLE `', @tablename, '` 
        ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL'
    ),
    'SELECT "Columns already exist" AS message'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key constraints if they don't exist
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

-- Create indexes if they don't exist
CREATE INDEX IF NOT EXISTS `idx_stagiaires_email` ON `stagiaires`(`email`);
CREATE INDEX IF NOT EXISTS `idx_stagiaires_statut` ON `stagiaires`(`statut`);
CREATE INDEX IF NOT EXISTS `idx_responsables_email` ON `responsables`(`email`);
CREATE INDEX IF NOT EXISTS `idx_administrateurs_email` ON `administrateurs`(`email`);
CREATE INDEX IF NOT EXISTS `idx_stagiaires_deleted_at` ON `stagiaires`(`deleted_at`);
CREATE INDEX IF NOT EXISTS `idx_responsables_deleted_at` ON `responsables`(`deleted_at`);
CREATE INDEX IF NOT EXISTS `idx_administrateurs_deleted_at` ON `administrateurs`(`deleted_at`);
