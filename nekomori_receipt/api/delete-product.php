<?php
header("Content-Type: application/json");
include "../config.php";

$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid ID: " . $_POST['product_id']]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Deleted product ID $product_id"]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}
?>