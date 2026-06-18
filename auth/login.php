<?php

// --- KONFIGURASI CORS ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Tangani "preflight" request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
// ------------------------

include "../config/database.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

// Fallback jika dikirim menggunakan form-data biasa
if (!$data) {
    $data = $_POST;
}

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak valid"
    ]);
    exit;
}

$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Email dan Password wajib diisi"
    ]);
    exit;
}

// MENGGUNAKAN PREPARED STATEMENT UNTUK MENCEGAH SQL INJECTION
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan database: " . $conn->error
    ]);
    exit;
}

if ($result->num_rows == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Email tidak ditemukan"
    ]);
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Password salah"
    ]);
    exit;
}

// Hapus password dari response demi keamanan
unset($user['password']);

// Return JSON (Pastikan di database Anda kolom 'role' benar-benar ada)
echo json_encode([
    "success" => true,
    "message" => "Login berhasil",
    "user" => $user
]);

$stmt->close();
$conn->close();

?>