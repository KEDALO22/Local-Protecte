<?php
// admin/manage-reports.php - Gestion des signalements (Admin)
require_once '../config/db.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    redirect('../index.php');
}

$error = '';
$success = '';

// Traitement de la mise à jour du statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $report_id = $_POST['report_id'] ?? 0;
    $new_status = $_POST['status'] ?? '';
    
    if ($report_id && in_array($new_status, ['pending', 'in_progress', 'resolved'])) {
        try {
            $stmt = $pdo->prepare("UPDATE reports SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_status, $report_id]);
            $success = 'Statut mis à jour avec succès!';
        } catch (PDOException $e) {
            $error = 'Erreur lors de la mise à jour du statut.';
        }
    }
}

// Traitement de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_report'])) {
    $report_id = $_POST['report_id'] ?? 0;
    
    if ($report_id) {
        try {
            // Supprimer l'image si elle existe
            $stmt = $pdo->prepare("SELECT image FROM reports WHERE id = ?");
            $stmt->execute([$report_id]);
            $report = $stmt->fetch();
            
            if ($report && $report['image'] && file_exists(__DIR__ . '/../uploads/' . $report['image'])) {
                unlink(__DIR__ . '/../uploads/' . $report['image']);
            }
            
            // Supprimer le signalement
            $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
            $stmt->execute([$report_id]);
            $success = 'Signalement supprimé avec succès!';
        } catch (PDOException $e) {
            $error = 'Erreur lors de la suppression du signalement.';
        }
    }
}

// Filtres
$status_filter = $_GET['status'] ?? 'all';
$category_filter = $_GET['category'] ?? 'all';
$search = $_GET['search'] ?? '';

// Requête avec filtres
$where = [];
$params = [];

if ($status_filter !== 'all') {
    $where[] = 'r.status = ?';
    $params[] = $status_filter;
}

if ($category_filter !== 'all') {
    $where[] = 'r.category = ?';
    $params[] = $category_filter;
}

if ($search) {
    $where[] = '(r.title LIKE ? OR r.description LIKE ? OR r.location LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.name as user_name, u.email as user_email 
        FROM reports r 
        LEFT JOIN users u ON r.user_id = u.id 
        $whereClause
        ORDER BY r.created_at DESC
    ");
    $stmt->execute($params);
    $reports = $stmt->fetchAll();
} catch (PDOException $e) {
    $reports = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des signalements - Local-Protecte Admin</title>
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
            flex-wrap: wrap;
            gap: 1rem;
        }
        .admin-header h1 {
            margin: 0;
        }
        .filters-bar {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
            background: var(--white);
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        .filters-bar input,
        .filters-bar select {
            padding: 0.5rem 1rem;
            border: 1px solid var(--light-color);
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .filters-bar input {
            flex: 1;
            min-width: 200px;
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
            vertical-align: middle;
        }
        th {
            background: var(--light-color);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .report-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .action-btns {
            display: flex;
            gap: 0.5rem;
        }
        .status-form {
            display: flex;
            gap: 0.5rem;
        }
        .status-form select {
            padding: 0.25rem 0.5rem;
            border: 1px solid var(--light-color);
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .status-form button {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        .btn-delete {
            background: var(--danger-color);
            color: var(--white);
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        @media (max-width: 1200px) {
            .table-container {
                overflow-x: auto;
            }
            table {
                min-width: 1000px;
            }
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
                    <a href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                </li>
                <li>
                    <a href="manage-reports.php" class="active">
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
                <h1><i class="fas fa-list"></i> Gestion des signalements</h1>
                <a href="index.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error" style="margin-bottom: 1rem;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" style="margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <!-- Filters -->
            <form method="GET" class="filters-bar">
                <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($search) ?>">
                <select name="status">
                    <option value="all">Tous les statuts</option>
                    <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>En attente</option>
                    <option value="in_progress" <?= $status_filter === 'in_progress' ? 'selected' : '' ?>>En cours</option>
                    <option value="resolved" <?= $status_filter === 'resolved' ? 'selected' : '' ?>>Résolu</option>
                </select>
                <select name="category">
                    <option value="all">Toutes les catégories</option>
                    <option value="environnement" <?= $category_filter === 'environnement' ? 'selected' : '' ?>>Environnement</option>
                    <option value="voirie" <?= $category_filter === 'voirie' ? 'selected' : '' ?>>Voirie</option>
                    <option value="eclairage" <?= $category_filter === 'eclairage' ? 'selected' : '' ?>>Éclairage</option>
                    <option value="espaces_verts" <?= $category_filter === 'espaces_verts' ? 'selected' : '' ?>>Espaces verts</option>
                    <option value="dechets" <?= $category_filter === 'dechets' ? 'selected' : '' ?>>Déchets</option>
                    <option value="pollution" <?= $category_filter === 'pollution' ? 'selected' : '' ?>>Pollution</option>
                    <option value="bruit" <?= $category_filter === 'bruit' ? 'selected' : '' ?>>Nuisances sonores</option>
                    <option value="autres" <?= $category_filter === 'autres' ? 'selected' : '' ?>>Autres</option>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrer
                </button>
            </form>
            
            <!-- Reports Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2><i class="fas fa-list"></i> Signalements (<?= count($reports) ?>)</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Titre</th>
                            <th>Utilisateur</th>
                            <th>Catégorie</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 2rem;">Aucun signalement trouvé</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td>
                                        <?php if ($report['image']): ?>
                                            <img src="../uploads/<?= htmlspecialchars($report['image']) ?>" alt="" class="report-thumb">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 60px; background: var(--light-color); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image" style="color: var(--gray-color);"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($report['title']) ?></strong><br>
                                        <small style="color: var(--gray-color);"><?= htmlspecialchars(substr($report['description'], 0, 50)) ?>...</small>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($report['user_name'] ?? 'Anonyme') ?><br>
                                        <small style="color: var(--gray-color);"><?= htmlspecialchars($report['user_email'] ?? '') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars(ucfirst($report['category'])) ?></td>
                                    <td>
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                            <select name="status">
                                                <option value="pending" <?= $report['status'] === 'pending' ? 'selected' : '' ?>>En attente</option>
                                                <option value="in_progress" <?= $report['status'] === 'in_progress' ? 'selected' : '' ?>>En cours</option>
                                                <option value="resolved" <?= $report['status'] === 'resolved' ? 'selected' : '' ?>>Résolu</option>
                                            </select>
                                            <button type="submit" name="update_status" value="1" class="btn btn-small btn-primary">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($report['created_at'])) ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="../report-detail.php?id=<?= $report['id'] ?>" class="btn btn-small btn-outline" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce signalement?');">
                                                <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                                <button type="submit" name="delete_report" value="1" class="btn-delete" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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

