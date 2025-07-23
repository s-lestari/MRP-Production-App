<?php
header('Content-Type: application/json');
require_once 'config.php';

$response = [
    'success' => false,
    'message' => ''
];

try {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
    $version = isset($_POST['version']) ? trim($_POST['version']) : '';
    $materials_type1 = isset($_POST['materials_type1']) ? $_POST['materials_type1'] : [];
    $materials_type2 = isset($_POST['materials_type2']) ? $_POST['materials_type2'] : [];

    if ($product_id <= 0 || empty($product_name)) {
        throw new Exception('Invalid input data');
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // 1. Update product
        $stmt = $conn->prepare("UPDATE products SET product_name = ? WHERE product_id = ?");
        $stmt->bind_param('si', $product_name, $product_id);
        $stmt->execute();
        $stmt->close();

        // 2. Dapatkan bom_id
        $stmt = $conn->prepare("SELECT bom_id FROM bom_headers WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bom = $result->fetch_assoc();
        $stmt->close();

        if ($bom) {
            $bom_id = $bom['bom_id'];

            // 3. Update bom_header (version)
            $stmt = $conn->prepare("UPDATE bom_headers SET version = ? WHERE bom_id = ?");
            $stmt->bind_param('si', $version, $bom_id);
            $stmt->execute();
            $stmt->close();

            // 4. Hapus semua bom_items lama
            $stmt = $conn->prepare("DELETE FROM bom_items WHERE bom_id = ?");
            $stmt->bind_param('i', $bom_id);
            $stmt->execute();
            $stmt->close();

            // 5. Tambahkan bom_items baru (type 1)
            foreach ($materials_type1 as $material_id) {
                $stmt = $conn->prepare("INSERT INTO bom_items (bom_id, material_id, quantity, uom_id) VALUES (?, ?, 1, 1)");
                $stmt->bind_param('ii', $bom_id, $material_id);
                $stmt->execute();
                $stmt->close();
            }

            // 6. Tambahkan bom_items baru (type 2 - colors)
            foreach ($materials_type2 as $material_id) {
                $stmt = $conn->prepare("INSERT INTO bom_items (bom_id, material_id, quantity, uom_id) VALUES (?, ?, 1, 1)");
                $stmt->bind_param('ii', $bom_id, $material_id);
                $stmt->execute();
                $stmt->close();
            }

            // 7. Update product_colors (jika diperlukan)
            // ... tambahkan kode sesuai kebutuhan ...
        } else {
            throw new Exception('BOM header not found');
        }

        // Commit transaksi
        $conn->commit();
        $response['success'] = true;
        $response['message'] = 'Product updated successfully';

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>