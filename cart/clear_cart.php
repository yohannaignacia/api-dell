<?php

header("Content-Type: application/json");

include '../config/database.php';

$data = json_decode(
    file_get_contents("php://input"),
    true
);

if(!$data){
    $data = $_POST;
}

$user_id = $data['user_id'];

$sql = "
DELETE ci
FROM cart_items ci
JOIN carts c
ON ci.cart_id=c.id
WHERE c.user_id=?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

echo json_encode([
    "success"=>true,
    "message"=>"Cart dikosongkan"
]);