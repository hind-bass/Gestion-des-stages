-- Add created_at, updated_at, and deleted_at columns to all tables
ALTER TABLE stagiaires
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE responsables
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE administrateurs
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Add indexes for better performance
CREATE INDEX idx_stagiaires_deleted_at ON stagiaires(deleted_at);
CREATE INDEX idx_responsables_deleted_at ON responsables(deleted_at);
CREATE INDEX idx_administrateurs_deleted_at ON administrateurs(deleted_at);

-- Update existing records to have timestamps
UPDATE stagiaires SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL;
UPDATE responsables SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL;
UPDATE administrateurs SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL;
