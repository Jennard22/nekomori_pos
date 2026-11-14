<?php
$conn = new mysqli('localhost', 'root', '', 'nekomori_pos');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>