<?php
header('Content-Type: application/json');
require_once 'config.php';

// Validate input
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['job_id']) || !isset($_POST['status'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$jobId = $_POST['job_id'];
$status = $_POST['status'];

// Validate status
$allowedStatuses = ['stopped', 'completed', 'pending'];
if (!in_array($status, $allowedStatuses)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "UPDATE jobs SET status = :status";
    
    // If stopping or completing, set end date
    if ($status === 'stopped' || $status === 'completed') {
        $query .= ", end_date = NOW()";
    }
    
    $query .= " WHERE job_id = :job_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':job_id', $jobId);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Job status updated successfully'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>