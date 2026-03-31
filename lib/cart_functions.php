<?php

/**
 * Initialiseert de winkelmand in de sessie.
 */
function initializeCart(): void
{
    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }
}

/**
 * Voegt een product toe aan de winkelmand.
 */
function addToCart(): void
{
    $id = (int) ($_POST["id"] ?? 0);
    $size = trim($_POST["size"] ?? "");
    $qty = max(1, (int) ($_POST["qty"] ?? 1));
    $name = trim($_POST["custom_name"] ?? "");

    $key = $id . "|" . $size . "|" . $name;
    $_SESSION["cart"][$key] = ($_SESSION["cart"][$key] ?? 0) + $qty;

    header("Location: ?page=cart");
    exit;
}

/**
 * Werkt de winkelmand bij (aantallen/verwijderen).
 */
function updateCart(): void
{
    foreach ($_POST["items"] ?? [] as $key => $value) {
        $quantity = max(0, (int) $value);

        if ($quantity === 0) {
            unset($_SESSION["cart"][$key]);
        } else {
            $_SESSION["cart"][$key] = $quantity;
        }
    }
}

/**
 * Haalt alle winkelmand items op inclusief productgegevens.
 */
function getCartItems(mysqli $conn): array
{
    $items = [];
    $total = 0;

    foreach ($_SESSION["cart"] as $key => $quantity) {
        [$productId, $size, $customName] = explode("|", $key, 3);

        $result = q($conn, "SELECT id,name,price FROM products WHERE id=?", [(int) $productId]);

        if ($row = $result->fetch_assoc()) {
            $row["qty"] = $quantity;
            $row["key"] = $key;
            $row["size"] = $size;
            $row["custom_name"] = $customName;
            $row["subtotal"] = $quantity * $row["price"];

            $total += $row["subtotal"];
            $items[] = $row;
        }
    }

    return ["items" => $items, "total" => $total];
}
