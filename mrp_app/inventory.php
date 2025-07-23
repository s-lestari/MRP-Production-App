<?php
// Koneksi ke database
include 'api/config.php';

// Ambil data dari tabel inventory dengan join ke material, material_type, dan uom
$sql = "SELECT 
            i.id,
            m.material_name,
            mt.type_name,
            u.uom_name,
            i.availability_length,
            i.last_order_date
        FROM inventory i
        JOIN material m ON i.material_id = m.material_id
        JOIN material_type mt ON m.type = mt.type_id
        JOIN uom u ON i.uom_id = u.uom_id";
$result = $conn->query($sql);

// Hitung statistik
$total_items = $result->num_rows;
$in_stock = 0;
$low_stock = 0;

// Hitung stok
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Tentukan status berdasarkan availability_length
        if ($row['availability_length'] > 20) {
            $in_stock++;
        } else {
            $low_stock++;
        }
    }
    // Reset pointer result
    $result->data_seek(0);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventory</title>
    <link rel="stylesheet" href="css/style-inventory.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      integrity="sha512-..."
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <style>
      .instock {
        color: #4CAF50;
        font-weight: bold;
      }
      .lowstock {
        color: #FF9800;
        font-weight: bold;
      }
      .outofstock {
        color: #F44336;
        font-weight: bold;
      }
    </style>
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
            <img
              src="images/profile.png"
              alt="Profile Picture"
              class="profile-pic"
            />
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
          
              <div class="inventory-card">
                <span class="label availabilities">Availabilities</span><br />
                <div class="inventory-entry">
                  <div class="inventory-value"><?php echo $in_stock; ?></div>
                  <div class="inventory-label">In Stock</div>
                </div>
                <div class="inventory-entry">
                  <div class="inventory-value"><?php echo $low_stock; ?></div>
                  <div class="inventory-label">Low Stock</div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="material-list-wrapper">
            <h3>Material List</h3>
            
            <div class="table-container">
              <table class="inventory-table" id="inventoryTable">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Material Name</th>
                    <th>Availability</th>
                    <th>Type</th>
                    <th>Last Order Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if ($result->num_rows > 0) {
                      $counter = 1;
                      while($row = $result->fetch_assoc()) {
                          // Tentukan status dan kelas CSS berdasarkan availability_length
                          $availability_length = $row['availability_length'];
                          if ($availability_length > 20) {
                              $status = "In-stock";
                              $status_class = "instock";
                          } else if ($availability_length > 0) {
                              $status = "Low-stock";
                              $status_class = "lowstock";
                          } else {
                              $status = "Out-of-stock";
                              $status_class = "outofstock";
                          }
                          
                          echo "<tr>
                                  <td>" . $counter++ . "</td>
                                  <td><a href='request.php?material_id=" . $row['id'] . "'>" . $row['material_name'] . "</a></td>
                                  <td>" . $availability_length . " " . $row['uom_name'] . "</td>
                                  <td>" . $row['type_name'] . "</td>
                                  <td>" . $row['last_order_date'] . "</td>
                                  <td class='" . $status_class . "'>" . $status . "</td>
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

      // Simple pagination functionality
      document.querySelector('.previous-btn').addEventListener('click', function() {
        // Add your pagination logic here
        console.log('Previous page');
      });

      document.querySelector('.next-btn').addEventListener('click', function() {
        // Add your pagination logic here
        console.log('Next page');
      });
    </script>
  </body>
</html>