-- This migration converts all tables to InnoDB engine
-- It's a clean version without any foreign key constraints

-- Convert all tables to InnoDB
ALTER TABLE notifications ENGINE=InnoDB;
ALTER TABLE attestations ENGINE=InnoDB;
ALTER TABLE demandes ENGINE=InnoDB;
ALTER TABLE documents ENGINE=InnoDB;
ALTER TABLE documents_stagiaire ENGINE=InnoDB;
ALTER TABLE rapports ENGINE=InnoDB;
ALTER TABLE services ENGINE=InnoDB;
ALTER TABLE responsables ENGINE=InnoDB;
ALTER TABLE stagiaires ENGINE=InnoDB;
ALTER TABLE stages ENGINE=InnoDB;
ALTER TABLE affectations ENGINE=InnoDB;
ALTER TABLE decisions ENGINE=InnoDB;
ALTER TABLE remarques ENGINE=InnoDB;
ALTER TABLE stagiaires_passes ENGINE=InnoDB;
ALTER TABLE suivi_stagiaire ENGINE=InnoDB;
ALTER TABLE taches ENGINE=InnoDB;
