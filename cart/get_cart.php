<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../config/database.php';

$user_id = isset($_GET['user_id'])
    ? (int)$_GET['user_id']
    : 0;

$sql = "
SELECT
    ci.id,
    ci.quantity,
    p.id AS product_id,
    p.name,
    p.price,
    p.image_url,
    (p.price * ci.quantity) AS subtotal
FROM cart_items ci
JOIN carts c ON ci.cart_id = c.id
JOIN products p ON ci.product_id = p.id
WHERE c.user_id = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
    exit();
}

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$data = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $row['quantity'] = (int)$row['quantity'];
    $row['price'] = (double)$row['price'];
    $row['subtotal'] = (double)$row['subtotal'];

    $total += $row['subtotal'];
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "total" => $total,
    "items" => $data
]);