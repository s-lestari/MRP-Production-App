<?php
include 'config.php';

$production_id = $_POST['production_id'];

$query = "DELETE FROM production WHERE production_id='$production_id'";

if(mysqli_query($conn, $query)){
    echo json_encode(["message" => "Production deleted successfully"]);
} else {
    echo json_encode(["message" => "Error deleting production"]);
}
?>
