<?php
// login.php - Connexion des utilisateurs
require_once 'config/db.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        $error = 'Veuillez entrer votre email et mot de passe.';
    } else {
        try {
            // Vérifier les identifiants
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Créer la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Rediriger vers le dashboard
                redirect('dashboard.php');
            } else {
                $error = 'Email ou mot de passe incorrect.';
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
        <i class="fas fa-sign-in-alt"></i>
        <h1>Connexion</h1>
        <p>Connectez-vous pour accéder à votre tableau de bord</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
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
                   placeholder="Entrez votre mot de passe">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-sign-in-alt"></i> Se connecter
        </button>
    </form>
    
    <div class="auth-footer">
        <p>Pas encore de compte? <a href="register.php">S'inscrire</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

