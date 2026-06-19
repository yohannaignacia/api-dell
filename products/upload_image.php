<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

$uploadDir = "../uploads/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!isset($_FILES['image'])) {
    echo json_encode([
        "success" => false,
        "message" => "File gambar tidak ditemukan"
    ]);
    exit();
}

$file = $_FILES['image'];

$fileName = time() . "_" . basename($file["name"]);

$targetPath = $uploadDir . $fileName;

if (move_uploaded_file($file["tmp_name"], $targetPath)) {

    $imageUrl =
        "https://api-dell-production.up.railway.app/uploads/" .
        $fileName;

    echo json_encode([
        "success" => true,
        "image_url" => $imageUrl
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Upload gagal"
    ]);

}
