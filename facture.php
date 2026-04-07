<?php
ob_start(); 

require('fpdf/fpdf.php');
require 'config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;

// ====== RECUP USER ======
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    die('Utilisateur introuvable.');
}

// ====== RECUP PANIER ======
$cart = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
$cart->execute([$user_id]);
$items = $cart->fetchAll(PDO::FETCH_ASSOC);

// ====== FONCTION DE CONVERSION SÉCURISÉE ======
// Remplace utf8_decode() qui est obsolète en PHP 8.2+
function decode_to_iso($text) {
    if ($text === null) return '';
    // On convertit de UTF-8 vers ISO-8859-1 (format compris par FPDF)
    return mb_convert_encoding($text, "ISO-8859-1", "UTF-8");
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(33, 37, 41);
        $this->Cell(100, 10, 'MON RESTO GOURMET', 0, 0, 'L');
        
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(100);
        $this->Cell(0, 10, 'FACTURE #'.date('Ymd').'-'.$_SESSION['user_id'], 0, 1, 'R');
        
        $this->Ln(5);
        $this->Line(10, 25, 200, 25);
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 10, decode_to_iso('Merci de votre visite ! Bon appétit. - Page ').$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);

// --- Section Infos ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(90, 7, 'DE :', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(90, 5, decode_to_iso("123 Avenue de la Gastronomie\nKinshasa, RDC\nContact: +243 810 000 000"), 0, 'L');

$pdf->SetY(40);
$pdf->SetX(110);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(90, 7, 'FACTURE POUR :', 0, 1, 'L');
$pdf->SetX(110);
$pdf->SetFont('Arial', '', 10);

$nom_complet = strtoupper($user['nom'] ?? '') . ' ' . ($user['prenom'] ?? '');
$adresse = $user['adresse'] ?? 'Non renseignee';
$tel = $user['telephone'] ?? 'N/A';

$pdf->MultiCell(90, 5, decode_to_iso("$nom_complet\n$adresse\nTel: $tel"), 0, 'L');

$pdf->Ln(15);

// --- Tableau ---
$pdf->SetFillColor(52, 58, 64);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 11);

$pdf->Cell(90, 10, decode_to_iso(' Désignation du Plat'), 1, 0, 'L', true);
$pdf->Cell(30, 10, 'Prix Unit.', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Quantite', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Total', 1, 1, 'C', true);

$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 10);
$total_general = 0;
$fill = false;

foreach ($items as $item) {
    $nom = $item['name'] ?? 'Plat';
    $prix = $item['price'] ?? 0;
    $qte = $item['quantity'] ?? 1;
    $total = $prix * $qte;
    $total_general += $total;

    $pdf->SetFillColor(248, 249, 250);
    
    $pdf->Cell(90, 10, ' '.decode_to_iso($nom), 'LRB', 0, 'L', $fill);
    $pdf->Cell(30, 10, number_format($prix, 2).' $', 'RB', 0, 'C', $fill);
    $pdf->Cell(30, 10, $qte, 'RB', 0, 'C', $fill);
    $pdf->Cell(40, 10, number_format($total, 2).' $', 'RB', 1, 'C', $fill);
    
    $fill = !$fill;
}

// --- Total ---
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(120, 10, '', 0, 0);
$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255);
$pdf->Cell(30, 10, 'TOTAL', 1, 0, 'C', true);
$pdf->Cell(40, 10, number_format($total_general, 2).' $', 1, 1, 'C', true);

$pdf->Output('I', 'Facture_Resto.pdf');
ob_end_flush();
?>