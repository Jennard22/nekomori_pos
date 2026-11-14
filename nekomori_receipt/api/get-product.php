<?php
header("Content-Type: application/json");
include "../config.php";

$id = $_GET['id'];

$result = $conn->query("SELECT * FROM products WHERE product_id = '$id'");
$product = $result->fetch_assoc();

if ($product) {
    echo json_encode(["success" => true, "product" => $product]);
} else {
    echo json_encode(["success" => false, "message" => "Product not found"]);
}
?>