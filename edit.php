<?php
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin'){
   header('location:login_admin.php');
   exit();
}

$id = $_GET['id'];

// Récupérer le plat
$stmt = $pdo->prepare("SELECT * FROM plats WHERE id=?");
$stmt->execute([$id]);
$dish = $stmt->fetch(PDO::FETCH_ASSOC);

// UPDATE
if(isset($_POST['update'])){
   $name = $_POST['name'];
   $price = $_POST['price'];
   $type = $_POST['type'];

   // image
   if(!empty($_FILES['image']['name'])){
      $image = time().'_'.$_FILES['image']['name'];
      move_uploaded_file($_FILES['image']['tmp_name'], "uploaded_img/".$image);

      $update = $pdo->prepare("UPDATE plats SET name=?, price=?, type=?, image=? WHERE id=?");
      $update->execute([$name, $price, $type, $image, $id]);
   } else {
      $update = $pdo->prepare("UPDATE plats SET name=?, price=?, type=? WHERE id=?");
      $update->execute([$name, $price, $type, $id]);
   }

   header("location:admin_dashboard.php");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Plat</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/edit.css">
</head>

<body>

<div class="card">
     <a href="admin_dashboard.php">
   <i class="fas fa-sign-out-alt"></i>
</a>
<h2>Modifier le plat</h2>

<form method="POST" enctype="multipart/form-data">

<input type="text" name="name" value="<?= $dish['name'] ?>" required>

<input type="number" name="price" value="<?= $dish['price'] ?>" required>

<select name="type">
<option value="dish" <?= $dish['type']=='dish'?'selected':'' ?>>Plat du jour</option>
<option value="menu" <?= $dish['type']=='menu'?'selected':'' ?>>Menu</option>
</select>

<input type="file" name="image">

<button name="update">Modifier</button>

</form>
</div>

</body>
</html>