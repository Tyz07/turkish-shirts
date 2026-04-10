<?php
require __DIR__ . "/config.php";   // site/db instellingen
require __DIR__ . "/lib/db.php";   // database connectie

if (session_status() === PHP_SESSION_NONE) {
    session_start();
};

if (isset($_GET['currency'])) {
    $_SESSION['currency'] = $_GET['currency'];
}

$currency = $_SESSION['currency'] ?? 'EUR';
$rate =52; // wisselkoers


$p = $_GET["page"] ?? "home";      // kies pagina uit URL of 'home'
$pages = ["home", "cart", "checkout", "login", "logout", "admin"]; // toegestane views
if (!in_array($p, $pages)) {       // voorkom vreemde includes
    $p = "home";
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8"> <!-- correcte tekens -->
    <meta name="viewport" content="width=device-width,initial-scale=1"> <!-- responsive -->
    <title>Voetbalshop TR</title> <!-- paginatitel -->
    <link rel="stylesheet" href="assets/css/style.css"> <!-- styles -->

   <div class="currency-switch">
<a href="?page=<?php echo $_GET['page'] ?? 'home'; ?>&currency=EUR">€</a>
<a href="?page=<?php echo $_GET['page'] ?? 'home'; ?>&currency=TRY">₺</a>
</div>
</head>

<body>

    <header class="hdr">
        <div class="wrap">
            <a class="logo" href="?page=home">Voetbalshop</a> <!-- naar home -->
            <nav>
                <a href="?page=home">Producten</a>
                <a href="?page=cart">Winkelmand</a>
                <?php

                ?>
            </nav>
        </div>
    </header>

    <section class="hero"> <!-- korte intro/banner -->
        <div class="wrap">
            <h1>Turkse Clubshirts</h1>
            <p>Selecteer je club en maat – S t/m XXL op voorraad.</p>
        </div>
    </section>

    <main class="wrap">
        <?php include __DIR__ . "/pages/" . $p . ".php"; // laad de gekozen view ?>
    </main>

    <footer class="ftr">
        <div class="wrap">© Voetbalshop</div> <!-- footer -->
    </footer>

    <script src="assets/script.js"></script> <!-- scripts -->
</body>

</html>