<?php
// index.php - Page d'accueil
require_once 'config/db.php';

// Récupérer les catégories
$categories = [
    ['id' => 'environnement', 'name' => 'Environnement', 'icon' => 'fa-leaf'],
    ['id' => 'voirie', 'name' => 'Voirie', 'icon' => 'fa-road'],
    ['id' => 'eclairage', 'name' => 'Éclairage', 'icon' => 'fa-lightbulb'],
    ['id' => 'espaces_verts', 'name' => 'Espaces verts', 'icon' => 'fa-tree'],
    ['id' => 'dechets', 'name' => 'Déchets', 'icon' => 'fa-trash'],
    ['id' => 'pollution', 'name' => 'Pollution', 'icon' => 'fa-smog'],
    ['id' => 'bruit', 'name' => 'Nuisances sonores', 'icon' => 'fa-volume-up'],
    ['id' => 'autres', 'name' => 'Autres', 'icon' => 'fa-ellipsis-h']
];

// Requête pour récupérer les signalements (publics)
try {
    $stmt = $pdo->query("
        SELECT r.*, u.name as user_name, c.name as category_name 
        FROM reports r 
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN categories c ON r.category = c.id
        ORDER BY r.created_at DESC 
        LIMIT 12
    ");
    $reports = $stmt->fetchAll();
} catch (PDOException $e) {
    $reports = [];
}

// Statistiques
try {
    $totalReports = $pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn();
    $resolvedReports = $pdo->query("SELECT COUNT(*) FROM reports WHERE status = 'resolved'")->fetchColumn();
} catch (PDOException $e) {
    $totalReports = 0;
    $resolvedReports = 0;
}

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content container">
        <h1>Signalez les problèmes de votre quartier</h1>
        <p>Participez à l'amélioration de votre cadre de vie en signalant les problèmes locaux. Ensemble, rendons notre environnement meilleur.</p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="report.php" class="btn btn-white">
                <i class="fas fa-exclamation-circle"></i> Signaler un problème
            </a>
            <a href="#reports" class="btn btn-outline-white">
                <i class="fas fa-search"></i> Voir les signalements
            </a>
        </div>
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-number"><?= $totalReports ?></span>
                <span class="stat-label">Signalements</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= $resolvedReports ?></span>
                <span class="stat-label">Résolus</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= date('Y') ?></span>
                <span class="stat-label">Année</span>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <form action="index.php" method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Rechercher un signalement..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Rechercher
            </button>
        </form>
    </div>
</section>

<!-- Categories Section -->
<section class="reports-section">
    <div class="container">
        <div class="section-title">
            <h2>Catégories</h2>
            <p>Choisissez une catégorie pour voir les signalements</p>
        </div>
        <div class="filters" style="margin-bottom: 2rem;">
            <button class="filter-btn active" data-category="all">Tous</button>
            <?php foreach ($categories as $cat): ?>
                <button class="filter-btn" data-category="<?= $cat['id'] ?>">
                    <i class="fas <?= $cat['icon'] ?>"></i> <?= htmlspecialchars($cat['name']) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Reports Section -->
<section id="reports" class="reports-section">
    <div class="container">
        <div class="section-title">
            <h2>Signalements Récents</h2>
            <p>Découvrez les derniers problèmes signalés dans votre quartier</p>
        </div>
        
        <?php if (empty($reports)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Aucun signalement</h3>
                <p>Soyez le premier à signaler un problème dans votre quartier!</p>
                <a href="report.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer un signalement
                </a>
            </div>
        <?php else: ?>
            <div class="reports-grid">
                <?php foreach ($reports as $report): ?>
                    <article class="report-card" data-category="<?= htmlspecialchars($report['category']) ?>">
                        <div class="report-image">
                            <?php if ($report['image']): ?>
                                <img src="uploads/<?= htmlspecialchars($report['image']) ?>" alt="<?= htmlspecialchars($report['title']) ?>">
                            <?php else: ?>
                                <img src="assets/images/placeholder.jpg" alt="Image par défaut">
                            <?php endif; ?>
                            <span class="category-badge"><?= htmlspecialchars($report['category_name'] ?? $report['category']) ?></span>
                        </div>
                        <div class="report-content">
                            <h3><?= htmlspecialchars($report['title']) ?></h3>
                            <p><?= htmlspecialchars($report['description']) ?></p>
                            <div class="report-meta">
                                <span><i class="fas fa-user"></i> <?= htmlspecialchars($report['user_name'] ?? 'Anonyme') ?></span>
                                <span class="status-badge status-<?= $report['status'] ?>">
                                    <?= match($report['status']) {
                                        'pending' => 'En attente',
                                        'in_progress' => 'En cours',
                                        'resolved' => 'Résolu',
                                        default => 'En attente'
                                    } ?>
                                </span>
                            </div>
                            <div class="report-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($report['location'] ?? 'Non spécifié') ?>
                            </div>
                            <a href="report-detail.php?id=<?= $report['id'] ?>" class="btn btn-small btn-outline mt-2" style="width: 100%;">
                                Voir détails
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (count($reports) >= 12): ?>
            <div class="text-center mt-4">
                <a href="dashboard.php" class="btn btn-primary">
                    Voir tous les signalements <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- How It Works Section -->
<section class="reports-section" style="background: var(--white);">
    <div class="container">
        <div class="section-title">
            <h2>Comment ça marche?</h2>
            <p>Three étapes simples pour signaler un problème</p>
        </div>
        <div class="reports-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            <div class="report-card" style="text-align: center;">
                <div class="report-content">
                    <div style="width: 80px; height: 80px; background: var(--primary-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-camera" style="font-size: 2rem; color: var(--primary-dark);"></i>
                    </div>
                    <h3>1. Photographiez</h3>
                    <p>Prenez une photo du problème que vous souhaitez signaler.</p>
                </div>
            </div>
            <div class="report-card" style="text-align: center;">
                <div class="report-content">
                    <div style="width: 80px; height: 80px; background: var(--primary-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-edit" style="font-size: 2rem; color: var(--primary-dark);"></i>
                    </div>
                    <h3>2. Décrivez</h3>
                    <p>Donnez des détails sur le problème et sa localisation.</p>
                </div>
            </div>
            <div class="report-card" style="text-align: center;">
                <div class="report-content">
                    <div style="width: 80px; height: 80px; background: var(--primary-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-paper-plane" style="font-size: 2rem; color: var(--primary-dark);"></i>
                    </div>
                    <h3>3. Soumettez</h3>
                    <p>Envoyez votre signalement et suivez son évolution.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.btn-white {
    background: var(--white);
    color: var(--primary-color);
}
.btn-white:hover {
    background: var(--light-color);
    color: var(--primary-dark);
}
.btn-outline-white {
    background: transparent;
    border: 2px solid var(--white);
    color: var(--white);
}
.btn-outline-white:hover {
    background: var(--white);
    color: var(--primary-color);
}
</style>

<?php include 'includes/footer.php'; ?>

