<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../config/database.php';

try {

    $rawData = file_get_contents("php://input");

    $data = json_decode($rawData, true);

    if (!$data || empty($data)) {
        $data = $_POST;
    }

    if (
        !isset($data['order_id']) ||
        !isset($data['status'])
    ) {
        echo json_encode([
            "success" => false,
            "message" => "Parameter order_id atau status tidak ditemukan"
        ]);
        exit();
    }

    $id = (int)$data['order_id'];
    $status = trim($data['status']);

    $stmt = $conn->prepare("
        UPDATE orders
        SET order_status = ?
        WHERE id = ?
    ");

    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => $conn->error
        ]);
        exit();
    }

    $stmt->bind_param(
        "si",
        $status,
        $id
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "order_id" => $id,
            "status" => $status
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => $stmt->error
        ]);

    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}
