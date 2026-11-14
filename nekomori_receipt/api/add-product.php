<?php
header("Content-Type: application/json");
include "../config.php";

$product_name  = $_POST['product_name'];
$description   = $_POST['description'];
$category      = $_POST['category'];
$quantity      = $_POST['quantity'];
$unit_price    = $_POST['unit_price'];
$selling_price = $_POST['selling_price'];

if (empty($product_name) || empty($quantity) || empty($unit_price) || empty($selling_price)) {
    echo json_encode(["success" => false, "message" => "Required fields are missing"]);
    exit;
}

$sql = "INSERT INTO products (product_name, description, category, quantity, unit_price, selling_price)
        VALUES ('$product_name', '$description', '$category', '$quantity', '$unit_price', '$selling_price')";

if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Product added!"]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}
?>