<?php

// --- KONFIGURASI CORS ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
// -----------------------

header("Content-Type: application/json");

include '../config/database.php';

$data = json_decode(
    file_get_contents("php://input"),
    true
);

if(!$data){
    $data = $_POST;
}

$id = $data['order_id'];

// 1. Hapus data produk di tabel order_items terlebih dahulu 
// (untuk mencegah error constraint relasi database)
$stmtItems = $conn->prepare("
    DELETE FROM order_items
    WHERE order_id = ?
");
$stmtItems->bind_param("i", $id);
$stmtItems->execute();

// 2. Hapus data pesanan utama di tabel orders
$stmtOrder = $conn->prepare("
    DELETE FROM orders
    WHERE id = ?
");
$stmtOrder->bind_param("i", $id);
$stmtOrder->execute();

echo json_encode([
    "success" => true,
    "message" => "Pesanan berhasil dibatalkan dan dihapus dari riwayat"
]);

?>