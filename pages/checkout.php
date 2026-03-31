<?php
require_once __DIR__ . "/../lib/order_functions.php";

$_SESSION["cart"] = $_SESSION["cart"] ?? [];
$success = false;

try {
    $orderData = buildOrderItems($conn);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $customerData = [
            "first_name" => trim($_POST["first_name"] ?? ""),
            "last_name" => trim($_POST["last_name"] ?? ""),
            "email" => trim($_POST["email"] ?? ""),
            "address" => trim($_POST["address"] ?? ""),
            "postal_code" => trim($_POST["postal_code"] ?? ""),
            "city" => trim($_POST["city"] ?? ""),
            "country" => trim($_POST["country"] ?? "")
        ];

        if (!in_array("", $customerData, true)) {
            $success = saveOrder($conn, $customerData, $orderData);
        }
    }

} catch (Exception $e) {
    die("Er is een fout opgetreden bij het verwerken van je bestelling.");
}
?>

<?php if ($success): ?>
    <div class="box">
        <h2>Bestelling geplaatst</h2>
        <p>Bedankt voor je bestelling.</p>
        <a class="btn" href="?page=home">Verder winkelen</a>
    </div>
<?php else: ?>
    <div class="box">
        <h2>Afrekenen</h2>
        <form method="post">
            <div class="row">
                <input class="input" name="first_name" placeholder="Voornaam" required>
                <input class="input" name="last_name" placeholder="Achternaam" required>
            </div>
            <div class="row">
                <input class="input" type="email" name="email" placeholder="E-mail" required>
            </div>
            <div class="row">
                <input class="input" name="address" placeholder="Adres" required>
                <input class="input" name="postal_code" placeholder="Postcode" required>
            </div>
            <div class="row">
                <input class="input" name="city" placeholder="Stad" required>
                <input class="input" name="country" placeholder="Land" required>
            </div>

            <div class="total">
                Totaal: €<?php echo number_format($orderData["total"], 2, ",", "."); ?>
            </div>

            <button class="btn" type="submit">Bestellen</button>
        </form>
    </div>
<?php endif; ?>