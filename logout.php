<?php
// logout.php - Déconnexion
require_once 'config/db.php';

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
redirect('index.php');

