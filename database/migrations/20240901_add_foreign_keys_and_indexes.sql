-- Add foreign key to stagiaires table
ALTER TABLE stagiaires
ADD CONSTRAINT fk_stagiaires_responsable
FOREIGN KEY (responsable_id) REFERENCES responsables(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Add indexes for frequently queried columns
CREATE INDEX idx_stagiaires_email ON stagiaires(email);
CREATE INDEX idx_stagiaires_statut ON stagiaires(statut);
CREATE INDEX idx_responsables_email ON responsables(email);
CREATE INDEX idx_administrateurs_email ON administrateurs(email);

-- Add created_at and updated_at timestamps to all tables
ALTER TABLE stagiaires
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE responsables
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE administrateurs
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add soft delete columns
ALTER TABLE stagiaires
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE responsables
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE administrateurs
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
