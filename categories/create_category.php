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

$name = $data['category_name'] ?? '';
$description = $data['description'] ?? '';

if(empty($name)){

    echo json_encode([
        "success"=>false,
        "message"=>"Nama kategori wajib diisi"
    ]);

    exit;
}

$stmt = $conn->prepare(
"
INSERT INTO categories
(
category_name,
description
)
VALUES
(
?,
?
)
"
);

$stmt->bind_param(
    "ss",
    $name,
    $description
);

if($stmt->execute()){

    echo json_encode([
        "success"=>true,
        "message"=>"Kategori berhasil ditambahkan"
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>$conn->error
    ]);

}