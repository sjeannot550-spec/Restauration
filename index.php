<?php
require_once 'config.php';

// ====== USER ======
$user = null;

$user_id = $_SESSION['user_id'] ?? 0;

if($user_id){
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ❌ on évite de bloquer ici (sinon add_to_cart ne marche pas)
if(isset($_GET['page']) && $_GET['page'] === 'cart'){
    if(!$user_id){
        header('location:login.php');
        exit();
    }
}

// ====== COMPTEUR PANIER ======
$cart_count = 0;

if($user_id){
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = $stmt->fetchColumn() ?? 0;
}

// ====== ADD TO CART ======
if(isset($_POST['add_to_cart'])){

    $pid   = $_POST['pid'] ?? 0;
    $name  = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $image = $_POST['image'] ?? '';
    $qty   = $_POST['qty'] ?? 1;

    if($user_id && $name){

        $check_cart = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND name = ?");
        $check_cart->execute([$user_id, $name]);

        if($check_cart->rowCount() > 0){
            $update = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND name = ?");
            $update->execute([$qty, $user_id, $name]);
        } else {
            $insert = $pdo->prepare("INSERT INTO cart (user_id, name, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$user_id, $name, $price, $qty, $image]);
        }
    }

    header('location:index.php');
    exit();
}

// ====== ADD REVIEW ======
if(isset($_POST['send_review'])){

    // On utilise les infos de la session $user si elle existe
    $nom = $user ? $user['nom'] : htmlspecialchars($_POST['nom']);
    $image_user = ($user && !empty($user['image'])) ? $user['image'] : 'default-user.png';
    $message = htmlspecialchars($_POST['message']);
    $note = $_POST['note'] ?? 5;

    if($nom && $message){
        // C'est ici que l'erreur se produisait car 'image' et 'note' manquaient en DB
        $insert = $pdo->prepare("INSERT INTO avis (nom, message, image, note) VALUES (?, ?, ?, ?)");
        $insert->execute([$nom, $message, $image_user, $note]);
    }

    header('location: index.php#Review');
    exit();
}

// ====== DATA ======
$dishes = $pdo->query("SELECT * FROM plats ORDER BY id DESC");
$menus  = $pdo->query("SELECT * FROM plats ORDER BY id DESC");
$reviews = $pdo->query("SELECT * FROM avis ORDER BY id DESC LIMIT 4");


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Responsive Fonnd Website Desing Tutorial</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>
<body>

    <header>
        <a href="#" class="logo"><i class="fas fa-utensils"></i>Resto.</a>

        <nav class="navbar">
            <a class="active" href="#Home">Home</a>
            <a href="#Dishes">Dishes</a>
            <a href="#About">About</a>
            <a href="#Menu">Menu</a>
            <a href="#Review">Review</a>
            <a href="admin_dashboard.php">Admin</a>
        </nav>

       <div class="icons">
   <i class="fas fa-bars" id="menu-bars"></i>
   <i class="fas fa-search" id="search-icons"></i>
   <a href="cart.php" class="cart-icon">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-count"><?= $cart_count ?></span>
</a>

 <?php if($user): ?>
      
   <div class="user-box">
      <img src="uploaded_img/<?= !empty($user['image']) ? htmlspecialchars($user['image']) : 'default-user.png'; ?>" alt="profil">
      
      <span><?= htmlspecialchars($user['nom'] ?? 'Utilisateur'); ?></span>

      <!-- 🔴 Icône déconnexion -->
      <a href="logout.php" class="logout-icon fas fa-sign-out-alt" title="Déconnexion"
         onclick="return confirm('Se déconnecter ?');"></a>
   </div>

<?php else: ?>

   <a href="login.php" class="fas fa-user"></a>

<?php endif; ?>

</div>
    </header>

    <form action="" id="search-form">
        <input type="search" placeholder="search here..." name="" id="search-box">
        <label for="search-box" class="fas fa-search"></label>
        <i class="fas fa-times" id="close"></i>
        </form>

    <section class="home" id="Home">
        <div class="swiper home-slider">
            <div class="swiper-wrapper wrapper">
                <div class="swiper-slide slide">
                    <div class="content">
                        <span>our special dish</span>
                        <h3>Poulet braisé en sauce aux champignons</h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nihil vitae ratione eligendi.</p>
                        <a href="#" class="btn">order now</a>
                    </div>
                    <div class="image">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ6_2yY5Kf7ViumPRG5p43t0t1KNJxuLAqDcQ&s" alt="">
                    </div>
                </div>

                <div class="swiper-slide slide">
                    <div class="content">
                        <span>our special dish</span>
                        <h3>Coca-Cola frais</h3>
                        <p>Profitez d’un Coca-Cola bien frais, une boisson gazeuse rafraîchissante au goût unique.</p>
                        <a href="#" class="btn">order now</a>
                    </div>
                    <div class="image">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqMliNPhBe2vv8REk0KV6QM9rNYpetIDMJqA&s" alt="">
                    </div>
                </div>

                <div class="swiper-slide slide">
                    <div class="content">
                        <span>our special dish</span>
                        <h3>Poulet sauté au brocoli sauce teriyaki</h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nihil vitae ratione eligendi.</p>
                        <a href="#" class="btn">order now</a>
                    </div>
                    <div class="image">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRSliR2dbVtxz6o34C7eApwqxjj4FW7g9Arcw&s" alt="">
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

        <section class="dishes" id="Dishes">
    <h3 class="sub-heading"> our dishes </h3>
    <h1 class="heading"> popular dishes </h1>

    <div class="box-container">
        <?php while($row = $dishes->fetch()) { ?>
        
            <div class="box">
                <img src="uploaded_img/<?= $row['image'] ?>" alt="">
                <h3><?= $row['nom'] ?></h3>
                <span><?= $row['prix'] ?> $</span>
                <form action="" method="POST">
    <input type="hidden" name="pid" value="<?= $row['id']; ?>">
    <input type="hidden" name="name" value="<?= $row['nom']; ?>">
    <input type="hidden" name="price" value="<?= $row['prix']; ?>">
    <input type="hidden" name="image" value="<?= $row['image']; ?>">
    
    <input type="number" name="qty" value="1" min="1" class="qty">
    
    <input type="submit" name="add_to_cart" value="Ajouter au panier" class="btn">
</form>
            </div>
        <?php } ?>
    </div>
</section>


    <section class="about" id="about">
        <h3 class="sub-heading"> about us </h3>
        <h1 class="heading"> why choose us? </h1>

        <div class="row">
            <div class="image">
                <img src="https://png.pngtree.com/png-vector/20241219/ourmid/pngtree-yummy-stretchy-cheese-pepperoni-pizza-png-image_14798826.png" alt="">
            </div>
            <div class="content">
                <h3>best food in the country</h3>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit...</p>
                <div class="icons-container">
                    <div class="icons"><i class="fas fa-shipping-fast"></i><span>free delivery</span></div>
                    <div class="icons"><i class="fas fa-dollar-sign"></i><span>easy payments</span></div>
                    <div class="icons"><i class="fas fa-headset"></i><span>24/7 service</span></div>
                </div>
                <a href="#" class="btn">learn more</a>
            </div>
        </div>
    </section>

    <section class="menu" id="Menu">
    <h3 class="sub-heading"> our menu </h3>
    <h1 class="heading"> today's speciality </h1>

    <div class="box-container">
        <?php while($row = $menus->fetch()) { ?>
            <div class="box">
                <img src="uploaded_img/<?= $row['image'] ?>" alt="">
                <h3><?= $row['nom'] ?></h3>
                <p><?= $row['description'] ?></p>
                <span><?= $row['prix'] ?> $</span>
                
            </div>
        <?php } ?>
    </div>
</section>

<section class="Review" id="Review">
    <h3 class="sub-heading"> Témoignages </h3>
    <h1 class="heading"> Ce qu'ils disent de nous </h1>

    <div class="swiper review-slider">
        <div class="swiper-wrapper">
            <?php while($review = $reviews->fetch()) { ?>
                <div class="swiper-slide slide" style="background:#fff; padding:2rem; border-radius:.5rem; box-shadow:0 .5rem 1.5rem rgba(0,0,0,.1);">
                    <i class="fas fa-quote-right" style="font-size: 4rem; color:#ccc; position: absolute; top:2rem; right:2rem;"></i>
                    <div class="user" style="display: flex; align-items: center; gap:1.5rem; margin-bottom:1.5rem;">
                        <img src="uploaded_img/<?= !empty($review['image']) ? $review['image'] : 'default-user.png' ?>" 
                            alt="" style="height:7rem; width:7rem; border-radius:50%; object-fit: cover;">
                        <div class="user-info">
                            <h3 style="font-size: 2rem; color:var(--black);"><?= htmlspecialchars($review['nom']) ?></h3>
                            <div class="stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star" style="color:<?= $i <= ($review['note'] ?? 5) ? 'gold' : '#ccc' ?>; font-size:1.5rem;"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <p style="font-size: 1.5rem; color:var(--light-color); line-height: 1.8;"><?= htmlspecialchars($review['message']) ?></p>
                </div>
            <?php } ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <div class="review-form-container" style="margin-top: 5rem;">
        <h3 class="sub-heading"> votre avis compte </h3>
        <h1 class="heading"> laissez un commentaire </h1>

        <?php if($user): ?>
            <form action="" method="POST" style="max-width:70rem; margin:0 auto; background:#fff; padding:2rem; border-radius:.5rem; box-shadow:0 .5rem 1.5rem rgba(0,0,0,.1);">
                <div class="inputBox" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 1.5rem;">
                    
                    <div class="input" style="width:100%; text-align: center; margin-bottom: 2rem;">
                        <img src="uploaded_img/<?= !empty($user['image']) ? $user['image'] : 'default-user.png'; ?>" 
                            style="width:80px; height:80px; border-radius:50%; border:3px solid var(--green); object-fit:cover;">
                        <p style="font-size: 1.6rem; margin-top: 1rem;">En tant que : <strong><?= htmlspecialchars($user['nom']) ?></strong></p>
                    </div>

                    <div class="input" style="width:100%;">
                        <span style="font-size:1.7rem; color:var(--light-color);">Note (1 à 5)</span>
                        <input type="number" name="note" min="1" max="5" value="5" style="width:100%; background:#eee; padding:1rem; margin:1rem 0; font-size:1.6rem; border-radius:.5rem;">
                    </div>
                </div>

                <span style="font-size:1.7rem; color:var(--light-color);">Votre Message</span>
                <textarea name="message" placeholder="Partagez votre expérience..." cols="30" rows="5" required
                        style="width:100%; background:#eee; padding:1rem; margin:1rem 0; font-size:1.6rem; resize:none; border-radius:.5rem;"></textarea>
                
                <input type="submit" name="send_review" value="Publier l'avis" class="btn" style="width: 100%;">
            </form>
        <?php else: ?>
            <div style="text-align: center; padding: 2rem;">
                <p style="font-size: 2rem;">Veuillez vous <a href="login.php" style="color:var(--green);">connecter</a> pour laisser un avis.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

    <section class="footer" id="About">
        <div class="box-container">
            <div class="box">
                <h3>locations</h3>
                <a href="#">India</a><a href="#">Japon</a><a href="#">RDC</a><a href="#">USA</a><a href="#">France</a>
            </div>
            <div class="box">
                <h3>quick links</h3>
                <a href="#Home">Home</a><a href="#Dishes">Dishes</a><a href="#About">About</a><a href="#Menu">Menu</a><a href="#Order">Order</a>
            </div>
            <div class="box">
                <h3>contact info</h3>
                <a href="#">+243-829-134-460</a><a href="#">sjeannot550@gmail.com</a>
            </div>
            <div class="box">
                <h3>follow us</h3>
                <a href="#">facebook</a><a href="#">WhatsApp</a><a href="#">instagram</a>
            </div>
        </div>
        <div class="credit">copyright @ 2026 by <span>mr. web designer</span></div>
    </section>

    

    
    <script src="script.js"></script>
   

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
var swiper = new Swiper(".home-slider", {
    loop: true,
    grabCursor: true,
    centeredSlides: true,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
});
</script></script>
</body>
</html>