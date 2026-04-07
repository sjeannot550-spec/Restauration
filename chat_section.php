<?php
if(!isset($_SESSION)){
    session_start();
}

require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$message_status = "";

// Récupération des informations du client
$user_info = null;
if($user_id){
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?"); // Assurez-vous que votre table s'appelle 'users'
    $stmt->execute([$user_id]);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Envoi du message (Commande)
if(isset($_POST['send_message']) && $user_id){
    $commande = htmlspecialchars($_POST['commande']);
    $user_name = $user_info['nom'] ?? 'Client #'.$user_id;

    if(!empty($commande)){
        // On insère la commande dans la base de données pour l'administrateur
        $insert_msg = $pdo->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
        if($insert_msg->execute([$user_id, "COMMANDE : " . $commande])){
            $message_status = "success";
        } else {
            $message_status = "error";
        }
    }
}
?>

<style>
    .chat-section {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .chat-section h2 {
        color: #2c3e50;
        text-align: center;
        margin-bottom: 20px;
    }
    .user-badge {
        background: #e1f5fe;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 5px solid #03a9f4;
    }
    .chat-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .chat-form label {
        font-weight: bold;
        color: #555;
    }
    .chat-form input[readonly] {
        background-color: #eee;
        cursor: not-allowed;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
    }
    .chat-form textarea {
        padding: 12px;
        border: 2px solid #03a9f4;
        border-radius: 8px;
        resize: vertical;
        min-height: 100px;
    }
    .btn-send {
        background-color: #27ae60;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: background 0.3s;
    }
    .btn-send:hover {
        background-color: #219150;
    }
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        text-align: center;
    }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<section class="chat-section">
    <h2>Passer une Commande 🍽️</h2>

    <?php if($message_status == "success"): ?>
        <div class="alert alert-success">✅ Votre commande a été envoyée avec succès au chef !</div>
    <?php elseif($message_status == "error"): ?>
        <div class="alert alert-error">❌ Une erreur est survenue, veuillez réessayer.</div>
    <?php endif; ?>

    <?php if($user_id && $user_info): ?>
        <div class="user-badge">
            <p><strong>Client :</strong> <?= htmlspecialchars($user_info['nom'] ?? 'Non défini'); ?></p>
            <p><strong>Contact :</strong> <?= htmlspecialchars($user_info['telephone'] ?? 'Non défini'); ?></p>
        </div>

        <form method="POST" class="chat-form">
            <label>Votre Nom</label>
            <input type="text" value="<?= htmlspecialchars($user_info['nom'] ?? ''); ?>" readonly>

            <label>Quel plat désirez-vous commander ?</label>
            <textarea name="commande" placeholder="Ex: 1 Poulet Mayo avec frites..." required></textarea>
            
            <button type="submit" name="send_message" class="btn-send">Envoyer la commande</button>
        </form>
    <?php else: ?>
        <p style="text-align:center;">Veuillez vous connecter pour passer une commande.</p>
    <?php endif; ?>
</section>