<?php
session_start();

// 🔥 Supprimer toutes les variables de session
$_SESSION = [];

// 🔥 Détruire la session
session_destroy();

// 🔥 Redirection vers login admin
header("Location: login_admin.php");
exit();