<?php

header("Content-Type: application/json");

include '../config/database.php';

$sql = "
SELECT
DATE(created_at) tanggal,
SUM(total_amount) total_penjualan,
COUNT(*) jumlah_order
FROM orders
GROUP BY DATE(created_at)
ORDER BY DATE(created_at) DESC
";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode([
    "success"=>true,
    "report"=>$data
]);