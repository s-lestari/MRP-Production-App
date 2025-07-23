<?php

require_once 'config2.php';

$data = json_decode(file_get_contents('php://input'), true);

$job_id = $data['job_id'];

try {
    $up = $pdo->prepare("UPDATE jobs SET status = 'Completed', completed_at = NOW() WHERE job_id = ?");
    $up->execute([$job_id]);

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Job completion noted (implementation needed)'
    ]);

    if ($up->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Job marked as completed successfully',
            'job_id' => $job_id
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => "No job found with ID $job_id"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to complete job: ' . $e->getMessage()]);
}
?>