-- Migration pour ajouter les champs de token de rappel à la table stagiaires
-- Ajout des colonnes si elles n'existent pas déjà
use gestion_stages;
ALTER TABLE stagiaires 
ADD COLUMN remember_token VARCHAR(100) NULL DEFAULT NULL,
ADD COLUMN  remember_token_expires DATETIME NULL DEFAULT NULL,
ADD INDEX idx_remember_token (remember_token);

-- Commentaires pour la documentation
-- Ces champs sont utilisés pour la fonctionnalité "Se souvenir de moi"
-- remember_token: Stocke le jeton de session sécurisé
-- remember_token_expires: Date d'expiration du jeton
-- Un index est ajouté sur remember_token pour optimiser les recherches
