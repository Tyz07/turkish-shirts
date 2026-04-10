<?php
require_once __DIR__ . "/../lib/cart_functions.php";

initializeCart();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currency = $_SESSION['currency'] ?? 'EUR';
$rate = 35;

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "add") {
  addToCart();
}

if (isset($_POST["update"])) {
  updateCart();
}

$data = getCartItems($conn);
$items = $data["items"];
$total = $data["total"];
?>

<div class="box">
  <h2>Winkelmand</h2>
  <?php if (!$items): ?>
    <p>Leeg</p>
  <?php else: ?>
    <form method="post"> <!-- bijwerken van aantallen -->
      <table class="table">
        <tr>
          <th>Product</th>
          <th>Maat / Naam</th>
          <th>Prijs</th>
          <th>Aantal</th>
          <th>Subtotaal</th>
        </tr>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?php echo htmlspecialchars($it["name"]); ?></td>
            <td>
              <?php echo htmlspecialchars($it["size"]); ?>    <?php if (!empty($it["custom_name"]))
                       echo " — " . htmlspecialchars($it["custom_name"]); ?>
            </td>
           <td>
<?php
if ($currency == "TRY") {
    echo number_format($it["price"] * $rate, 2, ",", ".") . " ₺";
} else {
    echo number_format($it["price"], 2, ",", ".") . " €";
}
?>
</td>
            <td>
              <input class="input" style="max-width:90px" type="number"
                name="items[<?php echo htmlspecialchars($it["key"]); ?>]" value="<?php echo $it["qty"]; ?>">
            </td>
            <td>
<?php
if ($currency == "TRY") {
    echo number_format($it["subtotal"] * $rate, 2, ",", ".") . " ₺";
} else {
    echo number_format($it["subtotal"], 2, ",", ".") . " €";
}
?>
</td>
          </tr>
        <?php endforeach; ?>
      </table>
   <div class="total">

<?php
if ($currency == "TRY") {
    echo number_format($total * $rate, 2, ",", ".") . " ₺";
} else {
    echo number_format($total, 2, ",", ".") . " €";
}
?>
</div>
      <div class="row">
        <button class="btn" name="update" value="1">Bijwerken</button>
        <a class="btn" href="?page=checkout">Afrekenen</a>
      </div>
    </form>
  <?php endif; ?>
</div>