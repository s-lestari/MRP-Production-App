<?php
include 'config.php';
header('Content-Type: application/json');

$query = "SELECT material_id, material_name, type FROM material";
$result = mysqli_query($conn, $query);

$type1 = [];
$type2 = [];

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['type'] == 1) {
        $type1[] = $row;
    } elseif ($row['type'] == 2) {
        $type2[] = $row;
    }
}

$response = [
    'type1' => $type1,
    'type2' => $type2
];

echo json_encode($response);
?>
