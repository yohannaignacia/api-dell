<?php

header("Content-Type: application/json");

include '../config/database.php';

$sql = "
SELECT
id,
invoice_number,
fullname,
total_amount,
order_status,
created_at
FROM orders
ORDER BY id DESC
LIMIT 10
";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode([
    "success"=>true,
    "orders"=>$data
]);