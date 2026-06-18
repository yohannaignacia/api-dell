<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "../config/database.php";

$user_id = isset($_GET['user_id'])
    ? intval($_GET['user_id'])
    : 0;

$product_id = isset($_GET['product_id'])
    ? intval($_GET['product_id'])
    : 0;

if ($user_id <= 0 || $product_id <= 0) {

    echo json_encode([
        "success" => false,
        "is_favorite" => false
    ]);

    exit;
}

$sql = "
SELECT id
FROM wishlists
WHERE user_id = '$user_id'
AND product_id = '$product_id'
LIMIT 1
";

$result = $conn->query($sql);

echo json_encode([
    "success" => true,
    "is_favorite" =>
        ($result && $result->num_rows > 0)
]);

$conn->close();