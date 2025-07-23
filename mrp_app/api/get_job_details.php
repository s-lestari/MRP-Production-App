<?php
require_once 'config2.php';

$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    $sql = "
        SELECT 
            j.job_id,
            j.product_id,
            p.product_name,
            j.quantity,
            j.deadline,
            j.status,
            j.created_at,
            j.started_at,
            j.completed_at,
            j.actual,
            j.barcode,
            pl.id             AS log_id,
            pl.employee,
            pl.production_date,
            m.machine_id,
            m.machine_name,
            s.id             AS shift_id,
            s.shift_name,
            l.id             AS line_id,
            l.line_name
        FROM jobs j
        JOIN products p
          ON j.product_id = p.product_id
        LEFT JOIN production_logs pl
          ON j.job_id = pl.job_id
        LEFT JOIN machine m
          ON pl.machine_id = m.machine_id
        LEFT JOIN shifts s
          ON pl.shift_id = s.id
        LEFT JOIN production_lines l
          ON pl.line_id = l.id
        WHERE j.job_id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika tidak ada job sama sekali
    if (!$job) {
        echo json_encode([
            'status' => 'error',
            'message' => "Job with ID $job_id not found"
        ]);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'job'    => $job
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to fetch job details: ' . $e->getMessage()
    ]);
}
