<?php
header('Content-Type: application/json');
require_once 'config.php';

$response = [
    'success' => false,
    'message' => ''
];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = isset($data['product_id']) ? intval($data['product_id']) : 0;

    if ($product_id <= 0) {
        throw new Exception('Invalid product ID');
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("DELETE FROM production_logs WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM jobs WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $stmt->close();

        // 2. Dapatkan bom_id terkait produk ini
        $stmt = $conn->prepare("SELECT bom_id FROM bom_headers WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bom = $result->fetch_assoc();
        $stmt->close();

        if ($bom) {
            $bom_id = $bom['bom_id'];

            // 3. Hapus product_colors terlebih dahulu
            $stmt = $conn->prepare("DELETE FROM product_colors WHERE product_id = ?");
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $stmt->close();

            // 4. Hapus bom_items
            $stmt = $conn->prepare("DELETE FROM bom_items WHERE bom_id = ?");
            $stmt->bind_param('i', $bom_id);
            $stmt->execute();
            $stmt->close();

            // 5. Hapus bom_headers
            $stmt = $conn->prepare("DELETE FROM bom_headers WHERE bom_id = ?");
            $stmt->bind_param('i', $bom_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Jika tidak ada BOM, tetap hapus product_colors
            $stmt = $conn->prepare("DELETE FROM product_colors WHERE product_id = ?");
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $stmt->close();
        }

        // 6. Hapus product
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $stmt->close();

        // Commit transaksi jika semua berhasil
        $conn->commit();
        $response['success'] = true;
        $response['message'] = 'Product and all related data deleted successfully';

    } catch (Exception $e) {
        // Rollback jika ada error
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>