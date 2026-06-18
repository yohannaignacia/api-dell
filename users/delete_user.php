<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

// Menangkap data ID yang dikirim dari Flutter
$id = isset($_POST['id']) ? $_POST['id'] : die();

// Sesuaikan nama tabel dengan database kamu
$query = "DELETE FROM users WHERE id = '$id'";

if ($conn->query($query) === TRUE) {
    echo json_encode(["success" => true, "message" => "Pengguna berhasil dihapus."]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menghapus pengguna."]);
}
?>