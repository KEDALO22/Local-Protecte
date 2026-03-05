# Local-Protecte

Une plateforme web permettant à la population de signaler les problèmes locaux afin d'assurer la protection de l'environnement.

## Fonctionnalités

- **Inscription/Connexion** : Système d'authentification sécurisé
- **Signalement de problèmes** : Soumission avec photos, localisation et catégorie
- **Tableau de bord personnel** : Suivre l'état de vos signalements
- **Catégories multiples** : Environnement, voirie, éclairage, espaces verts, déchets, pollution, nuisances sonores
- **Suivi des statuts** : En attente, En cours, Résolu
- **Panel d'administration** : Gestion complète des signalements

## Structure du projet

```
local_protecte/
├── config/
│   └── db.php           # Configuration base de données
├── includes/
│   ├── header.php       # En-tête commun
│   └── footer.php       # Pied de page commun
├── css/
│   └── style.css        # Styles CSS
├── js/
│   └── main.js          # Scripts JavaScript
├── assets/
│   └── images/          # Images et icônes
├── uploads/             # Images uploadées
├── admin/               # Panel admin
│   ├── index.php        # Tableau de bord admin
│   └── manage-reports.php
├── index.php            # Page d'accueil
├── login.php            # Connexion
├── register.php         # Inscription
├── dashboard.php        # Tableau de bord utilisateur
├── report.php           # Nouveau signalement
├── report-detail.php    # Détail d'un signalement
├── logout.php           # Déconnexion
├── database.sql         # Script base de données
└── README.md            # Ce fichier
```

## Installation

### Prérequis

- Serveur web (Apache/Nginx)
- PHP 7.4+
- MySQL/MariaDB

### Étapes d'installation

1. **Copiez les fichiers** dans votre répertoire web (ex: `htdocs` ou `/var/www/html`)

2. **Configurez la base de données** :
   - Ouvrez phpMyAdmin ou utilisez la ligne de commande
   - Importez le fichier `database.sql`

3. **Configurez la connexion** (si nécessaire) :
   - Éditez le fichier `config/db.php`
   - Modifiez les constantes `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`

4. **Configurez les permissions** :
   - Le dossier `uploads/` doit être accessible en écriture

5. **Accédez à l'application** :
   - Ouvrez votre navigateur à l'URL du projet

### Compte administrateur par défaut

- **Email** : admin@local-protecte.fr
- **Mot de passe** : admin123

> ⚠️ **Important** : Changez ce mot de passe immédiatement après la première connexion!

## Technologies utilisées

- **Frontend** : HTML5, CSS3, JavaScript (Vanilla)
- **Backend** : PHP 7+
- **Base de données** : MySQL/MariaDB
- **Design** : Moderne, Responsive, Accessibilité

## Captures d'écran

L'application inclut :
- Design moderne avec palette de couleurs écologique (vert)
- Interface responsive pour mobile et tablette
- Tableau de bord avec statistiques
- Formulaires de signalement avec upload d'images
- Panel d'administration complet

## Licence

Ce projet est open source et disponible sous licence MIT.

