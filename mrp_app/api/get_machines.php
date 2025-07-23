<?php
require_once 'config2.php'; // Sesuaikan path

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

try {
    $stmt = $pdo->query("SELECT machine_id, machine_name FROM machine");
    $machines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'data' => $machines
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch machines: ' . $e->getMessage()
    ]);
}
?>