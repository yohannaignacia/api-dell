<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request (Flutter Web)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';

try {

    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Ambil ID dari Flutter
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        throw new Exception("User ID is required");
    }

    // Hapus user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    if ($stmt->affected_rows > 0) {

        echo json_encode([
            "success" => true,
            "message" => "Pengguna berhasil dihapus"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Pengguna tidak ditemukan"
        ]);

    }

    $stmt->close();

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}

if (isset($conn)) {
    $conn->close();
}

?>
