<?php
// header.php - En-tête commun du site
require_once __DIR__ . '/../config/db.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local-Protecte - Signalez les problèmes locaux</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts crossorigin>
    <link href=".gstatic.com"https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php">
                    <i class="fas fa-leaf"></i>
                    <span>Local-Protecte</span>
                </a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="<?= $current_page === 'index' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Accueil
                </a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="dashboard.php" class="<?= $current_page === 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a></li>
                    <li><a href="report.php" class="<?= $current_page === 'report' ? 'active' : '' ?>">
                        <i class="fas fa-exclamation-circle"></i> Signaler
                    </a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/index.php" class="<?= strpos($current_page, 'admin') !== false ? 'active' : '' ?>">
                            <i class="fas fa-cog"></i> Administration
                        </a></li>
                    <?php endif; ?>
                    <li class="user-menu">
                        <a href="#" class="user-trigger">
                            <i class="fas fa-user-circle"></i>
                            <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur') ?></span>
                        </a>
                        <ul class="user-dropdown">
                            <li><a href="dashboard.php"><i class="fas fa-user"></i> Mon profil</a></li>
                            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="login.php" class="<?= $current_page === 'login' ? 'active' : '' ?>">
                        <i class="fas fa-sign-in-alt"></i> Connexion
                    </a></li>
                    <li><a href="register.php" class="btn-primary-small <?= $current_page === 'register' ? 'active' : '' ?>">
                        <i class="fas fa-user-plus"></i> Inscription
                    </a></li>
                <?php endif; ?>
            </ul>
            <button class="mobile-menu-btn" aria-label="Menu mobile">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
    <main>

