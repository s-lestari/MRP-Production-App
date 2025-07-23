<?php
// api/getBoMAndProduct.php

header('Content-Type: application/json');
require_once 'config.php'; // pastikan ini menginisialisasi $conn sebagai MySQLi

$response = [
    'success' => false,
    'data'    => [],
    'message' => ''
];

try {
    $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

    if ($product_id) {
        // 1) Ambil data product spesifik
        $stmt = $conn->prepare("
            SELECT 
                p.product_id,
                p.product_name,
                b.version,
                b.bom_id
            FROM products p
            LEFT JOIN bom_headers b ON p.product_id = b.product_id
            WHERE p.product_id = ?
        ");
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            throw new Exception('Product not found');
        }
        $product = $res->fetch_assoc();
        $stmt->close();

        // 2) Ambil BOM items dengan material type 1 (bahan utama)
        $stmt = $conn->prepare("
            SELECT 
                bi.material_id,
                m.material_name,
                bi.quantity,
                bi.uom_id,
                m.type
            FROM bom_items bi
            JOIN material m ON bi.material_id = m.material_id
            WHERE bi.bom_id = ? AND m.type = 1
        ");
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
        $stmt->bind_param('i', $product['bom_id']);
        $stmt->execute();
        $bom_items_type1 = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // 3) Ambil BOM items dengan material type 2 (warna)
        $stmt = $conn->prepare("
            SELECT 
                bi.material_id,
                m.material_name,
                bi.quantity,
                bi.uom_id,
                m.type
            FROM bom_items bi
            JOIN material m ON bi.material_id = m.material_id
            WHERE bi.bom_id = ? AND m.type = 2
        ");
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
        $stmt->bind_param('i', $product['bom_id']);
        $stmt->execute();
        $bom_items_type2 = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $response['data'] = [
            'product' => $product,
            'version' => $product['version'],
            'materials_type1' => $bom_items_type1,
            'materials_type2' => $bom_items_type2
        ];

    } else {
        // 4) Ambil semua products dengan versi dan material (type 1 dan type 2)
        $res = $conn->query("
            SELECT 
                p.product_id, 
                p.product_name,
                b.version,
                b.bom_id,
                GROUP_CONCAT(DISTINCT IF(m.type = 1, m.material_name, NULL) SEPARATOR ', ') as materials_type1,
                GROUP_CONCAT(DISTINCT IF(m.type = 2, m.material_name, NULL) SEPARATOR ', ') as materials_type2,
                COUNT(DISTINCT IF(m.type = 2, m.material_id, NULL)) as color_count
            FROM products p
            LEFT JOIN bom_headers b ON p.product_id = b.product_id
            LEFT JOIN bom_items bi ON b.bom_id = bi.bom_id
            LEFT JOIN material m ON bi.material_id = m.material_id
            GROUP BY p.product_id, p.product_name, b.version, b.bom_id
        ");
        
        if (!$res) {
            throw new Exception("Error executing products query: " . $conn->error);
        }
        $products = $res->fetch_all(MYSQLI_ASSOC);

        // Bersihkan hasil GROUP_CONCAT yang mungkin berisi NULL
        foreach ($products as &$product) {
            $product['materials_type1'] = $product['materials_type1'] ?: 'No materials';
            $product['materials_type2'] = $product['materials_type2'] ?: 'No colors';
            $product['color_count'] = (int)$product['color_count'];
        }

        $response['data'] = $products;
    }

    $response['success'] = true;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
exit;
?>