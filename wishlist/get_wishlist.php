<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "../config/database.php";

$user_id = isset($_GET['user_id'])
    ? intval($_GET['user_id'])
    : 0;

if ($user_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "User ID tidak valid"
    ]);

    exit;
}

$sql = "
SELECT
    p.id,
    p.category_id,
    p.name,
    p.description,
    p.price,
    p.stock,
    p.image_url
FROM wishlists w
INNER JOIN products p
ON p.id = w.product_id
WHERE w.user_id = '$user_id'
ORDER BY w.id DESC
";

$result = $conn->query($sql);

if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);

    exit;
}

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "total" => count($data),
    "data" => $data
]);

$conn->close();