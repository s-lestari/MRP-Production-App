<?php
// Koneksi ke database
include 'api/config.php';

// Ambil data dari tabel inventory dengan join ke material, material_type, dan uom
$sql = "SELECT 
            j.job_id,
            p.product_name,
            j.actual,
            j.completed_at
        FROM jobs j
        JOIN products p ON p.product_id = j.product_id";
$result = $conn->query($sql);

// Hitung statistik
$total_items = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventory</title>
    <link rel="stylesheet" href="css/style-finish-good.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <!-- side bar dashboard -->
            <div class="sidebar-header">
                <h2>MRP App</h2>
            </div>
            <nav class="sidebar-menu">
                <ul class="upper">
                    <li>
                        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="inventory.php" class="active"><i class="fas fa-boxes"></i> Inventory</a>
                    </li>
                    <li>
                        <a href="production.php"><i class="fas fa-industry"></i> Production</a>
                    </li>
                    <li>
                        <a href="capacity_planning.php"><i class="fas fa-cogs"></i> Machine Capacity</a>
                    </li>
                    
                    <li>
                        <a href="bill_of_material.php"><i class="fas fa-list"></i> Bill of Material</a>
                    </li>
                </ul>

                <ul class="lower">
                    
                    <li>
                        <a href="index.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Inventory</h1>
                <div class="search-box">
                    <input type="text" placeholder="Search..." id="searchInput" />
                </div>

                <div class="logo">
                    <img src="images/logo.png" alt="Logo" />
                </div>

                <div class="header-right">
                    <i class="fas fa-bell notification-icon"></i>
                    <img src="images/profile.png" alt="Profile Picture" class="profile-pic" />
                </div>
            </header>

            <section class="inventory-overview">
                <div class="inventory-wrapper">
                    <div class="inventory-box">
                        <div class="inventory-card">
                            <span class="label categories">Categories</span><br />
                            <div class="inventory-entry">
                                <div class="inventory-value"><?php echo $total_items; ?></div>
                                <div class="inventory-label">Total Items</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="material-list-wrapper">
                    <div>
                        <button class="production-button" id="stock-btn"
                            onclick="window.location.href='production.php'">Production</button>
                    </div>
                    <h3>Finish Good Product</h3>

                    <div class="table-container">
                        <table class="inventory-table" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Product Name</th>
                                    <th>Total Finish Good</th>
                                    <th>Batch</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
if ($result->num_rows > 0) {
    $counter = 1;
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $counter++ . "</td>
                <td>" . $row['product_name'] . "</td>
                <td>" . $row['actual'] . "</td>
                <td>" . $row['completed_at'] . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No data available</td></tr>";
}
$conn->close();
?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const input = this.value.toLowerCase();
        const rows = document.querySelectorAll('#inventoryTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    });

    // // Simple pagination functionality
    // document.querySelector('.previous-btn').addEventListener('click', function() {
    //     // Add your pagination logic here
    //     console.log('Previous page');
    // });

    // document.querySelector('.next-btn').addEventListener('click', function() {
    //     // Add your pagination logic here
    //     console.log('Next page');
    // });
    </script>
</body>

</html>