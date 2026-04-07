<?php

include 'config.php';

if(isset($_POST['submit'])){

   // Sécurisation des données saisies par l'utilisateur
   $nom = htmlspecialchars($_POST['nom']);
   $post_nom = htmlspecialchars($_POST['post_nom']);
   $prenom = htmlspecialchars($_POST['prenom']);
   $sexe = $_POST['sexe'];
   $number = htmlspecialchars($_POST['number']);
   $adresse = htmlspecialchars($_POST['adresse']);
   $email = htmlspecialchars($_POST['email']);
  
   // Hachage des mots de passe en MD5 (pour correspondre au login.php)
   $pass = md5($_POST['password']);
   $cpass = md5($_POST['c_password']);

   // Gestion du téléchargement de la photo de profil
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   // Vérification si l'email existe déjà dans la base de données
  $select = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$select->execute([$email]);
   if($select->rowCount() > 0){
      $message[] = 'Cet email est déjà enregistré !';
   }else{
      if($pass != $cpass){
         $message[] = 'Les mots de passe ne correspondent pas !';
      }elseif($image_size > 2000000){
         $message[] = 'L\'image est trop volumineuse (max 2Mo) !';
      }else{
         // Insertion du nouvel utilisateur dans la table 'users'
        $insert = $pdo->prepare("INSERT INTO users(nom, post_nom, prenom, sexe, telephone, adresse, email, password, image) VALUES(?,?,?,?,?,?,?,?,?)");

$insert->execute([
   $nom,
   $post_nom,
   $prenom,
   $sexe,
   $number,
   $adresse,
   $email,
   $pass,
   $image
]);

         if($insert){
            // Déplacement du fichier photo vers le dossier local
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Inscription réussie !';
            header('location:login.php');
         }
      }
   }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Restoration</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>

<div class="login-form-container active">

    <form action="" method="post" enctype="multipart/form-data">
        <h3>Créer un compte</h3>

        <?php
        // Affichage des messages d'erreur ou de succès
        if(isset($message)){
           foreach($message as $msg){
              echo '<div class="message" style="color:var(--green); background:rgba(39, 174, 96, 0.1); padding:1rem; margin-bottom:1rem; font-size:1.4rem; text-align:center; border-radius:.5rem;">'.$msg.'</div>';
           }
        }
        ?>
       
        
       
        <input type="text" name="nom" placeholder="votre nom" class="box" required>
        <input type="text" name="post_nom" placeholder="votre post-nom" class="box" required>
        <input type="text" name="prenom" placeholder="votre prénom" class="box" required>
       
        <select name="sexe" class="box" required>
            <option value="" disabled selected>sélectionnez votre sexe</option>
            <option value="M">Masculin</option>
            <option value="F">Féminin</option>
        </select>
        <p style="font-size: 1.4rem; color: var(--light-color); margin-bottom: .5rem;">Photo de profil :</p>
        <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png" required>
        <input type="number" name="number" placeholder="numéro de téléphone" class="box" required>
        <input type="text" name="adresse" placeholder="votre adresse" class="box" required>
        <input type="email" name="email" placeholder="votre email" class="box" required>
        <input type="password" name="password" placeholder="votre mot de passe" class="box" required>
        <input type="password" name="c_password" placeholder="confirmer le mot de passe" class="box" required>
       
        <p>avez-vous déjà un compte ? <a href="login.php">se connecter</a></p>
        <input type="submit" name="submit" value="s'inscrire" class="btn">
        <a href="index.php" class="fas fa-times" id="close-login-page"></a>
    </form>

</div>

</body>
</html>