<?php
require_once 'config2.php';

$data = json_decode(file_get_contents('php://input'), true);

$job_id     = (int)$data['job_id'];
$employee   = $data['employee_id'];  // nanti dimasukkan ke kolom `employee`
$machine_id = (int)$data['machine_id'];
$line_id    = (int)$data['line_id'];
$shift_id   = (int)$data['shift_id'];
$started_at = date('Y-m-d H:i:s');

try {
    $pdo->beginTransaction();

    // 1) Update production_logs: pakai kolom `employee` (bukan employee_id)
    //    dan filter WHERE job_id = ?
    $stmt = $pdo->prepare("
        UPDATE production_logs
        SET
            employee        = ?,
            machine_id      = ?,
            shift_id        = ?,
            line_id         = ?,
            production_date = ?
        WHERE job_id = ?
    ");
    $stmt->execute([
        $employee,
        $machine_id,
        $shift_id,
        $line_id,
        $started_at,
        $job_id
    ]);

    // 2) Update status job jadi In Progress
    $up = $pdo->prepare("UPDATE jobs SET status = 'In Progress' , started_at = NOW() WHERE job_id = ?");
    $up->execute([$job_id]);

    $pdo->commit();

    echo json_encode([
        'status'  => 'success',
        'message' => 'Production started successfully',
        'job_id'  => $job_id
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to start production: ' . $e->getMessage()
    ]);
}
