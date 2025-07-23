<?php
include 'api/config.php';

$items_per_page = 4;

if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $current_page = $_GET['page'];
} else {
    $current_page = 1;
}

$offset = ($current_page - 1) * $items_per_page;

$sql_count = "SELECT COUNT(*) AS total FROM machine";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_items = $row_count['total'];
$total_pages = ceil($total_items / $items_per_page);

$sql = "SELECT * FROM machine LIMIT $items_per_page OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Machine Capacity</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
    /* Pagination CSS */
    .pagination {
        margin-top: 20px;
        text-align: center;
    }

    .pagination a {
        padding: 8px 8px;
        margin: 0 4px;
        text-decoration: none;
        background-color: #f1f1f1;
        border-radius: 5px;
        color: darkgray;
        cursor: pointer;
    }

    .pagination a.active {
        background-color: darkgray;
        color: white;
        font-weight: bold;
    }

    .pagination a:hover {
        background-color: #ddd;
    }
    </style>
</head>

<body>
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

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Machine Capacity</h1>
                <div class="search-box">
                    <input type="text" placeholder="Search..." id="searchInput">
                </div>
                <div class="logo">
                    <img src="images/logo.png" alt="Logo">
                </div>
                <div class="header-right">
                    <i class="fas fa-bell notification-icon"></i>
                    <img src="images/profile.png" alt="Profile Picture" class="profile-pic" />
                </div>
            </header>

            <section class="planning-overview">
                <button class="add-machine" id="openModal">Add Machine</button>
                <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="card-planning">
                    <div>
                        <span class="label machine">Machine Model</span>
                        <span class="value"><?= $row['machine_name'] ?></span>
                    </div>
                    <div>
                        <span class="label speed">Speed (m/min)</span>
                        <span class="value"><?= $row['speed_m_per_min'] ?></span>
                    </div>
                    <div>
                        <span class="label output">Output (m/hour)</span>
                        <span class="value"><?php echo number_format($row['output_m_per_hour'], 0, ',', '.'); ?></span>
                    </div>
                    <div>
                        <span class="label labels">Labels per Meter</span>
                        <span class="value"><?= $row['labels_per_meter'] ?></span>
                    </div>
                    <div>
                        <span class="label theoretical">Theoretical Labels/Hour</span>
                        <span
                            class="value"><?php echo number_format($row['theoretical_labels_per_hour'], 0, ',', '.'); ?></span>
                    </div>
                    <div>
                        <span class="label actual">Actual Labels/Hour</span>
                        <span
                            class="value"><?php echo number_format($row['actual_labels_per_hour'], 0, ',', '.'); ?></span>
                    </div>
                    <div>
                        <span class="label action">Action</span>
                        <form method="POST" action="api/delete_machine.php">
                            <input type="hidden" name="machine_id" value="<?= $row['machine_id'] ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php else: ?>
                <p>No machine data available.</p>
                <?php endif; ?>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?>" class="prev">Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= ($i == $current_page) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?= $current_page + 1 ?>" class="next">Next</a>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Add Machine -->
    <div id="machineModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add New Machine</h3>
            <form method="POST" action="api/process_add_machine.php">
                <div class="form-group">
                    <label for="machine_name">Machine Model</label>
                    <input type="text" id="machine_name" name="machine_name" required>
                </div>
                <div class="form-group">
                    <label for="speed_m_per_min">Speed (m/min)</label>
                    <input type="number" id="speed_m_per_min" name="speed_m_per_min" required>
                </div>
                <div class="form-group">
                    <label for="labels_per_meter">Labels per Meter</label>
                    <input type="number" id="labels_per_meter" name="labels_per_meter" required>
                </div>
                <div class="form-group">
                    <label for="actual_labels_per_hour">Actual Labels/Hour</label>
                    <input type="number" id="actual_labels_per_hour" name="actual_labels_per_hour" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-discard" id="discardBtn">Discard</button>
                    <button type="submit" class="btn btn-add">Add Machine</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Modal functionality
    var modal = document.getElementById("machineModal");
    var btn = document.getElementById("openModal");
    var span = document.getElementsByClassName("close")[0];
    var discard = document.getElementById("discardBtn");

    btn.onclick = function() {
        modal.style.display = "block";
    }
    span.onclick = function() {
        modal.style.display = "none";
    }
    discard.onclick = function() {
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const input = this.value.toLowerCase();
        const cards = document.querySelectorAll('.card');

        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(input) ? '' : 'none';
        });
    });
    </script>
</body>

</html>