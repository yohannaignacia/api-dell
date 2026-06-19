<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Tangani Preflight Request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { 
    http_response_code(200); 
    exit(); 
}

if (isset($_FILES['image'])) {
    // Pastikan folder 'uploads' ini ada di struktur project PHP kamu
    $target_dir = "../uploads/"; 
    if(!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Memberi nama unik agar gambar tidak bentrok
    $file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", basename($_FILES["image"]["name"]));
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Ambil protokol (http/https) dan nama domain dari Railway
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $domain = $_SERVER['HTTP_HOST'];
        
        $image_url = $protocol . "://" . $domain . "/uploads/" . $file_name;
        
        echo json_encode(["success" => true, "image_url" => $image_url]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal menyimpan file ke folder"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Tidak ada file gambar yang dikirim"]);
}
?>
