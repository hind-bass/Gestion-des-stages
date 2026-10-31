use gestion_stages;
CREATE TABLE decisions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    responsable_id INT NOT NULL,
    decision ENUM('valide', 'refuse') NOT NULL,
    note_finale DECIMAL(5,2) DEFAULT NULL,
    date_decision DATETIME DEFAULT CURRENT_TIMESTAMP,
    commentaire TEXT,
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id),
    FOREIGN KEY (responsable_id) REFERENCES responsables(id)
);

CREATE TABLE suivi_stagiaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    responsable_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    date_action DATETIME DEFAULT CURRENT_TIMESTAMP,
    type_action ENUM('document', 'etape', 'autre') DEFAULT 'autre',
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id),
    FOREIGN KEY (responsable_id) REFERENCES responsables(id)
);