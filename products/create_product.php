<?php

header("Content-Type: application/json");

include '../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    $data = $_POST;
}

$category_id = $data['category_id'] ?? null;
$name = $data['name'] ?? '';
$sku = $data['sku'] ?? '';
$description = $data['description'] ?? '';
$price = $data['price'] ?? 0;
$stock = $data['stock'] ?? 0;
$image_url = $data['image_url'] ?? null;
$processor = $data['processor'] ?? null;
$ram = $data['ram'] ?? null;
$storage = $data['storage'] ?? null;
$display_size = $data['display_size'] ?? null;
$weight = $data['weight'] ?? null;

$stmt = $conn->prepare("
INSERT INTO products
(
category_id,
name,
sku,
description,
price,
stock,
image_url,
processor,
ram,
storage,
display_size,
weight
)
VALUES
(
?,
?,
?,
?,
?,
?,
?,
?,
?,
?,
?,
?
)
");

$stmt->bind_param(
    "isssdissssss",
    $category_id,
    $name,
    $sku,
    $description,
    $price,
    $stock,
    $image_url,
    $processor,
    $ram,
    $storage,
    $display_size,
    $weight
);

if($stmt->execute()){

    echo json_encode([
        "success" => true,
        "message" => "Produk berhasil ditambahkan",
        "product_id" => $conn->insert_id
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);

}