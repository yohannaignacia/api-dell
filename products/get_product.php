<?php

header("Content-Type: application/json");

include '../config/database.php';

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("
SELECT
    p.*,
    c.category_name
FROM products p
LEFT JOIN categories c
ON p.category_id = c.id
WHERE p.id = ?
");

$stmt->bind_param("i",$id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0){

    $product = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "data" => $product
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Produk tidak ditemukan"
    ]);

}