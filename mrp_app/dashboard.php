<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "mrp";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query untuk inventory summary
    $inventoryQuery = $conn->query("SELECT 
        COUNT(DISTINCT id) as total_products,
        SUM(availability_length) as total_quantity 
        FROM inventory");
    $inventoryData = $inventoryQuery->fetch(PDO::FETCH_ASSOC);
    
    // Query untuk low stock
    $lowStockQuery = $conn->query("SELECT 
    m.material_name as product_name, 
    i.availability_length as quantity, 
    uom.uom_name as uom
FROM inventory i
JOIN material m ON i.material_id = m.material_id
JOIN uom ON m.uom = uom.uom_id
WHERE i.availability_length < 10
ORDER BY i.availability_length DESC");

    // Fetch all low stock items
$lowStockItems = $lowStockQuery->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..."
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
      
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
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
                    <li><a href="production.php"><i class="fas fa-industry"></i> Production</a></li>
                    <li><a href="capacity_planning.php"><i class="fas fa-cogs"></i> Machine Capacity</a></li>
                    <li><a href="bill_of_material.php"><i class="fas fa-list"></i> Bill of Material</a></li>
                </ul>
                  
                <ul class="lower">
                    
                    <li><a href="index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
    
        <main class="main-content">
            <!-- Main dashboard -->
            <header class="dashboard-header">
                <h1>Dashboard</h1>
                <div class="search-box">
                    <input type="text" placeholder="Search...">
                </div>
            
                <div class="logo">
                    <img src="images/logo.png" alt="Logo">
                </div>
            
                <div class="header-right">
                    <i class="fas fa-bell notification-icon"></i>
                    <img src="images/profile.png" alt="Profile Picture" class="profile-pic">
                </div>
            </header>
            
            <section class="dashboard-overview">
                <div class="dashboard-wrapper">
                    <div class="summary-box">
                      <div class="summary-card">
                        <h3>Inventory Summary</h3>
                        <div class="summary-entry">
                        <div class="summary-value"><?php echo $inventoryData['total_products'] ?? 0; ?></div>
                          <div class="summary-label">Total Products</div>
                        </div>
                        <div class="summary-entry">
                        <div class="summary-value"><?php echo $inventoryData['total_quantity'] ?? 0; ?></div>
                          <div class="summary-label">Total Quantity</div>
                        </div>
                      </div>
                  
                      <!-- <div class="summary-card">
                        <h3>Product Summary</h3>
                        <div class="summary-entry">
                          <div class="summary-value">0</div>
                          <div class="summary-label">Product To Do</div>
                        </div>
                        <div class="summary-entry">
                          <div class="summary-value">0</div>
                          <div class="summary-label">Machine</div>
                        </div>
                      </div>
                    </div>
                  </div> -->
                  
                  <div class="low-stock-wrapper">
                    <h3>Low Quantity Stock</h3>
                    <table class="summary-table">
                      <thead>
                        <tr>
                          <th>Product Name</th>
                          <th>Quantity</th>
                          <th>UOM</th>
                        </tr>
                      </thead>
                      <tbody>
                                <?php if (!empty($lowStockItems)): ?>
                                    <?php foreach ($lowStockItems as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td class="<?php echo ($item['quantity'] < 10) ? 'text-danger' : ''; ?>">
                                                <?php echo $item['quantity']; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['uom']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">No low stock items</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                    </table>
                </div>
              </div>
                  
              </section>
        </main>
    </div>
    
</html>