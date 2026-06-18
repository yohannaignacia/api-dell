<?php

header("Content-Type: application/json");

include '../config/database.php';

$product =
$conn->query(
"SELECT COUNT(*) total FROM products"
)->fetch_assoc()['total'];

$user =
$conn->query(
"SELECT COUNT(*) total FROM users"
)->fetch_assoc()['total'];

$order =
$conn->query(
"SELECT COUNT(*) total FROM orders"
)->fetch_assoc()['total'];

$revenue =
$conn->query(
"
SELECT
IFNULL(SUM(total_amount),0)
AS total
FROM orders
WHERE payment_status='paid'
"
)->fetch_assoc()['total'];

echo json_encode([
    "success"=>true,
    "summary"=>[
        "products"=>$product,
        "users"=>$user,
        "orders"=>$order,
        "revenue"=>$revenue
    ]
]);