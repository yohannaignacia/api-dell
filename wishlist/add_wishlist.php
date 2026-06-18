<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

include "../config/database.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Data JSON tidak diterima"
    ]);
    exit;
}

$user_id = isset($data['user_id'])
    ? intval($data['user_id'])
    : 0;

$product_id = isset($data['product_id'])
    ? intval($data['product_id'])
    : 0;

if ($user_id <= 0 || $product_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "User ID atau Product ID tidak valid"
    ]);

    exit;
}

$check = $conn->query("
    SELECT id
    FROM wishlists
    WHERE user_id = '$user_id'
    AND product_id = '$product_id'
");

if ($check && $check->num_rows > 0) {

    echo json_encode([
        "success" => true,
        "message" => "Produk sudah ada di wishlist"
    ]);

    exit;
}

$sql = "
INSERT INTO wishlists(
    user_id,
    product_id
)
VALUES(
    '$user_id',
    '$product_id'
)
";

if ($conn->query($sql)) {

    echo json_encode([
        "success" => true,
        "message" => "Berhasil ditambahkan ke wishlist"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
}

$conn->close();
?>