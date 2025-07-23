<?php
require_once 'config2.php';

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];
$quantity   = $data['quantity'];

try {
    // Ambil BOM items + stok inventory
    $stmt = $pdo->prepare("
        SELECT 
            bi.material_id,
            m.material_name,
            bi.quantity       AS required_per_unit,            -- quantity per unit produk
            COALESCE(ps.availability_length, 0) AS available_quantity,
            u.uom_name
        FROM bom_headers bh
        JOIN bom_items bi   ON bh.bom_id = bi.bom_id
        JOIN material m     ON bi.material_id = m.material_id
        LEFT JOIN production_stock ps ON bi.material_id = ps.material_id
        LEFT JOIN uom u     ON bi.uom_id = u.uom_id
        WHERE bh.product_id = ?
    ");
    $stmt->execute([$product_id]);
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $allAvailable     = true;
    $missingMaterials = [];

    foreach ($materials as $mat) {
        // total required = per-unit * jumlah job
        $required  = $mat['required_per_unit'] * $quantity;
        $available = $mat['available_quantity'];

        if ($available < $required ) {
            $allAvailable = false;
            $need = $required - $available;
            $missingMaterials[] = "{$mat['material_name']} (Need: {$need} more {$mat['uom_name']})";
        } 
    }

    echo json_encode([
        'status'                => 'success',
        'allMaterialsAvailable' => $allAvailable,
        'missingMaterials'      => $missingMaterials
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to check materials: ' . $e->getMessage()
    ]);
}
