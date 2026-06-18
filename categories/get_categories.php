<?php

header("Content-Type: application/json");

include '../config/database.php';

$result = $conn->query(
"
SELECT *
FROM categories
ORDER BY id DESC
"
);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode([
    "success"=>true,
    "data"=>$data
]);