<?php

/**
 * Bouwt order items vanuit de winkelmand.
 */
function buildOrderItems(mysqli $conn): array
{
    $items = [];
    $total = 0;

    foreach ($_SESSION["cart"] ?? [] as $key => $quantity) {
        [$productId, $size, $customName] = explode("|", $key, 3);

        $result = q($conn, "SELECT id,name,price FROM products WHERE id=?", [(int)$productId]);

        if ($row = $result->fetch_assoc()) {
            $row["qty"] = $quantity;
            $row["size"] = $size;
            $row["custom_name"] = $customName;
            $row["subtotal"] = $quantity * $row["price"];

            $total += $row["subtotal"];
            $items[] = $row;
        }
    }

    return ["items" => $items, "total" => $total];
}

/**
 * Slaat een bestelling op in de database.
 */
function saveOrder(mysqli $conn, array $customerData, array $orderData): bool
{
    if (empty($orderData["items"])) {
        return false;
    }

    $stmt = $conn->prepare("
        INSERT INTO orders 
        (first_name,last_name,email,address,postal_code,city,country,total) 
        VALUES (?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "sssssssd",
        $customerData["first_name"],
        $customerData["last_name"],
        $customerData["email"],
        $customerData["address"],
        $customerData["postal_code"],
        $customerData["city"],
        $customerData["country"],
        $orderData["total"]
    );

    $stmt->execute();
    $orderId = $stmt->insert_id;

    foreach ($orderData["items"] as $item) {
        $st = $conn->prepare("
            INSERT INTO order_items 
            (order_id,product_id,size,quantity,unit_price) 
            VALUES (?,?,?,?,?)
        ");

        $st->bind_param(
            "iisid",
            $orderId,
            $item["id"],
            $item["size"],
            $item["qty"],
            $item["price"]
        );

        $st->execute();
    }

    $_SESSION["cart"] = [];
    return true;
}
S