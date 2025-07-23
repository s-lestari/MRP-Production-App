<?php
include 'config.php';

$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];
$start_date = $_POST['start_date'];
$created_by = $_POST['created_by'];

$query = "INSERT INTO production (product_id, quantity, start_date, created_by) 
          VALUES ('$product_id', '$quantity', '$start_date', '$created_by')";

if(mysqli_query($conn, $query)){
    echo json_encode(["message" => "Production added successfully"]);
} else {
    echo json_encode(["message" => "Error adding production"]);
}
?>
