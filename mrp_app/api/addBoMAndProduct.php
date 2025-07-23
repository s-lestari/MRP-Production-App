<?php
// api/addBoMAndProduct.php

header('Content-Type: application/json');
// aktifkan error reporting selama development
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';  // harus membuat $conn = new mysqli(...);

$response = ['success' => false, 'message' => ''];

try {
    // 0) Validasi input
    if (empty($_POST['product_name']) || empty($_POST['version'])) {
        throw new Exception('Product name and version are required');
    }

    // 1) Mulai transaction
    $conn->autocommit(false);

    // 2) Insert ke products (hanya product_name)
    $stmt = $conn->prepare("INSERT INTO products (product_name) VALUES (?)");
    if (!$stmt) {
        throw new Exception("Prepare products failed: " . $conn->error);
    }
    $stmt->bind_param('s', $_POST['product_name']);
    if (!$stmt->execute()) {
        throw new Exception("Execute products failed: " . $stmt->error);
    }
    $product_id = $conn->insert_id;
    $stmt->close();

    // 3) Insert ke bom_headers (product_id + version)
    $stmt = $conn->prepare("INSERT INTO bom_headers (product_id, version) VALUES (?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare bom_headers failed: " . $conn->error);
    }
    $stmt->bind_param('is', $product_id, $_POST['version']);
    if (!$stmt->execute()) {
        throw new Exception("Execute bom_headers failed: " . $stmt->error);
    }
    $bom_id = $conn->insert_id;
    $stmt->close();

    // 4) Insert materials type 1 ke bom_items
    if (!empty($_POST['materials_type1']) && is_array($_POST['materials_type1'])) {
        $stmt = $conn->prepare("
            INSERT INTO bom_items (bom_id, material_id, quantity, uom_id)
            VALUES (?, ?, 1, 1)
        ");
        if (!$stmt) {
            throw new Exception("Prepare bom_items type1 failed: " . $conn->error);
        }
        foreach ($_POST['materials_type1'] as $mid) {
            $mid = intval($mid);
            $stmt->bind_param('ii', $bom_id, $mid);
            if (!$stmt->execute()) {
                throw new Exception("Execute bom_items type1 failed: " . $stmt->error);
            }
        }
        $stmt->close();
    }

    // 5) Insert materials type 2 (colors) ke bom_items & product_colors
    if (!empty($_POST['materials_type2']) && is_array($_POST['materials_type2'])) {
        // Prepared statements
        $insItem = $conn->prepare("
            INSERT INTO bom_items (bom_id, material_id, quantity, uom_id)
            VALUES (?, ?, 1, 5)
        ");
        if (!$insItem) {
            throw new Exception("Prepare bom_items type2 failed: " . $conn->error);
        }
        $getMat = $conn->prepare("
            SELECT material_name
            FROM material
            WHERE material_id = ?
        ");
        if (!$getMat) {
            throw new Exception("Prepare get material failed: " . $conn->error);
        }
        $insCol = $conn->prepare("
            INSERT INTO product_colors (product_id, color_name)
            VALUES (?, ?)
        ");
        if (!$insCol) {
            throw new Exception("Prepare product_colors failed: " . $conn->error);
        }

        foreach ($_POST['materials_type2'] as $mid) {
            $mid = intval($mid);
            // a) bom_items
            $insItem->bind_param('ii', $bom_id, $mid);
            if (!$insItem->execute()) {
                throw new Exception("Execute bom_items type2 failed: " . $insItem->error);
            }
            // b) ambil nama warna
            $getMat->bind_param('i', $mid);
            $getMat->execute();
            $res = $getMat->get_result();
            $mat = $res->fetch_assoc();
            if (!$mat) {
                throw new Exception("Invalid material ID: $mid");
            }
            // c) product_colors
            $insCol->bind_param('is', $product_id, $mat['material_name']);
            if (!$insCol->execute()) {
                throw new Exception("Execute product_colors failed: " . $insCol->error);
            }
        }
        // close statements
        $insItem->close();
        $getMat->close();
        $insCol->close();
    }

    // 6) Commit
    if (! $conn->commit()) {
        throw new Exception("Commit failed: " . $conn->error);
    }

    // Balikkan autocommit
    $conn->autocommit(true);

    $response['success'] = true;
    $response['message'] = 'Product & BOM berhasil dibuat';

} catch (Exception $e) {
    // Rollback dan restore autocommit
    $conn->rollback();
    $conn->autocommit(true);

    $response['message'] = $e->getMessage();
}

// Output JSON
echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
exit;
