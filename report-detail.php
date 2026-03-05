<?php
// report-detail.php - Détail d'un signalement
require_once 'config/db.php';

$report_id = $_GET['id'] ?? 0;

// Récupérer le signalement
try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.name as user_name, u.email as user_email 
        FROM reports r 
        LEFT JOIN users u ON r.user_id = u.id 
        WHERE r.id = ?
    ");
    $stmt->execute([$report_id]);
    $report = $stmt->fetch();
    
    if (!$report) {
        $error = "Signalement non trouvé.";
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération du signalement.";
}

$error = $error ?? '';

include 'includes/header.php';
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <?php if ($error): ?>
        <div class="alert alert-error" style="max-width: 900px; margin: 2rem auto;">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <div class="text-center">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Retour à l'accueil
            </a>
        </div>
    <?php elseif ($report): ?>
        <div class="report-detail">
            <!-- Report Header -->
            <div class="report-detail-header">
                <?php if ($report['image']): ?>
                    <div class="report-detail-image">
                        <img src="uploads/<?= htmlspecialchars($report['image']) ?>" alt="<?= htmlspecialchars($report['title']) ?>">
                    </div>
                <?php else: ?>
                    <div class="report-detail-image" style="background: linear-gradient(135deg, var(--primary-light), var(--primary-color)); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-image" style="font-size: 4rem; color: var(--white); opacity: 0.5;"></i>
                    </div>
                <?php endif; ?>
                
                <div class="report-detail-content">
                    <span class="category-badge" style="display: inline-block; margin-bottom: 1rem;">
                        <?= htmlspecialchars(ucfirst($report['category'])) ?>
                    </span>
                    
                    <h1><?= htmlspecialchars($report['title']) ?></h1>
                    
                    <div class="report-detail-meta">
                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($report['user_name'] ?? 'Anonyme') ?></span>
                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y à H:i', strtotime($report['created_at'])) ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($report['location'] ?? 'Non spécifié') ?></span>
                        <span class="status-badge status-<?= $report['status'] ?>" style="font-size: 0.9rem;">
                            <?= match($report['status']) {
                                'pending' => 'En attente',
                                'in_progress' => 'En cours',
                                'resolved' => 'Résolu',
                                default => 'En attente'
                            } ?>
                        </span>
                    </div>
                    
                    <div class="report-description">
                        <h3>Description</h3>
                        <p><?= nl2br(htmlspecialchars($report['description'])) ?></p>
                    </div>
                    
                    <!-- Timeline -->
                    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--light-color);">
                        <h3 style="margin-bottom: 1rem;">Historique</h3>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 200px; padding: 1rem; background: var(--light-color); border-radius: var(--border-radius);">
                                <i class="fas fa-clock" style="color: var(--gray-color);"></i>
                                <strong>Créé le</strong><br>
                                <span style="color: var(--gray-color);"><?= date('d/m/Y à H:i', strtotime($report['created_at'])) ?></span>
                            </div>
                            <?php if ($report['updated_at']): ?>
                            <div style="flex: 1; min-width: 200px; padding: 1rem; background: var(--light-color); border-radius: var(--border-radius);">
                                <i class="fas fa-edit" style="color: var(--gray-color);"></i>
                                <strong>Dernière mise à jour</strong><br>
                                <span style="color: var(--gray-color);"><?= date('d/m/Y à H:i', strtotime($report['updated_at'])) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="report-actions">
                        <?php if (isLoggedIn() && $_SESSION['user_id'] == $report['user_id']): ?>
                            <a href="dashboard.php" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Retour au dashboard
                            </a>
                        <?php else: ?>
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Retour à l'accueil
                            </a>
                        <?php endif; ?>
                        
                        <!-- Share buttons -->
                        <button class="btn btn-outline" onclick="shareReport()">
                            <i class="fas fa-share-alt"></i> Partager
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Comments Section (for future implementation) -->
            <!--
            <div class="report-detail-header" style="margin-top: 2rem;">
                <div class="report-detail-content">
                    <h3>Commentaires</h3>
                    <p style="color: var(--gray-color);">Fonctionnalité à venir...</p>
                </div>
            </div>
            -->
        </div>
    <?php endif; ?>
</div>

<script>
function shareReport() {
    if (navigator.share) {
        navigator.share({
            title: '<?= htmlspecialchars($report['title'] ?? 'Signalement') ?>',
            text: '<?= htmlspecialchars($report['description'] ?? '') ?>',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Lien copié dans le presse-papiers!');
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>

