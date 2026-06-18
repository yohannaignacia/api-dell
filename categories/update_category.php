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
$name = $data['category_name'];
$description = $data['description'];

$stmt = $conn->prepare(
"
UPDATE categories
SET
category_name=?,
description=?
WHERE id=?
"
);

$stmt->bind_param(
    "ssi",
    $name,
    $description,
    $id
);

if($stmt->execute()){

    echo json_encode([
        "success"=>true,
        "message"=>"Kategori berhasil diupdate"
    ]);

}else{

    echo json_encode([
        "success"=>false
    ]);

}