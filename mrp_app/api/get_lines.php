<?php

require_once 'config2.php';

try {
    $stmt = $pdo->query("SELECT id, line_name FROM production_lines");
    $lines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($lines);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch lines: ' . $e->getMessage()]);
}
?>