<?php
require_once __DIR__ . "/../lib/order_functions.php";

$_SESSION["cart"] = $_SESSION["cart"] ?? [];
$success = false;
$orderResult = null; // Hier slaan we de details van de nieuwe bestelling in op

$discount = 0;
$error = "";
$code = "";

// kortingscode verwerken
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = strtoupper(trim($_POST["discount_code"] ?? ""));

    if ($code !== "") {
        if ($code === "KORTING10") {
            $discount = 0.10;
        } elseif ($code === "KORTING20") {
            $discount = 0.20;
        } else {
            $error = "❌ Ongeldige kortingscode";
            $discount = 0;
        }
        $_SESSION["discount"] = $discount;
    }
}

// korting ophalen
$discount = $_SESSION["discount"] ?? 0;

try {
    $orderData = buildOrderItems($conn);

    // totaal berekenen met korting
    $originalTotal = $orderData["total"];
    $discountAmount = $originalTotal * $discount;
    $newTotal = $originalTotal - $discountAmount;

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

        if (isset($_POST["first_name"])) {
            if (!in_array("", $customerData, true)) {
                
                $customerData["discount_code"] = $code;
                $customerData["discount_percent"] = $discount;

                $orderData["total"] = $newTotal;

                // We slaan de resultaten (order_id, order_number) op in $orderResult
                $orderResult = saveOrder($conn, $customerData, $orderData);
                $success = $orderResult["success"] ?? false;

                unset($_SESSION["discount"]);
            } else {
                $error = "❌ Vul a.u.b. alle verplichte velden in.";
            }
        }
    }

} catch (Throwable $e) {
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