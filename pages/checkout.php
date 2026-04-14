<?php
// Koppel het bestand met de bestelfuncties
require_once __DIR__ . "/../lib/order_functions.php";

// Zorg dat het winkelmandje klaarstaat
$_SESSION["cart"] = $_SESSION["cart"] ?? [];
$success = false;
$orderResult = null; // Hier slaan we straks het ordernummer in op

$discount = 0;
$error = "";
$code = "";

// Controleer of de klant een kortingscode probeert te gebruiken
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = strtoupper(trim($_POST["discount_code"] ?? ""));

    if ($code !== "") {
        // Kijk of de code geldig is en bepaal hoeveel korting erbij hoort
        if ($code === "KORTING10") {
            $discount = 0.10;
        } elseif ($code === "KORTING20") {
            $discount = 0.20;
        } else {
            $error = "❌ Ongeldige kortingscode";
            $discount = 0;
        }
        // Bewaar de korting in de sessie
        $_SESSION["discount"] = $discount;
    }
}

// Haal de bewaarde korting weer op
$discount = $_SESSION["discount"] ?? 0;

try {
    // Haal alle producten uit het mandje en bereken de prijs
    $orderData = buildOrderItems($conn);

    // Reken uit hoeveel er van de prijs af gaat door de korting
    $originalTotal = $orderData["total"];
    $discountAmount = $originalTotal * $discount;
    $newTotal = $originalTotal - $discountAmount;

    // Als de klant het bestelformulier verstuurt
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        // Verzamel alle verplichte adresgegevens van de klant
        $customerData = [
            "first_name" => trim($_POST["first_name"] ?? ""),
            "last_name" => trim($_POST["last_name"] ?? ""),
            "email" => trim($_POST["email"] ?? ""),
            "address" => trim($_POST["address"] ?? ""),
            "postal_code" => trim($_POST["postal_code"] ?? ""),
            "city" => trim($_POST["city"] ?? ""),
            "country" => trim($_POST["country"] ?? "")
        ];

        // Kijk of het formulier echt is ingevuld (voornaam is aanwezig)
        if (isset($_POST["first_name"])) {
            
            // Controleer of er geen verplichte vakjes leeg zijn gelaten
            if (!in_array("", $customerData, true)) {
                
                // Voeg de kortingsgegevens toe aan de klantgegevens
                $customerData["discount_code"] = $code;
                $customerData["discount_percent"] = $discount;

                // Geef het definitieve bedrag (met korting) mee aan de bestelling
                $orderData["total"] = $newTotal;

                // Sla de hele bestelling op in de database
                $orderResult = saveOrder($conn, $customerData, $orderData);
                $success = $orderResult["success"] ?? false;

                // Reset de korting voor een volgende klant
                unset($_SESSION["discount"]);
            } else {
                $error = "❌ Vul a.u.b. alle verplichte velden in.";
            }
        }
    }

} catch (Throwable $e) {
    // Vang database-fouten op en toon een melding zodat het systeem niet crasht
    die("<strong>Systeemfout:</strong> " . $e->getMessage());
}
?>

<?php if ($success): ?>
    <div class="box">
        <h2>🎉 Bestelling geplaatst!</h2>
        <p>Bedankt voor je bestelling, <strong><?php echo htmlspecialchars($customerData["first_name"]); ?></strong>.</p>
        <p>Je ordernummer is: <strong><?php echo $orderResult["order_number"]; ?></strong></p>
        
        <hr>
        
        <h3>Overzicht van je bestelling:</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="border-bottom: 1px solid #ddd; text-align: left;">
                    <th>Product</th>
                    <th>Aantal</th>
                    <th>Prijs</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderData["items"] as $item): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0;"><?php echo htmlspecialchars($item["name"]); ?> (Maat: M)</td>
                        <td><?php echo $item["quantity"]; ?>x</td>
                        <td>€<?php echo number_format($item["total"], 2, ",", "."); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="text-align: right; font-size: 1.2em;">
            <strong>Totaal betaald: €<?php echo number_format($orderData["total"], 2, ",", "."); ?></strong>
        </div>

        <br>
        <a class="btn" href="?page=home">Verder winkelen</a>
    </div>

<?php else: ?>
    <div class="box">
        <h2>Afrekenen</h2>

        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

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

            <div class="row">
                <input class="input" name="discount_code" placeholder="Kortingscode" value="<?php echo htmlspecialchars($code); ?>">
            </div>

            <div class="total">
                <?php if ($discount > 0): ?>
                    <p>Origineel: €<?php echo number_format($originalTotal, 2, ",", "."); ?></p>
                    <p>Korting: -€<?php echo number_format($discountAmount, 2, ",", "."); ?></p>
                <?php endif; ?>

                <strong>
                    Totaal: €<?php echo number_format($newTotal, 2, ",", "."); ?>
                </strong>
            </div>

            <button class="btn" type="submit">Bestellen</button>
        </form>
    </div>
<?php endif; ?>