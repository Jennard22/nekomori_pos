<?php
header("Content-Type: application/json");
include "../config.php";

$id = $_POST['transaction_id'];

if (empty($id)) {
    echo json_encode(["success" => false, "message" => "No transaction ID"]);
    exit;
}

// Delete items first, then transaction
$conn->query("DELETE FROM transaction_items WHERE transaction_id = '$id'");

if ($conn->query("DELETE FROM transactions WHERE transaction_id = '$id'")) {
    echo json_encode(["success" => true, "message" => "Transaction deleted!"]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}
?>
