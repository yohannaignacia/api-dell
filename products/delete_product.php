<?php

header("Content-Type: application/json");

include '../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    $data = $_POST;
}

$id = $data['id'] ?? 0;

$stmt = $conn->prepare(
    "DELETE FROM products WHERE id=?"
);

$stmt->bind_param("i",$id);

if($stmt->execute()){

    echo json_encode([
        "success" => true,
        "message" => "Produk berhasil dihapus"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);

}