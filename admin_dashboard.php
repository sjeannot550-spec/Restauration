<?php
include 'config.php';

// Vérification de la session
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin'){
   header('location:login_admin.php');
   exit();
}

// AJOUT D'UN PLAT
if(isset($_POST['add_dish'])){
   $name = $_POST['name'];
   $price = $_POST['price'];
   $type = $_POST['type']; 
   $description = $_POST['description']; // Nouvelle variable

   $image = time().'_'.$_FILES['image']['name'];
   $tmp = $_FILES['image']['tmp_name'];

   // Requête préparée incluant la description
   $pdo->prepare("INSERT INTO plats (nom, prix, image, type, description) VALUES (?, ?, ?, ?, ?)")
       ->execute([$name, $price, $image, $type, $description]);

   move_uploaded_file($tmp, "uploaded_img/".$image);
   header("location:admin_dashboard.php"); // Redirection pour éviter le renvoi du formulaire
}

// SUPPRESSION
if(isset($_GET['delete'])){
   $pdo->prepare("DELETE FROM plats WHERE id=?")->execute([$_GET['delete']]);
   header("location:admin_dashboard.php");
}

// RÉCUPÉRATION DES DONNÉES POUR LES STATS
$users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$dishes = $pdo->query("SELECT COUNT(*) FROM plats")->fetchColumn();
$messages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

$list = $pdo->query("SELECT * FROM plats ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Gestion Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/admin.css">
    <style>
        /* Petit ajout de style pour la description dans le tableau */
        .desc-text {
            font-size: 0.85em;
            color: #666;
            max-width: 200px;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#stats">Statistiques</a>
        <a href="#add">Ajouter</a>
        <a href="#table">Plats</a>
        <a href="#messages">Messages</a>
        <a href="logout.php">Déconnexion</a>
    </div>

    <div class="main">
        <h1>Dashboard</h1>

        <div class="stats" id="stats">
            <div class="card">👤 Users <br><strong><?= $users ?></strong></div>
            <div class="card">🍽 Plats <br><strong><?= $dishes ?></strong></div>
            <div class="card">💬 Messages <br><strong><?= $messages ?></strong></div>
        </div>

        <div class="card" id="add">
            <h2>Ajouter un plat</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Nom du plat" required>
                <input type="number" name="price" placeholder="Prix (ex: 15)" required>
                
                <select name="type" required>
                    <option value="dish">Plat du jour</option>
                    <option value="menu">Menu Complet</option>
                </select>

                <textarea name="description" rows="4" placeholder="Description et conseils pour le client..." required></textarea>

                <input type="file" name="image" required>
                <button type="submit" name="add_dish">Ajouter au menu</button>
            </form>
        </div>

        <div class="card" id="table">
            <h2>Liste des plats</h2>
            <table>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Description</th> <th>Prix</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>

                <?php while($d = $list->fetch(PDO::FETCH_ASSOC)){ ?>
                <tr>
                    <td><img src="uploaded_img/<?= $d['image'] ?>" width="50"></td>
                    <td><strong><?= $d['nom'] ?></strong></td>
                    <td><span class="desc-text" title="<?= $d['description'] ?>"><?= $d['description'] ?></span></td>
                    <td><?= $d['prix'] ?>$</td>
                    <td><?= $d['type'] ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $d['id'] ?>"><i class="fas fa-edit"></i></a>
                        <a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Supprimer ce plat ?')">
                            <i class="fas fa-trash" style="color:red;"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <div class="card" id="messages">
            <h2>📋 Commandes Spéciales par Client</h2>
            <?php
            $select_clients = $pdo->query("SELECT DISTINCT user_id FROM messages");
            while($client = $select_clients->fetch(PDO::FETCH_ASSOC)){
                $id_c = $client['user_id'];
                $info_user = $pdo->prepare("SELECT nom, email FROM users WHERE id = ?");
                $info_user->execute([$id_c]);
                $u = $info_user->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="client-order-group" style="border: 1px solid #ddd; margin-bottom: 20px; border-radius: 8px;">
                <div class="client-header" style="background: #f4f4f4; padding: 10px; border-bottom: 1px solid #ddd;">
                    <strong>👤 Client : <?= $u['nom'] ?? 'Inconnu' ?></strong><br>
                    <small>Contact : <?= $u['email'] ?? 'N/A' ?></small>
                </div>
                <div class="client-messages" style="padding: 10px;">
                    <?php
                    $user_msgs = $pdo->prepare("SELECT * FROM messages WHERE user_id = ? ORDER BY id DESC");
                    $user_msgs->execute([$id_c]);
                    while($m = $user_msgs->fetch(PDO::FETCH_ASSOC)){
                    ?>
                        <div class="single-msg" style="padding: 5px 0; border-bottom: 1px dashed #eee;">
                            <span style="color: #666; font-size: 0.8em;"><?= $m['created_at'] ?> :</span>
                            <p style="margin: 5px 0;">🍴 <?= $m['message'] ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>

    </div>
</div>

</body>
</html>