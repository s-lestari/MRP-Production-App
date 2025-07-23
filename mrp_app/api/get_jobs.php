<?php
require_once 'config2.php';

try {
    $stmt = $pdo->query("
        SELECT 
            j.job_id,  
            j.product_id, 
            p.product_name, 
            j.quantity,
            j.deadline, 
            j.status, 
            j.created_at,
            (
                SELECT GROUP_CONCAT(m.material_name SEPARATOR ', ')
                FROM bom_headers bh
                JOIN bom_items bi ON bh.bom_id = bi.bom_id
                JOIN material m ON bi.material_id = m.material_id
                WHERE bh.product_id = j.product_id
            ) AS materials_required,
            (
                SELECT MIN(ps.availability_length)
                FROM bom_headers bh
                JOIN bom_items bi ON bh.bom_id = bi.bom_id
                JOIN production_stock ps ON bi.material_id = ps.material_id
                WHERE bh.product_id = j.product_id
            ) AS min_availability
        FROM jobs j
        JOIN products p ON j.product_id = p.product_id
        LEFT JOIN production_logs pl ON j.job_id = pl.job_id
        ORDER BY j.created_at DESC;
    ");
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($jobs);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch jobs: ' . $e->getMessage()]);
}
?>