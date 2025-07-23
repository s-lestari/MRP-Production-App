<?php
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID is required']);
    exit;
}

$id = intval($_GET['id']);
$query = "SELECT pl.*, p.product_name, m.machine_name, l.line_name, s.shift_name 
          FROM production_logs pl
          JOIN products p ON pl.product_id = p.product_id
          JOIN machine m ON pl.machine_id = m.machine_id
          JOIN production_lines l ON pl.line_id = l.id
          JOIN shifts s ON pl.shift_id = s.id
          WHERE pl.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['status' => 'error', 'message' => 'Production not found']);
}
?>