<?php
require_once 'config2.php'; // Pastikan koneksi database tersambung

// Ambil data JSON dari body request
$input = json_decode(file_get_contents('php://input'), true);

// Validasi input
if (!isset($input['job_id']) || !is_numeric($input['job_id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid job ID'
    ]);
    exit;
}

$job_id = intval($input['job_id']);

try {
    // Cek apakah job masih bisa dibatalkan (misalnya: belum selesai)
    $checkStmt = $pdo->prepare("SELECT status FROM jobs WHERE job_id = ?");
    $checkStmt->execute([$job_id]);
    $job = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Job not found'
        ]);
        exit;
    }

    if ($job['status'] === 'complete') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Cannot cancel a completed job'
        ]);
        exit;
    }

    // Update status ke 'cancelled'
    $updateStmt = $pdo->prepare("UPDATE jobs SET status = 'cancelled' WHERE job_id = ?");
    $updateStmt->execute([$job_id]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Job cancelled successfully'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
