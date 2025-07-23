<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $machine = $_POST['machine_name'];
    $speed = $_POST['speed_m_per_min'];
    $labels = $_POST['labels_per_meter'];
    $actual = $_POST['actual_labels_per_hour'];
    
    // Calculate derived values
    $output = $speed * 60;
    $theoretical = $output * $labels;
    
    $sql = "INSERT INTO machine (machine_name, speed_m_per_min, output_m_per_hour, 
            labels_per_meter, theoretical_labels_per_hour, actual_labels_per_hour)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            speed_m_per_min = VALUES(speed_m_per_min), 
            output_m_per_hour = VALUES(output_m_per_hour), 
            labels_per_meter = VALUES(labels_per_meter), 
            theoretical_labels_per_hour = VALUES(theoretical_labels_per_hour), 
            actual_labels_per_hour = VALUES(actual_labels_per_hour)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiiii", $machine, $speed, $output, 
                     $labels, $theoretical, $actual);
    
    if ($stmt->execute()) {
        header("Location: ../capacity_planning.php");
    } else {
        header("Location: ../capacity_planning.php?error=1");
    }
    
    $stmt->close();
    $conn->close();
}
?>