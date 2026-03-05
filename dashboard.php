<?php
// dashboard.php - Tableau de bord utilisateur
require_once 'config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Statistiques de l'utilisateur
try {
    $totalReports = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE user_id = ?");
    $totalReports->execute([$user_id]);
    $totalCount = $totalReports->fetchColumn();
    
    $pendingReports = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE user_id = ? AND status = 'pending'");
    $pendingReports->execute([$user_id]);
    $pendingCount = $pendingReports->fetchColumn();
    
    $resolvedReports = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE user_id = ? AND status = 'resolved'");
    $resolvedReports->execute([$user_id]);
    $resolvedCount = $resolvedReports->fetchColumn();
    
    $inProgressReports = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE user_id = ? AND status = 'in_progress'");
    $inProgressReports->execute([$user_id]);
    $inProgressCount = $inProgressReports->fetchColumn();
    
    // Récupérer les signalements de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $reports = $stmt->fetchAll();
} catch (PDOException $e) {
    $reports = [];
    $totalCount = 0;
    $pendingCount = 0;
    $resolvedCount = 0;
    $inProgressCount = 0;
}

include 'includes/header.php';
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1><i class="fas fa-tachometer-alt"></i> Mon Tableau de Bord</h1>
        <p>Bienvenue, <?= htmlspecialchars($_SESSION['user_name']) ?>! Gérez vos signalements ici.</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <i class="fas fa-list"></i>
            <div class="stat-info">
                <h3><?= $totalCount ?></h3>
                <p>Total signalements</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock" style="background: #fff3cd; color: #856404;"></i>
            <div class="stat-info">
                <h3><?= $pendingCount ?></h3>
                <p>En attente</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-spinner" style="background: #cce5ff; color: #004085;"></i>
            <div class="stat-info">
                <h3><?= $inProgressCount ?></h3>
                <p>En cours</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle" style="background: #d4edda; color: #155724;"></i>
            <div class="stat-info">
                <h3><?= $resolvedCount ?></h3>
                <p>Résolus</p>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h2><i class="fas fa-folder-open"></i> Mes Signalements</h2>
        <a href="report.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau signalement
        </a>
    </div>
    
    <!-- Reports List -->
    <?php if (empty($reports)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Aucun signalement</h3>
            <p>Vous n'avez pas encore soumis de signalement.</p>
            <a href="report.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Créer un signalement
            </a>
        </div>
    <?php else: ?>
        <div class="reports-grid">
            <?php foreach ($reports as $report): ?>
                <article class="report-card">
                    <div class="report-image">
                        <?php if ($report['image']): ?>
                            <img src="uploads/<?= htmlspecialchars($report['image']) ?>" alt="<?= htmlspecialchars($report['title']) ?>">
                        <?php else: ?>
                            <img src="assets/images/placeholder.jpg" alt="Image par défaut">
                        <?php endif; ?>
                        <span class="category-badge"><?= htmlspecialchars(ucfirst($report['category'])) ?></span>
                    </div>
                    <div class="report-content">
                        <h3><?= htmlspecialchars($report['title']) ?></h3>
                        <p><?= htmlspecialchars($report['description']) ?></p>
                        <div class="report-meta">
                            <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($report['created_at'])) ?></span>
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
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <a href="report-detail.php?id=<?= $report['id'] ?>" class="btn btn-small btn-outline" style="flex: 1;">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

