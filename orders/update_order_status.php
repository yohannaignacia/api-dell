<?php

header("Content-Type: application/json");

include '../config/database.php';

$data =
json_decode(
file_get_contents("php://input"),
true
);

if(!$data){
    $data = $_POST;
}

$id =
$data['order_id'];

$status =
$data['status'];

$stmt = $conn->prepare(
"
UPDATE orders
SET order_status=?
WHERE id=?
"
);

$stmt->bind_param(
    "si",
    $status,
    $id
);

$stmt->execute();

echo json_encode([
    "success"=>true
]);