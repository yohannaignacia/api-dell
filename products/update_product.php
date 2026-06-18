<?php

header("Content-Type: application/json");

include '../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    $data = $_POST;
}

$id = $data['id'] ?? 0;

$stmt = $conn->prepare("
UPDATE products
SET
category_id=?,
name=?,
sku=?,
description=?,
price=?,
stock=?,
image_url=?,
processor=?,
ram=?,
storage=?,
display_size=?,
weight=?
WHERE id=?
");

$stmt->bind_param(
    "isssdissssssi",
    $data['category_id'],
    $data['name'],
    $data['sku'],
    $data['description'],
    $data['price'],
    $data['stock'],
    $data['image_url'],
    $data['processor'],
    $data['ram'],
    $data['storage'],
    $data['display_size'],
    $data['weight'],
    $id
);

if($stmt->execute()){

    echo json_encode([
        "success" => true,
        "message" => "Produk berhasil diupdate"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);

}