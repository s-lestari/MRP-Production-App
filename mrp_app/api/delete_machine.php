<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $machine_id = $_POST['machine_id'];

    $sql = "DELETE FROM machine WHERE machine_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $machine_id);

    if ($stmt->execute()) {
        header("Location: ../capacity_planning.php"); // Redirect kembali ke halaman utama
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>