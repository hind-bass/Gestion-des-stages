-- This migration adds foreign key constraints after tables have been converted to InnoDB
-- It checks if each constraint exists before attempting to add it

-- Stagiaires table
SET @dbname = DATABASE();
SET @tablename = 'stagiaires';
SET @constraintname = 'fk_stagiaires_responsable';

SELECT COUNT(*) INTO @constraint_exists
FROM information_schema.TABLE_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = @dbname
AND TABLE_NAME = @tablename 
AND CONSTRAINT_NAME = @constraintname
AND CONSTRAINT_TYPE = 'FOREIGN KEY';

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE stagiaires 
    ADD CONSTRAINT fk_stagiaires_responsable 
    FOREIGN KEY (responsable_id) REFERENCES responsables(id) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE',
    'SELECT "Foreign key fk_stagiaires_responsable already exists" AS message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Affectations table
-- fk_affectations_stagiaire
SET @tablename = 'affectations';
SET @constraintname = 'fk_affectations_stagiaire';

SELECT COUNT(*) INTO @constraint_exists
FROM information_schema.TABLE_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = @dbname
AND TABLE_NAME = @tablename 
AND CONSTRAINT_NAME = @constraintname
AND CONSTRAINT_TYPE = 'FOREIGN KEY';

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE affectations 
    ADD CONSTRAINT fk_affectations_stagiaire 
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE',
    'SELECT "Foreign key fk_affectations_stagiaire already exists" AS message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- fk_affectations_responsable
SET @constraintname = 'fk_affectations_responsable';

SELECT COUNT(*) INTO @constraint_exists
FROM information_schema.TABLE_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = @dbname
AND TABLE_NAME = @tablename 
AND CONSTRAINT_NAME = @constraintname
AND CONSTRAINT_TYPE = 'FOREIGN KEY';

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE affectations 
    ADD CONSTRAINT fk_affectations_responsable 
    FOREIGN KEY (responsable_id) REFERENCES responsables(id) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE',
    'SELECT "Foreign key fk_affectations_responsable already exists" AS message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Continue with other tables following the same pattern
-- Add more foreign key constraints as needed
