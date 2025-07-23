<?php

require_once 'config2.php';

try {
    $stmt = $pdo->query("SELECT id, shift_name FROM shifts");
    $shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($shifts);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch shifts: ' . $e->getMessage()]);
}
?>