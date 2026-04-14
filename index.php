<?php
require __DIR__ . "/config.php";   // site/db instellingen
require __DIR__ . "/lib/db.php";   // database connectie

// Start session (maar 1x!)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Currency switch opslaan
if (isset($_GET['currency'])) {
    $_SESSION['currency'] = $_GET['currency'];
}

$currency = $_SESSION['currency'] ?? 'EUR';
$rate = 52; // wisselkoers

// Pagina bepalen
$p = $_GET["page"] ?? "home";
$pages = ["home", "cart", "checkout",];

if (!in_array($p, $pages)) {
    $p = "home";
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Voetbalshop TR</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header class="hdr">
    <div class="wrap">
        <a class="logo" href="?page=home">Voetbalshop</a>

        <!-- Currency switch (nu op juiste plek) -->
        <div class="currency-switch">
            <a href="?page=<?php echo $p; ?>&currency=EUR">€</a>
            <a href="?page=<?php echo $p; ?>&currency=TRY">₺</a>
        </div>

        <nav>
            <a href="?page=home">Producten</a>
            <a href="?page=cart">Winkelmand</a>
        </nav>
    </div>
</header>

<section class="hero">
    <div class="wrap"> <!-- dit is waarmee de klanten worden begroet -->
        <h1>Turkse Clubshirts</h1>
        <p>Selecteer je club en maat – S t/m XXL op voorraad.</p>
    </div>
</section>

<main class="wrap">
    <?php include __DIR__ . "/pages/" . $p . ".php"; ?>
</main>

<footer class="ftr">
    <div class="wrap">© Voetbalshop</div><!-- footer wat onder de site van elke pagina blijft -->
</footer>

<script src="assets/script.js"></script>
</body>
</html>