<?php
header("Content-Type: application/json");
include "../config.php";

$customer_name = $_POST['customer_name'] ?? 'Walk-in';
$total_amount  = $_POST['total_amount'];
$discount      = $_POST['discount'] ?? 0;
$subtotal      = $_POST['subtotal'];
$allowed_mop   = ['Cash', 'GCash', 'Maya', 'Card', 'Bank Transfer'];
$mop           = $_POST['mop'] ?? 'Cash';
if (!in_array($mop, $allowed_mop, true)) {
    echo json_encode(["success" => false, "message" => "Invalid method of payment"]);
    exit;
}
$items         = json_decode($_POST['items'], true);
$user_id       = session_start() ? $_SESSION['user_id'] : 1;

if (empty($items)) {
    echo json_encode(["success" => false, "message" => "No items in order"]);
    exit;
}

// Insert transaction
$stmt = $conn->prepare("INSERT INTO transactions (user_id, customer_name, subtotal, discount, total_amount, mop, date)
                        VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("isddds", $user_id, $customer_name, $subtotal, $discount, $total_amount, $mop);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => $stmt->error]);
    exit;
}

$transaction_id = $stmt->insert_id;

// Insert each item & deduct stock
foreach ($items as $item) {
    $product_id = $item['id'];
    $qty        = $item['qty'];
    $unit_price = $item['price'];
    $item_sub   = $item['subtotal'];

    // Insert into transaction_items
    $sql2 = "INSERT INTO transaction_items (transaction_id, product_id, quantity, unit_price, subtotal)
             VALUES ('$transaction_id', '$product_id', '$qty', '$unit_price', '$item_sub')";
    $conn->query($sql2);

    // Deduct stock
    $sql3 = "UPDATE products SET quantity = quantity - $qty WHERE product_id = '$product_id'";
    $conn->query($sql3);
}

echo json_encode(["success" => true, "message" => "Transaction complete!", "transaction_id" => $transaction_id]);
?>