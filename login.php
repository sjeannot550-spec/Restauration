<?php

include 'config.php';


if(isset($_POST['submit'])){

   // Nettoyage des données entrantes
   $email = htmlspecialchars($_POST['email']);
   $pass = md5($_POST['password']); // Hachage MD5 pour correspondre à la base de données

   // Requête préparée pour vérifier l'utilisateur
   $select = $pdo->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
   $select->execute([$email, $pass]);
   $row = $select->fetch(PDO::FETCH_ASSOC);

   if($select->rowCount() > 0){

      // Création des variables de session
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['user_nom'] = $row['nom'];
      $_SESSION['user_type'] = $row['user_type'];

      // Redirection selon le rôle
      if($row['user_type'] == 'admin'){
         header('location:admin_dashboard.php');
      }else{
         header('location:index.php');
      }

   }else{
      $message[] = 'Email ou mot de passe incorrect !';
   }

}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Restoration</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="login-form-container active">

    <form action="" method="post">
        <h3>Se connecter</h3>

        <?php
        // Affichage des messages d'erreur
        if(isset($message)){
           foreach($message as $msg){
              echo '<div class="message" style="color:var(--green); background:rgba(39, 174, 96, 0.1); padding:1rem; margin-bottom:1rem; font-size:1.4rem; text-align:center; border-radius:.5rem;">'.$msg.'</div>';
           }
        }
        ?>

        <input type="email" name="email" placeholder="votre email" class="box" required>
        <input type="password" name="password" placeholder="votre mot de passe" class="box" required>
        
        <p>mot de passe oublié ? <a href="#">cliquez ici</a></p>
        <p>pas encore de compte ? <a href="register.php">créer un compte</a></p>
        
        <input type="submit" name="submit" value="connexion" class="btn">
        <a href="index.php" class="fas fa-times" id="close-login-page"></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </form>

</div>

</body>
</html>










