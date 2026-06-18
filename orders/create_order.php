<?php

// --- KONFIGURASI CORS HARUS DI BARIS PALING ATAS ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Tangani "preflight" request dari browser
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
// --------------------------------------------------

header("Content-Type: application/json");

include '../config/database.php';

$data = json_decode(
    file_get_contents("php://input"),
    true
);

if(!$data){
    $data = $_POST;
}

$user_id = $data['user_id'];
$fullname = $data['fullname'];
$phone = $data['phone'];
$address = $data['address'];

$invoice = "INV" . date("YmdHis");

$sql = "
SELECT
    ci.quantity,
    p.id,
    p.name,
    p.price
FROM cart_items ci
JOIN carts c ON ci.cart_id = c.id
JOIN products p ON ci.product_id = p.id
WHERE c.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$items = [];
$subtotal = 0;

while($row = $result->fetch_assoc()){
    $rowTotal = $row['price'] * $row['quantity'];
    $subtotal += $rowTotal;
    $items[] = $row;
}

if(count($items) == 0){
    echo json_encode([
        "success" => false,
        "message" => "Cart kosong"
    ]);
    exit;
}

$shipping = 0;
$total = $subtotal + $shipping;

$orderSql = "
INSERT INTO orders
(
    user_id,
    invoice_number,
    fullname,
    phone,
    address,
    subtotal,
    shipping_cost,
    total_amount
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($orderSql);

$stmt->bind_param(
    "issssddd",
    $user_id,
    $invoice,
    $fullname,
    $phone,
    $address,
    $subtotal,
    $shipping,
    $total
);

$stmt->execute();

$order_id = $conn->insert_id;

foreach($items as $item){
    $qty = $item['quantity'];
    $price = $item['price'];
    $lineTotal = $qty * $price;

    $stmtItem = $conn->prepare("
        INSERT INTO order_items
        (
            order_id,
            product_id,
            product_name,
            price,
            quantity,
            total
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmtItem->bind_param(
        "iisdii",
        $order_id,
        $item['id'],
        $item['name'],
        $price,
        $qty,
        $lineTotal
    );

    $stmtItem->execute();
}

// Hapus isi keranjang setelah checkout berhasil
$conn->query("
    DELETE ci
    FROM cart_items ci
    JOIN carts c ON ci.cart_id = c.id
    WHERE c.user_id = '$user_id'
");

echo json_encode([
    "success" => true,
    "order_id" => $order_id,
    "invoice" => $invoice
]);

?>