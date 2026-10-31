# Taskify - Plateforme de Gestion des Stages

## Description
Taskify est une plateforme moderne de gestion des stages avec suivi du bien-être des employés et des stagiaires. Le projet utilise PHP, MySQL et un design moderne inspiré des meilleures pratiques UX/UI.

## Structure du Projet

```
Projet4/
├── assets/                 # Ressources statiques
│   ├── css/               # Styles CSS
│   │   ├── dashboard.css  # Styles du tableau de bord moderne
│   │   └── style.css      # Styles généraux
│   ├── js/                # Scripts JavaScript
│   │   └── dashboard.js   # Logique du tableau de bord
│   ├── images/            # Images et logos
│   └── fonts/             # Polices personnalisées
├── api/                   # API endpoints
├── modals/                # Modales et popups
├── uploads/               # Fichiers uploadés
├── vendor/                # Dépendances Composer
├── administrateur_modern.php  # Nouveau tableau de bord moderne
├── config.php             # Configuration de la base de données
├── functions.php          # Fonctions utilitaires
└── README.md             # Documentation
```

## Fonctionnalités

### Tableau de Bord Moderne
- **Design Taskify** : Interface moderne inspirée des meilleures pratiques UX
- **Suivi des Humeurs** : Analyse du bien-être des employés
- **Statistiques en Temps Réel** : Métriques clés de la plateforme
- **Filtres Avancés** : Filtrage par employé, type de feedback, période
- **Alertes Intelligentes** : Notifications pour les situations critiques

### Gestion des Utilisateurs
- **Administrateurs** : Gestion complète de la plateforme
- **Responsables** : Gestion des stagiaires et services
- **Stagiaires** : Espace personnel avec suivi des stages

### Fonctionnalités Avancées
- **Génération d'Attestations** : PDF automatiques en français et arabe
- **Système d'Email** : Notifications automatiques
- **Upload de Documents** : Gestion sécurisée des fichiers
- **Interface Responsive** : Compatible mobile et desktop

## Installation

### Prérequis
- PHP 7.4+
- MySQL 5.7+
- Serveur web (Apache/Nginx)
- Composer (pour les dépendances)

### Configuration

1. **Base de données**
   ```sql
   -- Créer la base de données
   CREATE DATABASE gestion_stages;
   
   -- Importer les tables
   mysql -u root -p gestion_stages < baseDonne.sql
   ```

2. **Configuration PHP**
   ```php
   // config.php
   $host = 'localhost';
   $dbname = 'gestion_stages';
   $username = 'root';
   $password_db = '';
   ```

3. **Dépendances**
   ```bash
   composer install
   ```

### Démarrage

1. Placer le projet dans le répertoire web
2. Configurer la base de données
3. Accéder à `login_administrateur.php`
4. Utiliser les identifiants par défaut

## Design System

### Couleurs
- **Primary** : `#6366f1` (Indigo)
- **Secondary** : `#8b5cf6` (Purple)
- **Success** : `#10b981` (Green)
- **Warning** : `#f59e0b` (Yellow)
- **Danger** : `#ef4444` (Red)

### Typographie
- **Font** : Inter (Google Fonts)
- **Weights** : 300, 400, 500, 600, 700

### Composants
- **Cards** : Ombres douces, bordures arrondies
- **Buttons** : Gradients, animations hover
- **Tables** : Design moderne avec hover effects
- **Sidebar** : Navigation collapsible

## API Endpoints

### Administrateur
- `api/admin_stagiaires.php` - Gestion des stagiaires
- `api/admin_services.php` - Gestion des services
- `api/admin_affectations.php` - Gestion des affectations

### Responsable
- `api/responsable_stagiaires.php` - Stagiaires du responsable
- `api/responsable_evaluations.php` - Évaluations

### Stagiaire
- `api/stagiaire_profile.php` - Profil stagiaire
- `api/stagiaire_documents.php` - Documents stagiaire

## Sécurité

### Authentification
- Sessions PHP sécurisées
- Hachage des mots de passe
- Protection contre les injections SQL

### Uploads
- Validation des types de fichiers
- Limitation de taille
- Stockage sécurisé

### Base de Données
- Requêtes préparées
- Échappement des données
- Contrôle d'accès

## Maintenance

### Logs
- Erreurs PHP dans `/logs/`
- Accès utilisateurs
- Actions administratives

### Sauvegarde
- Base de données : `baseDonne.sql`
- Fichiers uploadés : `/uploads/`
- Configuration : `config.php`

## Support

Pour toute question ou problème :
1. Vérifier les logs d'erreur
2. Tester la connexion à la base de données
3. Vérifier les permissions des dossiers
4. Consulter la documentation PHP/MySQL

## Licence

Ce projet est développé pour la gestion des stages de la Commune d'Oujda. 