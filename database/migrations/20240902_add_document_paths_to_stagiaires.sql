ALTER TABLE stagiaires
ADD COLUMN cv_path VARCHAR(255) NULL AFTER cin,
ADD COLUMN demande_stage_path VARCHAR(255) NULL AFTER cv_path,
ADD COLUMN assurance_path VARCHAR(255) NULL AFTER demande_stage_path,
ADD COLUMN cin_scan_path VARCHAR(255) NULL AFTER assurance_path;
