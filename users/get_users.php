<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';

try {

    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    $query = "
        SELECT
            id,
            fullname,
            email,
            role,
            status
        FROM users
        ORDER BY id DESC
    ";

    $result = $conn->query($query);

    if (!$result) {
        throw new Exception($conn->error);
    }

    $users = [];

    while ($row = $result->fetch_assoc()) {

        $users[] = [
            "id" => (int)$row["id"],
            "fullname" => $row["fullname"] ?? "",
            "email" => $row["email"] ?? "",
            "role" => $row["role"] ?? "user",
            "status" => $row["status"] ?? "active"
        ];
    }

    echo json_encode([
        "success" => true,
        "message" => "Users fetched successfully",
        "data" => $users
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "data" => []
    ]);

}

if (isset($conn)) {
    $conn->close();
}

?>
