<?php
header("Content-Type: application/json");
include "../config.php";

$product_id    = $_POST['product_id'];
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

$sql = "UPDATE products SET 
        product_name  = '$product_name',
        description   = '$description',
        category      = '$category',
        quantity      = '$quantity',
        unit_price    = '$unit_price',
        selling_price = '$selling_price'
        WHERE product_id = '$product_id'";

if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Product updated!"]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}
?>