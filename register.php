<?php
// register.php - Inscription des utilisateurs
require_once 'config/db.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Veuillez entrer une adresse email valide.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        try {
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Cette adresse email est déjà utilisée.';
            } else {
                // Hasher le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insérer l'utilisateur
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
                $stmt->execute([$name, $email, $hashed_password]);
                
                $success = 'Inscription réussie! Vous pouvez maintenant vous connecter.';
            }
        } catch (PDOException $e) {
            $error = 'Une erreur est survenue. Veuillez réessayer.';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-header">
        <i class="fas fa-user-plus"></i>
        <h1>Créer un compte</h1>
        <p>Rejoignez-nous pour améliorer votre quartier</p>
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
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">
                <i class="fas fa-user"></i> Nom complet
            </label>
            <input type="text" id="name" name="name" required 
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                   placeholder="Entrez votre nom complet">
        </div>
        
        <div class="form-group">
            <label for="email">
                <i class="fas fa-envelope"></i> Adresse email
            </label>
            <input type="email" id="email" name="email" required 
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="Entrez votre adresse email">
        </div>
        
        <div class="form-group">
            <label for="password">
                <i class="fas fa-lock"></i> Mot de passe
            </label>
            <input type="password" id="password" name="password" required 
                   placeholder="Minimum 6 caractères">
        </div>
        
        <div class="form-group">
            <label for="confirm_password">
                <i class="fas fa-lock"></i> Confirmer le mot de passe
            </label>
            <input type="password" id="confirm_password" name="confirm_password" required 
                   placeholder="Répétez votre mot de passe">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-user-plus"></i> S'inscrire
        </button>
    </form>
    
    <div class="auth-footer">
        <p>Déjà inscrit? <a href="login.php">Se connecter</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

