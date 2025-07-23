<?php
header('Content-Type: application/json');
require_once 'config.php';

$query = "SELECT pl.*, p.product_name, m.machine_name, l.line_name, s.shift_name 
          FROM production_logs pl
          JOIN products p ON pl.product_id = p.product_id
          JOIN machine m ON pl.machine_id = m.machine_id
          JOIN production_lines l ON pl.line_id = l.id
          JOIN shifts s ON pl.shift_id = s.id
          ORDER BY pl.production_date DESC";
$result = $conn->query($query);

$productionList = [];
while ($row = $result->fetch_assoc()) {
    $productionList[] = $row;
}

echo json_encode($productionList);
?>