
<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

try {

    $rawInput = file_get_contents("php://input");

    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        $data = $_POST;
    }

    $user_id = isset($data['user_id'])
        ? (int)$data['user_id']
        : 0;

    $product_id = isset($data['product_id'])
        ? (int)$data['product_id']
        : 0;

    $quantity = isset($data['quantity'])
        ? (int)$data['quantity']
        : 1;

    if ($quantity <= 0) {
        $quantity = 1;
    }

    if ($user_id <= 0 || $product_id <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "User ID atau Product ID tidak valid",
            "received" => $data
        ]);
        exit();
    }

    // Cek cart milik user
    $stmt = $conn->prepare(
        "SELECT id
         FROM carts
         WHERE user_id = ?
         LIMIT 1"
    );

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $cart = $result->fetch_assoc();
        $cart_id = (int)$cart['id'];

    } else {

        $stmtInsertCart = $conn->prepare(
            "INSERT INTO carts(user_id)
             VALUES(?)"
        );

        $stmtInsertCart->bind_param(
            "i",
            $user_id
        );

        if (!$stmtInsertCart->execute()) {
            throw new Exception(
                $stmtInsertCart->error
            );
        }

        $cart_id = $conn->insert_id;
    }

    // Cek apakah produk sudah ada
    $stmtCheck = $conn->prepare(
        "SELECT id, quantity
         FROM cart_items
         WHERE cart_id = ?
         AND product_id = ?
         LIMIT 1"
    );

    $stmtCheck->bind_param(
        "ii",
        $cart_id,
        $product_id
    );

    $stmtCheck->execute();

    $existing = $stmtCheck
        ->get_result();

    if ($existing->num_rows > 0) {

        $item = $existing->fetch_assoc();

        $newQty =
            ((int)$item['quantity'])
            + $quantity;

        $stmtUpdate = $conn->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE id = ?"
        );

        $stmtUpdate->bind_param(
            "ii",
            $newQty,
            $item['id']
        );

        if (!$stmtUpdate->execute()) {
            throw new Exception(
                $stmtUpdate->error
            );
        }

    } else {

        $stmtInsertItem = $conn->prepare(
            "INSERT INTO cart_items(
                cart_id,
                product_id,
                quantity
            )
            VALUES(
                ?, ?, ?
            )"
        );

        $stmtInsertItem->bind_param(
            "iii",
            $cart_id,
            $product_id,
            $quantity
        );

        if (!$stmtInsertItem->execute()) {
            throw new Exception(
                $stmtInsertItem->error
            );
        }
    }

    echo json_encode([
        "success" => true,
        "message" => "Produk berhasil ditambahkan ke cart",
        "cart_id" => $cart_id,
        "user_id" => $user_id,
        "product_id" => $product_id,
        "quantity" => $quantity
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
