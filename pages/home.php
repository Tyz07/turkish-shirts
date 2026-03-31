<?php
require_once __DIR__ . "/../lib/product_functions.php";

$filters = [
  "club" => $_GET["club"] ?? "",
  "q" => $_GET["q"] ?? "",
  "min" => $_GET["min"] ?? "",
  "max" => $_GET["max"] ?? ""
];

try {
  $products = getFilteredProducts($conn, $filters);
  $clubs = getAllClubs($conn);
  $varMap = getAvailableSizes($conn);
} catch (Exception $e) {
  die("Er is een fout opgetreden.");
}
?>

<div class="box">
  <form method="get" class="row">
    <input type="hidden" name="page" value="home">

    <div style="flex:2">
      <input class="input" type="text" name="q" placeholder="Zoeken"
        value="<?php echo htmlspecialchars($filters["q"]); ?>">
    </div>

    <div style="flex:1">
      <select name="club" class="input">
        <option value="">Club</option>
        <?php while ($r = $clubs->fetch_assoc()): ?>
          <option value="<?php echo htmlspecialchars($r["club"]); ?>" <?php echo $filters["club"] === $r["club"] ? "selected" : ""; ?>>
            <?php echo htmlspecialchars($r["club"]); ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div style="flex:1">
      <input class="input" type="number" step="0.01" name="min" placeholder="Min €"
        value="<?php echo htmlspecialchars($filters["min"]); ?>">
    </div>

    <div style="flex:1">
      <input class="input" type="number" step="0.01" name="max" placeholder="Max €"
        value="<?php echo htmlspecialchars($filters["max"]); ?>">
    </div>

    <div><button class="btn" type="submit">Filter</button></div>
    <div><a class="btn alt" href="?page=home">Reset</a></div>
  </form>
</div>

<div class="grid">
  <?php while ($p = $products->fetch_assoc()):
    $sizes = $varMap[$p["id"]] ?? [];
    ?>
    <div class="card">
      <img src="assets/img/<?php echo htmlspecialchars($p["image"]); ?>" alt="">
      <div class="p">
        <div class="badge"><?php echo htmlspecialchars($p["club"]); ?></div>
        <h3 style="margin:8px 0 4px"><?php echo htmlspecialchars($p["name"]); ?></h3>
        <div class="price">
          €<?php echo number_format($p["price"], 2, ",", "."); ?>
        </div>

        <form method="post" action="?page=cart">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="id" value="<?php echo $p["id"]; ?>">

          <div class="row">
            <select class="input" name="size" required>
              <option value="">Kies maat</option>
              <?php foreach ($sizes as $s): ?>
                <option value="<?php echo htmlspecialchars($s); ?>">
                  <?php echo htmlspecialchars($s); ?>
                </option>
              <?php endforeach; ?>
            </select>

            <input class="input" type="text" name="custom_name" placeholder="Naam op shirt (optioneel)">

            <input class="input" type="number" name="qty" value="1" min="1" style="max-width:90px">

            <button class="btn" type="submit">In mand</button>
          </div>
        </form>
      </div>
    </div>
  <?php endwhile; ?>
</div>