# Local-Protecte - Plan de Développement

## 1. Analyse du Projet

### Description
Une plateforme web permettant aux citoyens de signaler les problèmes locaux (environnement, voirie, pollution, etc.) pour contribuer à l'amélioration de leur cadre de vie.

### Fonctionnalités Principales
1. **Authentification**: Inscription/Connexion des utilisateurs
2. **Signalement**: Soumission de problèmes avec photos, localisation, catégorie
3. **Tableau de bord**: Consultation des signalements soumis
4. **Suivi**: Visualisation du statut des signalements (En attente, En cours, Résolu)
5. **Admin**: Interface d'administration pour gérer les signalements

---

## 2. Structure des Fichiers

```
/home/enock/Bureau/Local-Protecte/
├── config/
│   └── db.php              # Connexion à la base de données
├── includes/
│   ├── header.php          # En-tête commun
│   └── footer.php          # Pied de page commun
├── css/
│   └── style.css           # Styles globaux
├── js/
│   └── main.js             # Scripts JavaScript
├── assets/
│   └── images/             # Images et icônes
├── uploads/                # Dossier des images uploadées
├── admin/                  # Panel admin
│   ├── index.php           # Tableau de bord admin
│   └── manage-reports.php  # Gestion des signalements
├── index.php               # Page d'accueil
├── login.php               # Connexion
├── register.php            # Inscription
├── dashboard.php           # Tableau de bord utilisateur
├── report.php              # Soumettre un signalement
├── report-detail.php       # Détail d'un signalement
├── logout.php              # Déconnexion
└── TODO.md                 # Ce fichier
```

---

## 3. Base de Données

### Tables SQL
1. **users**: id, name, email, password, role, created_at
2. **reports**: id, user_id, title, description, category, location, image, status, created_at, updated_at
3. **categories**: id, name, icon, created_at

---

## 4. Plan d'Implémentation

### Étape 1: Configuration et Base de données
- [x] Créer le fichier config/db.php
- [x] Créer la base de données et les tables (database.sql)

### Étape 2: Fichiers Communs
- [x] Créer includes/header.php
- [x] Créer includes/footer.php

### Étape 3: Styles CSS
- [x] Créer css/style.css avec design moderne

### Étape 4: Authentification
- [x] Créer register.php (inscription)
- [x] Créer login.php (connexion)
- [x] Créer logout.php (déconnexion)

### Étape 5: Pages Principales
- [x] Créer index.php (page d'accueil)
- [x] Créer dashboard.php (tableau de bord utilisateur)
- [x] Créer report.php (formulaire de signalement)
- [x] Créer report-detail.php (détail d'un signalement)

### Étape 6: Panel Admin
- [x] Créer admin/index.php
- [x] Créer admin/manage-reports.php

### Étape 7: JavaScript
- [x] Créer js/main.js pour les interactions

---

## 5. Technologies Utilisées
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 7+
- **Base de données**: MySQL
- **Design**: Moderne, Responsive, Accessibilité

