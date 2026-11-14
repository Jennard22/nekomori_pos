<?php
header("Content-Type: application/json");
include "../config.php";

$id = $_GET['id'];

$t = $conn->query("SELECT * FROM transactions WHERE transaction_id = '$id'")->fetch_assoc();
$items = [];
$result = $conn->query("SELECT ti.*, p.product_name FROM transaction_items ti 
                        JOIN products p ON ti.product_id = p.product_id 
                        WHERE ti.transaction_id = '$id'");
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode(["success" => true, "transaction" => $t, "items" => $items]);
?>