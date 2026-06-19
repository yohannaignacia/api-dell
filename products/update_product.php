<?php
// =====================================
// HEADER CORS & JSON (Wajib agar tidak kena blokir browser)
// =====================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// =====================================
// KONEKSI DATABASE
// =====================================
include '../config/database.php'; // Sesuaikan path jika berbeda

// Ambil input JSON dari Flutter
$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Data tidak diterima"]);
    exit();
}

// =====================================
// PROSES UPDATE
// =====================================
$id = $data['id'] ?? 0;

$stmt = $conn->prepare("
    UPDATE products SET
        category_id = ?,
        name = ?,
        sku = ?,
        description = ?,
        price = ?,
        stock = ?,
        image_url = ?,
        processor = ?,
        ram = ?,
        storage = ?,
        display_size = ?,
        weight = ?
    WHERE id = ?
");

// Bind parameter (i=int, s=string, d=double)
// Pastikan urutan di sini SAMA PERSIS dengan urutan kolom di atas
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

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Produk berhasil diupdate"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gagal update: " . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
