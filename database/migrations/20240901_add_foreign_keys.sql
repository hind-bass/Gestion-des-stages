-- Add foreign key constraints

-- Stagiaires table
ALTER TABLE stagiaires
ADD CONSTRAINT fk_stagiaires_responsable
FOREIGN KEY (responsable_id) REFERENCES responsables(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Affectations table
ALTER TABLE affectations
ADD CONSTRAINT fk_affectations_stagiaire
FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
ADD CONSTRAINT fk_affectations_responsable
FOREIGN KEY (responsable_id) REFERENCES responsables(id)
ON DELETE CASCADE
ON UPDATE CASCADE;

-- Decisions table
ALTER TABLE decisions
ADD CONSTRAINT fk_decisions_stagiaire
FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
ADD CONSTRAINT fk_decisions_responsable
FOREIGN KEY (responsable_id) REFERENCES responsables(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Documents table
ALTER TABLE documents
ADD CONSTRAINT fk_documents_stagiaire
FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
ON DELETE CASCADE
ON UPDATE CASCADE;

-- Documents_stagiaire table
ALTER TABLE documents_stagiaire
ADD CONSTRAINT fk_documents_stagiaire_stagiaire
FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
ON DELETE CASCADE
ON UPDATE CASCADE;

-- Rapports table
ALTER TABLE rapports
ADD CONSTRAINT fk_rapports_stagiaire
FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
ON DELETE CASCADE
ON UPDATE CASCADE;

-- Remarques table
ALTER TABLE remarques
ADD CONSTRAINT fk_remarques_responsable
FOREIGN KEY (responsable_id) REFERENCES responsables(id)
ON DELETE SET NULL
ON UPDATE CASCADE,
ADD CONSTRAINT fk_remarques_stage
FOREIGN KEY (stage_id) REFERENCES stages(id)
ON DELETE CASCADE
ON UPDATE CASCADE;

-- Services table
ALTER TABLE services
ADD CONSTRAINT fk_services_responsable
FOREIGN KEY (responsable_id) REFERENCES responsables(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Stages table
ALTER TABLE stages
ADD CONSTRAINT fk_stages_stagiaire
FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
ADD CONSTRAINT fk_stages_responsable
FOREIGN KEY (responsable_id) REFERENCES responsables(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Stagiaires_passes table
ALTER TABLE stagiaires_passes
ADD CONSTRAINT fk_stagiaires_passes_stagiaire
FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
ADD CONSTRAINT fk_stagiaires_passes_responsable
FOREIGN KEY (responsable_id) REFERENCES responsables(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Suivi_stagiaire table
ALTER TABLE suivi_stagiaire
ADD CONSTRAINT fk_suivi_stagiaire_stagiaire
FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
ADD CONSTRAINT fk_suivi_stagiaire_responsable
FOREIGN KEY (responsable_id) REFERENCES responsables(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Taches table
ALTER TABLE taches
ADD CONSTRAINT fk_taches_stage
FOREIGN KEY (stage_id) REFERENCES stages(id)
ON DELETE CASCADE
ON UPDATE CASCADE;
