<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $product = $result->fetch_assoc();

    $product['id'] = (int)$product['id'];
    $product['category_id'] = (int)$product['category_id'];
    $product['price'] = (double)$product['price'];
    $product['stock'] = (int)$product['stock'];

    // Normalisasi URL gambar untuk Railway
    if (!empty($product['image_url'])) {

        // Jika masih URL localhost lama
        if (str_contains($product['image_url'], 'localhost')) {

            $filename = basename($product['image_url']);

            $product['image_url'] =
                'https://api-dell-production.up.railway.app/uploads/' .
                $filename;
        }

        // Jika hanya nama file
        else if (!str_starts_with($product['image_url'], 'http')) {

            $product['image_url'] =
                'https://api-dell-production.up.railway.app/uploads/' .
                $product['image_url'];
        }
    }

    echo json_encode([
        "success" => true,
        "data" => $product
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Produk tidak ditemukan"
    ]);
}

$stmt->close();
$conn->close();

?>
