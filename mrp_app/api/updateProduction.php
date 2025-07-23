<?php
include 'config.php';

$production_id = $_POST['production_id'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];
$start_date = $_POST['start_date'];
$status = $_POST['status'];

$query = "UPDATE production 
          SET product_id='$product_id', quantity='$quantity', start_date='$start_date', status='$status'
          WHERE production_id='$production_id'";

if(mysqli_query($conn, $query)){
    echo json_encode(["message" => "Production updated successfully"]);
} else {
    echo json_encode(["message" => "Error updating production"]);
}
?>
