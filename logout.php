<?php

include 'config.php';

// 1. Démarrer la session pour pouvoir y accéder
session_start();

// 2. Libérer toutes les variables de session
session_unset();

// 3. Détruire la session elle-même sur le serveur
session_destroy();

// 4. Rediriger l'administrateur vers la page de connexion
header('location:login.php');

// 5. Arrêter l'exécution du script
exit();

?>