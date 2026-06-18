<?php

// ==========================
// CORS
// ==========================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ==========================
// DATABASE
// ==========================
include '../config/database.php';

// ==========================
// GET DATA
// ==========================
$data = json_decode(
    file_get_contents("php://input"),
    true
);

if (!$data) {
    $data = $_POST;
}

// ==========================
// VALIDASI
// ==========================
if (!isset($data['cart_item_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "cart_item_id tidak ditemukan"
    ]);
    exit();
}

$id = intval($data['cart_item_id']);

// ==========================
// DELETE CART
// ==========================
$stmt = $conn->prepare("
    DELETE FROM cart_items
    WHERE id = ?
");

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Produk berhasil dihapus"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();