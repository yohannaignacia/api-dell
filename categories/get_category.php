<?php

header("Content-Type: application/json");

include '../config/database.php';

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare(
"
SELECT *
FROM categories
WHERE id=?
"
);

$stmt->bind_param("i",$id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0){

    echo json_encode([
        "success"=>true,
        "data"=>$result->fetch_assoc()
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>"Kategori tidak ditemukan"
    ]);

}