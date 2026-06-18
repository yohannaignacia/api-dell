<?php

// --- KONFIGURASI CORS HARUS DI BARIS PALING ATAS ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Tangani "preflight" request dari browser
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
// --------------------------------------------------

header("Content-Type: application/json");

include '../config/database.php';

// Pengecekan keamanan: pastikan order_id dikirimkan
if (!isset($_GET['order_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Parameter order_id tidak ditemukan"
    ]);
    exit();
}

$order_id = $_GET['order_id'];

$stmt = $conn->prepare("
    SELECT *
    FROM order_items
    WHERE order_id = ?
");

$stmt->bind_param(
    "i",
    $order_id
);

$stmt->execute();

$result = $stmt->get_result();

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "items" => $data
]);

?>