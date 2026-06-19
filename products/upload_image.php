<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");
include '../config/database.php';

$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Data tidak diterima"]);
    exit();
}

$id = $data['id'];
$stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, sku=?, description=?, price=?, stock=?, image_url=?, processor=?, ram=?, storage=?, display_size=?, weight=? WHERE id=?");

$stmt->bind_param("isssdissssssi", 
    $data['category_id'], $data['name'], $data['sku'], $data['description'], 
    $data['price'], $data['stock'], $data['image_url'], 
    $data['processor'], $data['ram'], $data['storage'], 
    $data['display_size'], $data['weight'], $id
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Produk berhasil diupdate"]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}
$stmt->close();
$conn->close();
?>
