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

$sql = "
SELECT
    p.id,
    p.category_id,
    c.category_name,
    p.name,
    p.sku,
    p.description,
    p.price,
    p.stock,
    p.image_url,
    p.processor,
    p.ram,
    p.storage,
    p.display_size,
    p.weight,
    p.status,
    p.created_at,
    p.updated_at
FROM products p
LEFT JOIN categories c
ON p.category_id = c.id
ORDER BY p.id DESC
";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {

    $row['id'] = (int)$row['id'];
    $row['category_id'] = (int)$row['category_id'];
    $row['price'] = (double)$row['price'];
    $row['stock'] = (int)$row['stock'];

    // URL gambar otomatis
    if (
        !empty($row['image_url']) &&
        !str_starts_with($row['image_url'], 'http')
    ) {
        $row['image_url'] =
            'http://localhost/dell_xps_api/uploads/' .
            $row['image_url'];
    }

    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "total" => count($data),
    "data" => $data
], JSON_UNESCAPED_UNICODE);