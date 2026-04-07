<?php
require_once 'config.php';

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){ 
    header('location:login.php'); 
    exit(); 
}

// Logique pour supprimer un seul article
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_cart_item = $pdo->prepare("DELETE FROM `cart` WHERE id = ? AND user_id = ?");
   $delete_cart_item->execute([$delete_id, $user_id]);
   header('location:cart.php');
   exit();
}

// Logique pour vider tout le panier
if(isset($_GET['delete_all'])){
   $delete_all = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
   $delete_all->execute([$user_id]);
   header('location:cart.php');
   exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier Gourmand</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        :root {
            --primary-color: #ff7e5f;
            --secondary-color: #feb47b;
            --dark-color: #2c3e50;
            --light-bg: #f8f9fa;
            --danger: #e74c3c;
            --shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .shopping-cart {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .heading {
            text-align: center;
            font-size: 2.5rem;
            color: var(--dark-color);
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Grille des produits */
        .cart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .cart-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
            position: relative;
        }

        .cart-card:hover {
            transform: translateY(-5px);
        }

        .cart-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            margin-right: 20px;
        }

        .cart-card .info h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: var(--dark-color);
        }

        .cart-card .price {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.1rem;
        }

        .cart-card .qty {
            background: #eee;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 10px;
            display: inline-block;
        }

        .delete-btn-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            color: #ccc;
            transition: color 0.3s;
            font-size: 1.2rem;
        }

        .delete-btn-icon:hover {
            color: var(--danger);
        }

        /* Section Total */
        .cart-summary {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .cart-summary h3 {
            font-size: 1.8rem;
            margin-bottom: 25px;
        }

        .cart-summary h3 span {
            color: var(--primary-color);
            font-weight: 800;
        }

        /* Style spécifique pour chaque type d'action */
.btn-custom {
    padding: 12px 25px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: none;
    cursor: pointer;
}

/* Couleur "Ajouter plus" - Bleu calme */
.btn-add { 
    background: #3498db; 
    color: white; 
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
}

/* Couleur "Facture" - Vert émeraude */
.btn-pdf { 
    background: #27ae60; 
    color: white; 
    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
}

/* Couleur "Home" - Indigo élégant */
.btn-home { 
    background: #5f27cd; 
    color: white; 
    box-shadow: 0 4px 15px rgba(95, 39, 205, 0.3);
}

/* Couleur "Vider Panier" - Rouge corail (comme la suppression) */
.btn-empty { 
    background: #ff6b6b; 
    color: white; 
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
}

.btn-custom:hover {
    transform: translateY(-3px);
    filter: brightness(1.1);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

        /* Message vide */
        .empty-msg {
            text-align: center;
            padding: 50px;
            font-size: 1.2rem;
            color: #666;
        }
    </style>
</head>
<body>

<section class="shopping-cart">
    <h1 class="heading"><i class="fas fa-shopping-basket"></i> Votre Panier</h1>

    <div class="cart-grid">
        <?php
        $grand_total = 0;
        $select_cart = $pdo->prepare("SELECT * FROM `cart` WHERE user_id = ?");
        $select_cart->execute([$user_id]);
        
        if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
                $grand_total += $sub_total;
        ?>
        <div class="cart-card">
            <a href="cart.php?delete=<?= $fetch_cart['id']; ?>" 
               class="fas fa-times delete-btn-icon" 
               onclick="return confirm('Retirer cet article ?');"></a>
            
            <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
            
            <div class="info">
                <h3><?= $fetch_cart['name']; ?></h3>
                <div class="price"><?= $fetch_cart['price']; ?>$</div>
                <div class="qty">Quantité: <b><?= $fetch_cart['quantity']; ?></b></div>
                <div style="margin-top:5px; font-size: 0.8rem; color: #888;">
                    Total: <?= $sub_total; ?>$
                </div>
            </div>
        </div>
        <?php 
            }
        } else {
            echo '<p class="empty-msg">Votre panier est actuellement vide.</p>';
        }
        ?>
    </div>

    <?php if($grand_total > 0){ ?>
    <div class="cart-summary">
        <h3>Total Général : <span><?= $grand_total; ?>$</span></h3>

        <div class="button-group">
    <a href="index.php#menu" class="btn-custom btn-add">
        <i class="fas fa-plus-circle"></i> Continuer mes achats
    </a>

    <a href="facture.php" class="btn-custom btn-pdf">
        <i class="fas fa-file-invoice-dollar"></i> Télécharger ma facture
    </a>

    <a href="index.php" class="btn-custom btn-home">
        <i class="fas fa-home"></i> Retour à l'accueil
    </a>

    <a href="cart.php?delete_all" class="btn-custom btn-empty"
       onclick="return confirm('⚠️ Attention : Voulez-vous vraiment vider TOUT votre panier ?');">
        <i class="fas fa-trash-alt"></i> Vider tout le panier
    </a>
</div>
    <?php } ?>
</section>

<?php include 'chat_section.php'; ?>

</body>
</html>