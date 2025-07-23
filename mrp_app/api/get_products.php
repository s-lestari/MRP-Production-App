<?php
// Include database connection
require_once 'config2.php'; 

try {
    $stmt = $pdo->query("SELECT product_id, product_name FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($products);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch products: ' . $e->getMessage()]);
}
?>