-- Local-Protecte - Base de données
-- Installer ce fichier dans votre serveur MySQL

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS local_protecte CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE local_protecte;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user' COMMENT 'Rôle de l\'utilisateur',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-folder',
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des signalements
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    location VARCHAR(255),
    image VARCHAR(255),
    status ENUM('pending', 'in_progress', 'resolved') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer les catégories par défaut
INSERT INTO categories (name, icon, description) VALUES
('Environnement', 'fa-leaf', 'Problèmes liés à l\'environnement'),
('Voirie', 'fa-road', 'Problèmes de voirie et routes'),
('Éclairage', 'fa-lightbulb', 'Problèmes d\'éclairage public'),
('Espaces verts', 'fa-tree', 'Parcs, jardins et espaces verts'),
('Déchets', 'fa-trash', 'Encombrants et déchets'),
('Pollution', 'fa-smog', 'Pollution environnementale'),
('Nuisances sonores', 'fa-volume-up', 'Bruits excessifs'),
('Autres', 'fa-ellipsis-h', 'Autres problèmes');

-- Insérer un administrateur par défaut (mot de passe: admin123)
-- IMPORTANT: Changer le mot de passe après la première connexion!
INSERT INTO users (name, email, password, role) VALUES
('Administrateur', 'admin@local-protecte.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Exemple: Insérer des signalements de test (optionnel)
-- INSERT INTO reports (user_id, title, description, category, location, status) VALUES
-- (1, 'Lampadaire cassé', 'Le lampadaire du rue de la Paix ne fonctionne plus depuis une semaine.', 'eclairage', 'Rue de la Paix, Paris', 'pending'),
-- (1, 'Tas d\'ordures', 'Des déchets sont accumulés depuis plusieurs jours au coin de la rue.', 'dechets', 'Avenue Victor Hugo', 'in_progress'),
-- (1, 'Fuite d\'eau', 'Une fuite d\'eau importante sur la voie publique.', 'voirie', 'Boulevard Saint-Michel', 'resolved');

-- =============================================================================
-- INSTRUCTIONS D'INSTALLATION
-- =============================================================================
-- 
-- 1. Assurez-vous que MySQL ou MariaDB est installé sur votre serveur
-- 2. Créez un utilisateur MySQL avec les droits appropriés
-- 3. Exécutez ce fichier SQL via phpMyAdmin ou en ligne de commande:
--    mysql -u root -p < database.sql
-- 4. Modifiez le fichier config/db.php si nécessaire (hôte, utilisateur, mot de passe)
-- 5. Assurez-vous que le dossier 'uploads' est accessible en écriture
--
-- =============================================================================
-- COMPTE ADMINISTRATEUR PAR DÉFAUT
-- =============================================================================
-- Email: admin@local-protecte.fr
-- Mot de passe: admin123
--
-- IMPORTANT: Changez ce mot de passe immédiatement après la première connexion!
--
-- =============================================================================

