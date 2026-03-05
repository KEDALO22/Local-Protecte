<?php
// admin/index.php - Panel d'administration
require_once '../config/db.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    redirect('../index.php');
}

// Statistiques globales
try {
    $totalReports = $pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn();
    $pendingReports = $pdo->query("SELECT COUNT(*) FROM reports WHERE status = 'pending'")->fetchColumn();
    $inProgressReports = $pdo->query("SELECT COUNT(*) FROM reports WHERE status = 'in_progress'")->fetchColumn();
    $resolvedReports = $pdo->query("SELECT COUNT(*) FROM reports WHERE status = 'resolved'")->fetchColumn();
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    // Récupérer les derniers signalements
    $stmt = $pdo->query("
        SELECT r.*, u.name as user_name 
        FROM reports r 
        LEFT JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC 
        LIMIT 10
    ");
    $recentReports = $stmt->fetchAll();
} catch (PDOException $e) {
    $totalReports = 0;
    $pendingReports = 0;
    $inProgressReports = 0;
    $resolvedReports = 0;
    $totalUsers = 0;
    $recentReports = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Local-Protecte</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 250px;
            background: var(--dark-color);
            padding: 1rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .admin-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
            background: #f8f9fa;
        }
        .admin-brand {
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .admin-brand a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--white);
            font-size: 1.25rem;
            font-weight: 700;
        }
        .admin-brand i {
            color: var(--primary-color);
        }
        .admin-nav {
            list-style: none;
            padding: 0;
        }
        .admin-nav li {
            margin-bottom: 0.5rem;
        }
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255,255,255,0.7);
            border-radius: 8px;
            transition: var(--transition);
        }
        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(255,255,255,0.1);
            color: var(--white);
        }
        .admin-nav a.active {
            background: var(--primary-color);
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .admin-header h1 {
            margin: 0;
        }
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .admin-stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        .admin-stat-card h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        .admin-stat-card p {
            color: var(--gray-color);
            margin: 0;
        }
        .table-container {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--light-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-header h2 {
            margin: 0;
            font-size: 1.25rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--light-color);
        }
        th {
            background: var(--light-color);
            font-weight: 600;
        }
        tr:hover {
            background: #f8f9fa;
        }
        @media (max-width: 768px) {
            .admin-sidebar {
                display: none;
            }
            .admin-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <a href="../index.php">
                    <i class="fas fa-leaf"></i>
                    <span>Local-Protecte</span>
                </a>
            </div>
            <p style="color: rgba(255,255,255,0.5); padding: 0 1rem; margin-bottom: 1rem;">ADMINISTRATION</p>
            <ul class="admin-nav">
                <li>
                    <a href="index.php" class="active">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                </li>
                <li>
                    <a href="manage-reports.php">
                        <i class="fas fa-list"></i> Signalements
                    </a>
                </li>
                <li>
                    <a href="../dashboard.php">
                        <i class="fas fa-user"></i> Mon profil
                    </a>
                </li>
                <li>
                    <a href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1><i class="fas fa-cog"></i> Administration</h1>
                <a href="../index.php" class="btn btn-outline">
                    <i class="fas fa-home"></i> Voir le site
                </a>
            </div>
            
            <!-- Statistics -->
            <div class="admin-stats">
                <div class="admin-stat-card">
                    <h3><?= $totalReports ?></h3>
                    <p><i class="fas fa-list"></i> Total Signalements</p>
                </div>
                <div class="admin-stat-card" style="border-left: 4px solid #f39c12;">
                    <h3><?= $pendingReports ?></h3>
                    <p><i class="fas fa-clock"></i> En attente</p>
                </div>
                <div class="admin-stat-card" style="border-left: 4px solid #3498db;">
                    <h3><?= $inProgressReports ?></h3>
                    <p><i class="fas fa-spinner"></i> En cours</p>
                </div>
                <div class="admin-stat-card" style="border-left: 4px solid #27ae60;">
                    <h3><?= $resolvedReports ?></h3>
                    <p><i class="fas fa-check-circle"></i> Résolus</p>
                </div>
                <div class="admin-stat-card" style="border-left: 4px solid #9b59b6;">
                    <h3><?= $totalUsers ?></h3>
                    <p><i class="fas fa-users"></i> Utilisateurs</p>
                </div>
            </div>
            
            <!-- Recent Reports -->
            <div class="table-container">
                <div class="table-header">
                    <h2><i class="fas fa-clock"></i> Derniers signalements</h2>
                    <a href="manage-reports.php" class="btn btn-primary-small">
                        Voir tout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Utilisateur</th>
                            <th>Catégorie</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentReports)): ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 2rem;">Aucun signalement</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentReports as $report): ?>
                                <tr>
                                    <td>#<?= $report['id'] ?></td>
                                    <td><?= htmlspecialchars($report['title']) ?></td>
                                    <td><?= htmlspecialchars($report['user_name'] ?? 'Anonyme') ?></td>
                                    <td><?= htmlspecialchars(ucfirst($report['category'])) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $report['status'] ?>">
                                            <?= match($report['status']) {
                                                'pending' => 'En attente',
                                                'in_progress' => 'En cours',
                                                'resolved' => 'Résolu',
                                                default => 'En attente'
                                            } ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($report['created_at'])) ?></td>
                                    <td>
                                        <a href="../report-detail.php?id=<?= $report['id'] ?>" class="btn btn-small btn-outline">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>

