<?php
header('Content-Type: application/json');
require_once 'config2.php';

$response = [
    'status' => 'error',
    'message' => 'Unknown error',
    'debug' => ''
];

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }

    $product_id = isset($input['product_id']) ? intval($input['product_id']) : 0;
    $jobId = isset($input['job_id']) ? intval($input['job_id']) : 0;

    if (!$jobId || !$product_id) {
        throw new Exception('Missing job_id or product_id');
    }

    $pdo->beginTransaction();

    // 1. Get current job status with FOR UPDATE lock
    $stmt = $pdo->prepare("
        SELECT quantity, actual, status 
        FROM jobs 
        WHERE job_id = :job_id 
        AND product_id = :product_id
        FOR UPDATE
    ");
    $stmt->execute([':job_id' => $jobId, ':product_id' => $product_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        $pdo->rollBack();
        $response['message'] = 'Job not found';
        echo json_encode($response);
        exit;
    }

    // Check if already completed
    if ($job['status'] === 'completed') {
        $pdo->rollBack();
        $response['status'] = 'success';
        $response['message'] = 'Job already completed';
        $response['completed'] = true;
        echo json_encode($response);
        exit;
    }

    // 2. Update job actual count
    $updateJob = $pdo->prepare("
        UPDATE jobs 
        SET actual = actual + 1 
        WHERE job_id = :job_id
        AND actual < quantity
    ");
    $updateJob->execute([':job_id' => $jobId]);

    // 3. Update material stock
    $updateMaterials = $pdo->prepare("
        UPDATE production_stock ps
        JOIN bom_items bi ON ps.material_id = bi.material_id
        JOIN bom_headers bh ON bi.bom_id = bh.bom_id
        SET ps.availability_length = ps.availability_length - bi.quantity
        WHERE bh.product_id = :product_id
        AND ps.availability_length >= bi.quantity
    ");
    $updateMaterials->execute([':product_id' => $product_id]);

    if ($updateMaterials->rowCount() === 0) {
        $pdo->rollBack();
        $response['message'] = 'Insufficient materials';
        echo json_encode($response);
        exit;
    }

    // 4. Check if job is now completed
    $newActual = $job['actual'] + 1;
    $completed = $newActual >= $job['quantity'];
    
    if ($completed) {
        $completeJob = $pdo->prepare("
            UPDATE jobs 
            SET status = 'completed', 
                completed_at = NOW() 
            WHERE job_id = :job_id
        ");
        $completeJob->execute([':job_id' => $jobId]);
    }

    $pdo->commit();

    $response = [
        'status' => 'success',
        'message' => 'Production updated',
        'completed' => $completed
    ];

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['message'] = 'Database error';
    $response['debug'] = $e->getMessage();
    http_response_code(500);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['message'] = $e->getMessage();
    $response['debug'] = $e->getTraceAsString();
    http_response_code(400);
}

echo json_encode($response);
?>