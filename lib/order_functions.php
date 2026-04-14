<?php

function buildOrderItems($conn) {
    $cart = $_SESSION["cart"] ?? [];

    $items = [];
    $total = 0;

    foreach ($cart as $productId => $quantity) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $itemTotal = $product["price"] * $quantity;

            $items[] = [
                "id" => $product["id"],
                "name" => $product["name"],
                "price" => $product["price"],
                "quantity" => $quantity,
                "total" => $itemTotal
            ];

            $total += $itemTotal;
        }
    }

    return [
        "items" => $items,
        "total" => $total
    ];
}


function saveOrder($conn, $customerData, $orderData) {

    // uniek ordernummer
    $orderNumber = "ORD-" . time() . rand(1000,9999);

    // bestelling opslaan
    $stmt = $conn->prepare("
        INSERT INTO orders 
        (order_number, first_name, last_name, email, address, postal_code, city, country, total, discount_code, discount_percent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $orderNumber,
        $customerData["first_name"],
        $customerData["last_name"],
        $customerData["email"],
        $customerData["address"],
        $customerData["postal_code"],
        $customerData["city"],
        $customerData["country"],
        $orderData["total"],
        $customerData["discount_code"] ?? "",
        $customerData["discount_percent"] ?? 0
    ]);

    // De correcte MySQLi manier om het laatste ID op te halen
    $orderId = $conn->insert_id;

    // 🔥 producten opslaan (aangepast aan jouw database structuur!)
    foreach ($orderData["items"] as $item) {
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, unit_price, size)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $orderId,
            $item["id"],          // Koppelt het product ID in plaats van de naam
            $item["quantity"],    // Het aantal
            $item["price"],       // De prijs (in de DB opgeslagen als unit_price)
            'M'                   // Hardcoded 'M' omdat je winkelwagen nog geen maten heeft
        ]);
    }

    // winkelwagen leegmaken
    unset($_SESSION["cart"]);

    return [
        "success" => true,
        "order_id" => $orderId,
        "order_number" => $orderNumber
    ];
}