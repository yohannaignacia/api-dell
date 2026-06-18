<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include "../config/database.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak valid"
    ]);
    exit;
}

$fullname = trim(
    $data['fullname'] ?? ''
);

$email = trim(
    $data['email'] ?? ''
);

$phone = trim(
    $data['phone'] ?? ''
);

$passwordInput = trim(
    $data['password'] ?? ''
);

if (
    empty($fullname) ||
    empty($email) ||
    empty($phone) ||
    empty($passwordInput)
) {
    echo json_encode([
        "success" => false,
        "message" => "Semua field wajib diisi"
    ]);
    exit;
}

$password = password_hash(
    $passwordInput,
    PASSWORD_DEFAULT
);

$check = $conn->query(
    "SELECT id
     FROM users
     WHERE email='$email'"
);

if ($check->num_rows > 0) {

    echo json_encode([
        "success" => false,
        "message" => "Email sudah digunakan"
    ]);

    exit;
}

$sql = "
INSERT INTO users(
    fullname,
    email,
    phone,
    password,
    role,
    status
)
VALUES(
    '$fullname',
    '$email',
    '$phone',
    '$password',
    'customer',
    'active'
)
";

if ($conn->query($sql)) {

    echo json_encode([
        "success" => true,
        "message" => "Registrasi berhasil"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
}

$conn->close();