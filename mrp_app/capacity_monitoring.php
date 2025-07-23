<?php
include 'api/config.php'; // pastikan koneksi ke database

// Pagination setup
$limit = 4; // banyak data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Ambil total data
$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM machine");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data mesin sesuai page
$query = "SELECT * FROM machine LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Capacity Monitoring</title>
    <link rel="stylesheet" href="css/style.css" />
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
                        <a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a>
                    </li>
                    <li>
                        <a href="production.php"><i class="fas fa-industry"></i> Production</a>
                    </li>
                    <li>
                        <a href="capacity_planning.php"><i class="fas fa-cogs"></i> Machine Capacity</a>
                    </li>
                    <li>
                        <a href="capacity_monitoring.php"><i class="fas fa-chart-line"></i> Capacity Monitoring</a>
                    </li>
                    <li>
                        <a href="bill_of_material.php"><i class="fas fa-list"></i> Bill of Material</a>
                    </li>
                </ul>

                <ul class="lower">
                    <li>
                        <a href="setting.html"><i class="fas fa-cog"></i> Settings</a>
                    </li>
                    <li>
                        <a href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <!-- Main dashboard -->
            <header class="dashboard-header">
                <h1>Capacity Monitoring</h1>
                <div class="search-box">
                    <input type="text" placeholder="Search..." />
                </div>

                <div class="logo">
                    <img src="images/logo.png" alt="Logo" />
                </div>

                <div class="header-right">
                    <i class="fas fa-bell notification-icon"></i>
                    <img src="images/profile.png" alt="Profile Picture" class="profile-pic" />
                </div>
            </header>

            <section class="monitoring-overview">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <?php
          $machine = $row['machine_name'];
          $speed = $row['speed_m_per_min'];
          $output = $speed * 60;
          $labels = $row['labels_per_meter'];
          $theoretical = $output * $labels;
          $actual = $row['actual_labels_per_hour'];
        //   $capacity_hour = $avg_speed * 60;
        //   $capacity_shift = $capacity_hour * 8;
          $utilization = $row['utilization']; // pastikan ini dihitung di database atau ambil
          
          // Status color
          if ($utilization >= 90) {
            $status = "Overload";
            $status_class = "red";
          } elseif ($utilization >= 80) {
            $status = "Close to limit";
            $status_class = "yellow";
          } else {
            $status = "Normal";
            $status_class = "green";
          }
        ?>

                <div class="card">
                    <div>
                        <span class="label machine">Machine Model</span><br>
                        <span class="value"><?php echo $machine; ?></span>
                    </div>
                    <div>
                        <span class="label speed"> Speed (m/min)</span><br>
                        <span class="value"><?php echo $speed; ?></span>
                    </div>
                    <div>
                        <span class="label output">Output (m/h)</span><br>
                        <span class="value"><?php echo number_format($output, 0, ',', '.'); ?></span>
                    </div>
                    <div>
                        <span class="label labels">Labels per Meter</span><br>
                        <span class="value"><?php echo $labels; ?></span>
                    </div>
                    <div>
                        <span class="label theoritical">Theoretical Labels/Hour</span><br>
                        <span class="value"><?php echo number_format($theoretical, 0, ',', '.'); ?></span>
                    </div>
                    <div>
                        <span class="label actual">Actual Labels/Hour</span><br>
                        <span class="value"><?php echo number_format($actual, 0, ',', '.'); ?></span>
                    </div>
                    <div>
                        <span class="label utilization">Utilization</span><br>
                        <span class="value"><?php echo $utilization; ?>%</span>
                    </div>
                    <div>
                        <span class="label status">Status</span><br>
                        <span class="status <?php echo $status_class; ?> value"><?php echo $status; ?></span>
                    </div>
                </div>

                <?php endwhile; ?>
            </section>

            <!-- Pagination Navigation -->
            <div class="pagination">
                <?php if($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">&laquo; Prev</a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php if($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <style>
    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .pagination a {
        display: inline-block;
        padding: 8px 8px;
        margin: 0 2px;
        border-radius: 5px;
        color: #007bff;
        text-decoration: none;
        border-radius: 5px;
        color: darkgray;
        cursor: pointer;

    }

    .pagination a.active {
        background-color: darkgray;
        color: white;
        font-weight: bold;
    }
    </style>

</body>

</html>