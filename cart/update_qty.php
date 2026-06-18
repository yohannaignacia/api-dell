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

$id = $data['cart_item_id'];
$qty = $data['quantity'];

$stmt = $conn->prepare(
"
UPDATE cart_items
SET quantity=?
WHERE id=?
"
);

$stmt->bind_param(
    "ii",
    $qty,
    $id
);

if($stmt->execute()){

    echo json_encode([
        "success"=>true,
        "message"=>"Quantity berhasil diubah"
    ]);

}else{

    echo json_encode([
        "success"=>false
    ]);

}