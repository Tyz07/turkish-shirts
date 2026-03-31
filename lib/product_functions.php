<?php

/**
 * Bouwt de filter query voor producten.
 */
function buildProductQuery(array $filters): array
{
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];

    if (!empty($filters["q"])) {
        $sql .= " AND name LIKE ?";
        $params[] = "%" . $filters["q"] . "%";
    }

    if (!empty($filters["club"])) {
        $sql .= " AND club = ?";
        $params[] = $filters["club"];
    }

    if (!empty($filters["min"])) {
        $sql .= " AND price >= ?";
        $params[] = $filters["min"];
    }

    if (!empty($filters["max"])) {
        $sql .= " AND price <= ?";
        $params[] = $filters["max"];
    }

    return [$sql, $params];
}

/**
 * Haalt gefilterde producten op.
 */
function getFilteredProducts(mysqli $conn, array $filters): mysqli_result
{
    [$sql, $params] = buildProductQuery($filters);
    return q($conn, $sql, $params);
}

/**
 * Haalt alle unieke clubs op.
 */
function getAllClubs(mysqli $conn): mysqli_result
{
    return q($conn, "SELECT DISTINCT club FROM products");
}

/**
 * Haalt beschikbare maten per product op.
 */
function getAvailableSizes(mysqli $conn): array
{
    $result = q($conn, "SELECT product_id,size FROM product_variants WHERE stock>0");
    $sizeMap = [];

    while ($row = $result->fetch_assoc()) {
        $sizeMap[$row["product_id"]][] = $row["size"];
    }

    return $sizeMap;
}
