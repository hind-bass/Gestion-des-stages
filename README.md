# 🏛️ Gestion des Stages - Commune d'Oujda

## 📋 Description

Système de gestion des stages pour la Commune Urbaine d'Oujda. Cette application permet la gestion complète du cycle de vie des stages, de l'inscription à la validation finale.

## 🏗️ Architecture

Le projet suit une architecture MVC moderne avec une organisation claire par fonctionnalité :

```
Projet4/
├── 📁 config/                    # Configuration centralisée
│   ├── database.php             # Paramètres base de données
│   ├── email.php                # Configuration email
│   ├── app.php                  # Configuration générale
│   └── constants.php            # Constantes de l'application
│
├── 📁 core/                      # Fonctionnalités centrales
│   ├── Database.php             # Classe DB (Pattern Singleton)
│   ├── Autoloader.php           # Chargement automatique des classes
│   └── BaseController.php       # Contrôleur de base
│
├── 📁 models/                    # Modèles de données
│   ├── BaseModel.php            # Modèle de base (CRUD)
│   ├── Stagiaire.php            # Gestion des stagiaires
│   ├── Responsable.php          # Gestion des responsables
│   └── Tache.php                # Gestion des tâches
│
├── 📁 controllers/               # Contrôleurs organisés par rôle
│   ├── BaseController.php       # Contrôleur de base
│   ├── 📁 admin/                # Contrôleurs administrateur
│   ├── 📁 responsable/          # Contrôleurs responsable
│   └── 📁 stagiaire/            # Contrôleurs stagiaire
│
├── 📁 views/                     # Vues organisées
│   ├── 📁 layouts/              # Layouts de base
│   ├── 📁 admin/                # Vues administrateur
│   ├── 📁 responsable/          # Vues responsable
│   ├── 📁 stagiaire/            # Vues stagiaire
│   └── 📁 components/           # Composants réutilisables
│
├── 📁 public/                    # Fichiers publics
│   ├── index.php                # Point d'entrée unique
│   ├── .htaccess                # Routage et sécurité
│   └── 📁 assets/               # Ressources statiques
│
├── 📁 utils/                     # Utilitaires
│   ├── helpers.php              # Fonctions utilitaires
│   └── generators.php           # Générateurs d'attestations
│
├── 📁 database/                  # Base de données
│   ├── schema.sql               # Schéma complet
│   └── 📁 seeds/                # Données de test
│
├── 📁 tests/                     # Tests
├── 📁 logs/                      # Logs
├── 📁 docs/                      # Documentation
└── 📁 api/                       # API REST organisée
```

## 🚀 Installation

### Prérequis
- PHP 7.4+
- MySQL 5.7+
- Serveur web (Apache/Nginx)

### Installation
1. **Cloner le projet**
   ```bash
   git clone [url-du-projet]
   cd Projet4
   ```

2. **Configurer la base de données**
   - Créer une base de données MySQL
   - Importer le schéma : `database/schema.sql`
   - Configurer les paramètres dans `config/database.php`

3. **Configurer le serveur web**
   - Pointer le DocumentRoot vers le dossier `public/`
   - Activer la réécriture d'URL (mod_rewrite)

4. **Permissions**
   ```bash
   chmod 755 public/uploads/
   chmod 755 logs/
   ```

## 🔧 Configuration

### Base de données
Éditer `config/database.php` :
```php
return [
    'host' => 'localhost',
    'dbname' => 'gestion_stages',
    'username' => 'votre_utilisateur',
    'password' => 'votre_mot_de_passe',
    'charset' => 'utf8mb4'
];
```

### Email
Éditer `config/email.php` :
```php
return [
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => 'votre-email@gmail.com',
        'password' => 'votre-mot-de-passe-app'
    ]
];
```

## 👥 Rôles Utilisateurs

### 1. **Administrateur**
- Gestion complète des stagiaires
- Gestion des responsables
- Validation/refus des demandes
- Statistiques et rapports
- Génération d'attestations

### 2. **Responsable**
- Gestion des stagiaires assignés
- Attribution de tâches
- Suivi des progrès
- Ajout de remarques
- Validation des rapports

### 3. **Stagiaire**
- Inscription et profil
- Téléchargement de documents
- Consultation des tâches
- Suivi des remarques
- Accès aux attestations

## 🔄 Fonctionnalités Principales

### Gestion des Stagiaires
- ✅ Inscription en ligne
- ✅ Validation/refus des demandes
- ✅ Attribution à un responsable
- ✅ Suivi des documents
- ✅ Génération d'attestations

### Gestion des Tâches
- ✅ Création de tâches par les responsables
- ✅ Attribution aux stagiaires
- ✅ Suivi des statuts
- ✅ Remarques et évaluations

### Génération d'Attestations
- ✅ Attestation de début de stage
- ✅ Attestation de fin de stage
- ✅ Formats PDF et HTML
- ✅ Support bilingue (FR/AR)

### Système de Notifications
- ✅ Emails automatiques
- ✅ Notifications en temps réel
- ✅ Historique des actions

## 🛠️ Développement

### Structure des Modèles
```php
// Exemple d'utilisation du modèle Stagiaire
$stagiaireModel = new Stagiaire();
$stagiaires = $stagiaireModel->getEnAttente();
$stagiaire = $stagiaireModel->find($id);
```

### Structure des Contrôleurs
```php
// Exemple de contrôleur
class AdminController extends BaseController
{
    public function dashboard()
    {
        $this->requireRole('admin');
        $this->view('admin/dashboard', ['data' => $data]);
    }
}
```

### Validation des Données
```php
$rules = [
    'email' => 'required|email',
    'nom' => 'required|min:2|max:100'
];
$errors = $this->validate($data, $rules);
```

## 📊 Base de Données

### Tables Principales
- `stagiaires` : Informations des stagiaires
- `responsables` : Informations des responsables
- `taches` : Tâches assignées aux stagiaires
- `documents` : Documents uploadés
- `attestations` : Attestations générées
- `remarques` : Remarques des responsables

## 🔒 Sécurité

- ✅ Validation des entrées utilisateur
- ✅ Protection CSRF
- ✅ Authentification par rôle
- ✅ Logging des actions
- ✅ Gestion sécurisée des uploads

## 📈 Performance

- ✅ Pattern Singleton pour la DB
- ✅ Autoloader automatique
- ✅ Cache des requêtes fréquentes
- ✅ Optimisation des requêtes SQL

## 🧪 Tests

Exécuter les tests :
```bash
php tests/check_system.php
php tests/diagnostic_db.php
```

## 📚 Documentation

- `docs/README.md` : Documentation générale
- `docs/MIGRATION_GUIDE.md` : Guide de migration
- `RESUME_FINAL_REORGANISATION.md` : Résumé de la réorganisation

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Commit les changements (`git commit -am 'Ajout nouvelle fonctionnalité'`)
4. Push vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créer une Pull Request

## 📄 Licence

Ce projet est développé pour la Commune Urbaine d'Oujda.

## 📞 Support

Pour toute question ou problème :
- Email : [email-support]
- Documentation : `docs/`
- Issues : [url-issues]

---

**🏛️ Commune Urbaine d'Oujda - Système de Gestion des Stages**
