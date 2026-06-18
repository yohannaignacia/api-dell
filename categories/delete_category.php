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

$id = $data['id'];

$stmt = $conn->prepare(
"
DELETE FROM categories
WHERE id=?
"
);

$stmt->bind_param(
    "i",
    $id
);

if($stmt->execute()){

    echo json_encode([
        "success"=>true,
        "message"=>"Kategori berhasil dihapus"
    ]);

}else{

    echo json_encode([
        "success"=>false
    ]);

}