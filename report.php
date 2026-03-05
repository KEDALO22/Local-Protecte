<?php
// report.php - Soumettre un nouveau signalement
require_once 'config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';

// Catégories disponibles
$categories = [
    'environnement' => 'Environnement',
    'voirie' => 'Voirie',
    'eclairage' => 'Éclairage',
    'espaces_verts' => 'Espaces verts',
    'dechets' => 'Déchets',
    'pollution' => 'Pollution',
    'bruit' => 'Nuisances sonores',
    'autres' => 'Autres'
];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? '';
    $location = trim($_POST['location'] ?? '');
    
    // Validation
    if (empty($title) || empty($description) || empty($category)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        try {
            // Gérer l'upload de l'image
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $file_type = $_FILES['image']['type'];
                
                if (in_array($file_type, $allowed_types)) {
                    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('report_') . '.' . $extension;
                    $upload_dir = __DIR__ . '/uploads/';
                    
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                        $image = $filename;
                    }
                }
            }
            
            // Insérer le signalement
            $stmt = $pdo->prepare("
                INSERT INTO reports (user_id, title, description, category, location, image, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $title,
                $description,
                $category,
                $location,
                $image
            ]);
            
            $success = 'Votre signalement a été soumis avec succès!';
            
            // Rediriger vers le dashboard après 2 secondes
            header("refresh:2;url=dashboard.php");
        } catch (PDOException $e) {
            $error = 'Une erreur est survenue. Veuillez réessayer.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <div class="form-container" style="max-width: 700px;">
        <div class="auth-header">
            <i class="fas fa-exclamation-circle"></i>
            <h1>Signaler un problème</h1>
            <p>Décrivez le problème que vous avez constaté dans votre quartier</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading"></i> Titre du problème *
                </label>
                <input type="text" id="title" name="title" required 
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       placeholder="Ex: Lampadaire cassé, Tas d'ordures, etc.">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category">
                        <i class="fas fa-folder"></i> Catégorie *
                    </label>
                    <select id="category" name="category" required>
                        <option value="">Sélectionner une catégorie</option>
                        <?php foreach ($categories as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($_POST['category'] ?? '') === $key ? 'selected' : '' ?>>
                                <?= htmlspecialchars($value) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="location">
                        <i class="fas fa-map-marker-alt"></i> Localisation
                    </label>
                    <input type="text" id="location" name="location" 
                           value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"
                           placeholder="Ex: Rue de la Paix, Paris">
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i> Description détaillée *
                </label>
                <textarea id="description" name="description" required 
                          placeholder="Décrivez le problème en détail: date de constatation, gravité, observations..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">
                    <i class="fas fa-camera"></i> Photo du problème
                </label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <p class="help-text">Formats acceptés: JPEG, PNG, GIF, WebP. Taille max: 5Mo</p>
            </div>
            
            <div class="form-actions">
                <a href="dashboard.php" class="btn btn-outline">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Soumettre le signalement
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

