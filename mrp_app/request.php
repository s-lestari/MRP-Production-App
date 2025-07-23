<?php
// Koneksi ke database
include 'api/config.php';

// Tambahkan data ke inventory jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $material_id = $_POST['material_id'];
    $request_amount = $_POST['request_amount'];
    
    // Update database untuk mengurangi stok di inventory
    $sql = "UPDATE inventory SET availability_length = availability_length - $request_amount WHERE material_id = '$material_id'";
    if ($conn->query($sql) === TRUE) {
        // Update juga ke tabel production_stock (menambahkan stok)
        $updateProduction = "UPDATE production_stock 
                             SET availability_length = availability_length + $request_amount 
                             WHERE material_id = '$material_id'";
        $conn->query($updateProduction);

        // Redirect ke inventory.php setelah sukses
        header("Location: inventory.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
}
}

// Ambil data dari tabel inventory untuk ditampilkan
if (isset($_GET['material_id'])) {
    $material_id = $_GET['material_id'];
    $sql = "SELECT i.*, m.material_name, m.type, u.uom_name 
            FROM inventory i
            JOIN material m ON i.material_id = m.material_id
            JOIN uom u ON i.uom_id = u.uom_id
            WHERE i.material_id = '$material_id'";
    $result = $conn->query($sql);
    $material = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Material</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <!-- Sidebar -->
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>MRP App</h2>
            </div>
            <nav class="sidebar-menu">
                <ul class="upper">
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
                    <li><a href="production.php"><i class="fas fa-industry"></i> Production</a></li>
                    <li><a href="capacity_planning.php"><i class="fas fa-cogs"></i> Machine Capacity</a></li>
                    <li><a href="bill_of_material.php"><i class="fas fa-list"></i> Bill of Material</a></li>
                </ul>

                <ul class="lower">
                    <li><a href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="dashboard-header">
                <h1>Request Material</h1>
                <div class="header-right">
                    <i class="fas fa-bell notification-icon"></i>
                    <img src="images/profile.png" alt="Profile Picture" class="profile-pic">
                </div>
            </header>

            <!-- Popup Section -->
            <div class="popup" id="requestPopup">
    <h2>Material Details</h2>
    <p>Material Name: <?php echo $material['material_name']; ?></p>
    <p>Material ID: <?php echo $material['material_id']; ?></p>
    <p>Type: <?php echo $material['type']; ?></p>
    <p>Last Order Date: <?php echo $material['last_order_date']; ?></p>
    <h2>Request Detail</h2>
    <p>Availability Stock (in <?php echo $material['uom_name']; ?>): <?php echo $material['availability_length']; ?> <?php echo $material['uom_name']; ?></p>
    <form method="POST" action="request.php">
        <input type="hidden" name="material_id" value="<?php echo $material['material_id']; ?>">
        <label for="request_amount">Request Amount (in <?php echo $material['uom_name']; ?>):</label>
        <input type="number" id="request_amount" name="request_amount" required>
        <button type="submit">Request</button>
    </form><br>
    <!-- Back Button -->
    <button onclick="window.location.href='inventory.php';">Back to Inventory</button>
</div>
        </main>
    </div>
</body>

</html>