<?php
require_once 'config2.php';

$data = json_decode(file_get_contents('php://input'), true);

$job_id = $data['job_id'];
$requested_at = date('Y-m-d H:i:s');

try {
    // In your current schema, you might need to implement this differently
    // This is a simplified version that just returns success
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Material request noted (implementation needed)',
        'job_id' => $job_id
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit material request: ' . $e->getMessage()]);
}
?>