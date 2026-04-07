<?php
// 🔐 SESSION
session_start();

// ⚙️ CONFIG DB
$host = 'localhost';
$dbname = 'restoration_db'; // ⚠️ change si besoin
$user = 'root';
$password = '';

// 🔌 CONNEXION PDO
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur connexion DB");
}

/* =========================
   🔒 SÉCURITÉ (helper)
========================= */

// Nettoyage XSS
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Vérifier si admin connecté (optionnel)
function isAdmin() {
    return isset($_SESSION['admin']);
}

// Protection page admin (optionnel)
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: login.php");
        exit;
    }
}