<?php
include 'config.php';

if(isset($_POST['submit'])){

   $email = htmlspecialchars($_POST['email']);
   $pass = $_POST['password']; // Pas de MD5 ici pour tester avec le mot de passe "admin" en clair

   // Vérification statique des identifiants admin
   if($email == 'admin@gmail.com' && $pass == 'admin'){
     
      // On crée une session factice pour l'admin
      $_SESSION['user_id'] = '1';
      $_SESSION['user_nom'] = 'Administrateur';
      $_SESSION['user_type'] = 'admin';

      header('location:admin_dashboard.php');
      exit();
   }else{
      $message[] = 'Identifiants administrateur incorrects !';
   }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin - Restoration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="login-form-container active">

    <form action="" method="post">
        <h3>Connexion Admin</h3>

        <?php
        if(isset($message)){
           foreach($message as $msg){
              echo '<div class="message" style="color:var(--green); background:rgba(39, 174, 96, 0.1); padding:1rem; margin-bottom:1rem; font-size:1.4rem; text-align:center; border-radius:.5rem;">'.$msg.'</div>';
           }
        }
        ?>

        <input type="email" name="email" placeholder="admin@gmail.com" class="box" required>
        <input type="password" name="password" placeholder="admin" class="box" required>
       
        <input type="submit" name="submit" value="Se connecter" class="btn">
       
        <p style="margin-top: 2rem; font-size: 1.3rem; color: var(--light-color);">
            <i class="fas fa-info-circle"></i> Accès restreint au personnel autorisé.
        </p>
        <a href="index.php" class="fas fa-times" id="close-login-page"></a>
    </form>

</div>

</body>
</html>