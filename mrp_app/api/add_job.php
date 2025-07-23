<?php
require_once 'config2.php';

// Ambil data dari input
$data = json_decode(file_get_contents('php://input'), true);

// Validasi data yang diperlukan
if (!isset($data['product_id']) || !isset($data['quantity']) || !isset($data['deadline'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$product_id = $data['product_id'];
$quantity = $data['quantity'];
$deadline = $data['deadline'];
$status = 'Pending';
$production_date = date('Y-m-d H:i:s'); // Untuk production_logs

try {
    // Mulai transaksi
    $pdo->beginTransaction();

    // 1. Insert ke tabel jobs
    $stmt = $pdo->prepare("INSERT INTO jobs 
                          (product_id, quantity, deadline, status) 
                          VALUES (?, ?, ?, ?)");
    $stmt->execute([$product_id, $quantity, $deadline, $status]);
    $job_id = $pdo->lastInsertId();

    // 2. Insert ke tabel production_logs
    $stmt_logs = $pdo->prepare("INSERT INTO production_logs 
        (product_id, employee, machine_id, shift_id, line_id, production_date, job_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_logs->execute([
        $product_id,
        '-',      // employee (sementara, wajib tidak NULL)
        0,        // machine_id
        0,        // shift_id
        0,        // line_id
        $production_date,
        $job_id
    ]);



    // Commit transaksi jika semua berhasil
    $pdo->commit();

    // Response sukses
    echo json_encode([
        'status' => 'success',
        'message' => 'Job created successfully in both tables',
        'job_id' => $job_id,
        'job_data' => [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'deadline' => $deadline,
            'status' => $status
        ]
    ]);
} catch (PDOException $e) {
    // Rollback jika ada error
    $pdo->rollBack();
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Failed to create job: ' . $e->getMessage()
    ]);
}
?>