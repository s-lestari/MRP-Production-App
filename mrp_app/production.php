<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production</title>
    <link rel="stylesheet" href="css/style-production.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    integrity="sha512-..."
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
                    <li><a href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="main-content">
            <!-- Main dashboard -->
            <header class="dashboard-header">
                <h1>Production</h1>
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
                <div class="content-production">
                    <div>
                        <button class="add-button" id="add-btn">Add Job</button>
                    </div>
                    <div>
                        <button class="stock-button" id="stock-btn" onclick="window.location.href='production_stock.php'">Production Stock</button>
                    </div>
                    <div>
                        <button class="fg-button" id="fg-btn" onclick="window.location.href='finish_good.php'">Finish Good</button>
                    </div>
                    <div class="product-list" id="job-list">
                        <!-- Jobs will be loaded dynamically -->
                    </div>
                </div>  
            </section>
        </main>
    </div>
    
<!-- Add-Job Modal -->
<div class="modal" id="add-modal">
    <div class="modal-content">
        <button class="modal-close">&times;</button>
        <h2>Add Production Job</h2>
        <form id="add-form">
            <div class="form-group">
                <label for="job-product-select">Product</label>
                <select id="job-product-select" required>
                    <option value="">Select Product</option>
                    <!-- Products will be loaded dynamically -->
                </select>
            </div>
            
            <div class="form-group">
                <label for="job-quantity">Quantity</label>
                <input type="number" id="job-quantity" min="1" required>
            </div>

            <div class="form-group">
                <label for="job-deadline">Deadline</label>
                <input type="date" id="job-deadline" required>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="job-cancel-btn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="job-create-btn">Create Job</button>
            </div>
        </form>
    </div>
</div>

<!-- Start-Production Modal -->
<div class="modal" id="production-modal">
    <div class="modal-content">
        <button class="modal-close">&times;</button>
        <h2>Start Production</h2>
        <div id="material-check-result"></div>
        <form id="production-form">
            <input type="hidden" id="production-job-id">
            
            <div class="form-group">
                <label for="production-product">Product</label>
                <input type="text" id="production-product" readonly data-product-id="">
            </div>

            <div class="form-group">
                <label for="production-quantity">Quantity</label>
                <input type="number" id="production-quantity" readonly>
            </div>
            
            <div class="form-group">
                <label for="employee-id">Employee ID</label>
                <input type="text" id="employee-id" required>
            </div>
            
            <div class="form-group">
                <label for="machine-select">Machine</label>
                <select id="machine-select" required>
                    <option value="">Select Machine</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="line-select">Production Line</label>
                <select id="line-select" required>
                    <option value="">Select Line</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="shift-select">Shift</label>
                <select id="shift-select" required>
                    <option value="">Select Shift</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="production-cancel-btn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="start-production-btn">Start Production</button>
                <button type="button" class="btn btn-warning" id="request-materials-btn" style="display:none;">Request Materials</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load initial data
        loadProductionData();
        loadJobs();

        // Add Job Modal
        const addModal = document.getElementById('add-modal');
        const addBtn = document.getElementById('add-btn');
        const jobCancelBtn = document.getElementById('job-cancel-btn');

        addBtn.addEventListener('click', function() {
            addModal.style.display = 'block';
        });
        
        document.querySelector('#add-modal .modal-close').addEventListener('click', function() {
            addModal.style.display = 'none';
        });
        
        jobCancelBtn.addEventListener('click', function() {
            addModal.style.display = 'none';
        });
        
        document.getElementById('add-form').addEventListener('submit', function(e) {
            e.preventDefault();
            addJob();
        });

        // Production Modal
        const productionModal = document.getElementById('production-modal');
        const productionCancelBtn = document.getElementById('production-cancel-btn');
        
        document.querySelector('#production-modal .modal-close').addEventListener('click', function() {
            productionModal.style.display = 'none';
        });
        
        productionCancelBtn.addEventListener('click', function() {
            productionModal.style.display = 'none';
        });
        
        document.getElementById('production-form').addEventListener('submit', function(e) {
            e.preventDefault();
            startProduction();
        });
        
        document.getElementById('request-materials-btn').addEventListener('click', function() {
            requestMaterials();
        });
    });

    function loadProductionData() {
        // Fetch products
        fetch('api/get_products.php')
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    const select = document.getElementById('job-product-select');
                    select.innerHTML = '<option value="">Select Product</option>';
                    data.forEach(product => {
                        select.innerHTML += `<option value="${product.product_id}">${product.product_name}</option>`;
                    });
                } else {
                    console.error('Invalid product data:', data);
                }
            });

        // Fetch machines
        fetch('api/get_machines.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && Array.isArray(data.data)) {
                    const machines = data.data;
                    const addJobSelect = document.getElementById('add-job-machine');
                    const productionSelect = document.getElementById('machine-select');

                    if (addJobSelect) addJobSelect.innerHTML = '<option value="">Select Machine</option>';
                    if (productionSelect) productionSelect.innerHTML = '<option value="">Select Machine</option>';

                    machines.forEach(machine => {
                        const option = `<option value="${machine.machine_id}">${machine.machine_name}</option>`;
                        if (addJobSelect) addJobSelect.innerHTML += option;
                        if (productionSelect) productionSelect.innerHTML += option;
                    });
                } else {
                    console.error('Invalid machine data:', data);
                }
            })
            .catch(error => {
                console.error('Error fetching machines:', error);
            });

        // Fetch lines
        fetch('api/get_lines.php')
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    const select = document.getElementById('line-select');
                    select.innerHTML = '<option value="">Select Line</option>';
                    data.forEach(line => {
                        select.innerHTML += `<option value="${line.id}">${line.line_name}</option>`;
                    });
                } else {
                    console.error('Invalid line data:', data);
                }
            });

        // Fetch shifts
        fetch('api/get_shifts.php')
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    const select = document.getElementById('shift-select');
                    select.innerHTML = '<option value="">Select Shift</option>';
                    data.forEach(shift => {
                        select.innerHTML += `<option value="${shift.id}">${shift.shift_name}</option>`;
                    });
                } else {
                    console.error('Invalid shift data:', data);
                }
            });
    }

    function loadJobs() {
        fetch('api/get_jobs.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('job-list');
                container.innerHTML = '';
                
                if (data.length === 0) {
                    container.innerHTML = '<p>No production jobs found</p>';
                    return;
                }
                
                data.forEach(job => {
                    // Determine status class for color coding
                    const statusClass = 
                        job.status === 'Pending' ? 'status-pending' :
                        job.status === 'In Progress' ? 'status-in-progress' :
                        job.status === 'Completed' ? 'status-completed' :
                        job.status === 'Cancelled' ? 'status-cancelled' : '';
                    
                    // Determine which buttons to show
                    const showStartBtn = job.status === 'Pending' || job.status === 'In Progress';
                    const showCompleteBtn = job.status === 'In Progress';
                    const showCancelBtn = job.status !== 'Completed' && job.status !== 'Cancelled';
                    
                    container.innerHTML += `
                        <div class="product-item">
                            <div class="product-info">
                                <h3>${job.product_name}</h3>
                                <p>Job ID: ${job.job_id}</p>
                                <p>Quantity: ${job.quantity}</p>
                                <p>Materials Required: ${job.materials_required || 'N/A'}</p>
                                <p>Stock: ${job.min_availability || 'N/A'}</p>
                                
                                <p>Deadline: ${job.deadline}</p>
                                <p class="status ${statusClass}">Status: ${job.status}</p>
                            </div>
                            <div class="product-actions">
                                ${showStartBtn ? 
                                    `<button class="btn btn-primary start-job-btn" data-id="${job.job_id}">
                                        ${job.status === 'In Progress' ? 'Continue Production' : 'Start Production'}
                                    </button>` : ''}
                                ${showCompleteBtn ? 
                                    `<button class="btn btn-success complete-job-btn" data-id="${job.job_id}">
                                        Complete Job
                                    </button>` : ''}
                                ${showCancelBtn ? 
                                    `<button class="btn btn-danger cancel-job-btn" data-id="${job.job_id}">
                                        Cancel Job
                                    </button>` : ''}
                                <button class="btn btn-info view-job-btn" data-id="${job.job_id}">
                                    View Details
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                // Add event listeners to buttons
                document.querySelectorAll('.start-job-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const jobId = this.getAttribute('data-id');
                        openProductionModal(jobId);
                    });
                });
                
                document.querySelectorAll('.complete-job-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const jobId = this.getAttribute('data-id');
                        completeJob(jobId);
                    });
                });
            
                document.querySelectorAll('.cancel-job-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const jobId = this.getAttribute('data-id');
                        jobCancel(jobId);
                    });
                });
                
                document.querySelectorAll('.view-job-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const jobId = this.getAttribute('data-id');
                        viewJobDetails(jobId);
                    });
                });
            })
            .catch(error => {
                console.error('Error loading jobs:', error);
                const container = document.getElementById('job-list');
                container.innerHTML = '<p>Error loading jobs. Please try again.</p>';
            });
    }

    function addJob() {
        // Ambil nilai dari form
        const productId = document.getElementById('job-product-select').value;
        const quantity = document.getElementById('job-quantity').value;
        const deadline = document.getElementById('job-deadline').value;
        
        // Validasi form
        if (!productId || !quantity || !deadline) {
            Swal.fire({
                title: 'Error!',
                text: 'Please fill all required fields',
                icon: 'error'
            });
            return;
        }
        
        // Validasi tanggal deadline tidak boleh sebelum hari ini
        const today = new Date().toISOString().split('T')[0];
        if (deadline < today) {
            Swal.fire({
                title: 'Error!',
                text: 'Deadline cannot be before today',
                icon: 'error'
            });
            return;
        }
    
        // Kirim data ke server
        fetch('api/add_job.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity,
                deadline: deadline
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: 'Production job created successfully',
                    icon: 'success'
                }).then(() => {
                    // Tutup modal dan reset form
                    document.getElementById('add-modal').style.display = 'none';
                    document.getElementById('add-form').reset();
                    // Muat ulang daftar job
                    loadJobs();
                });
            } else {
                throw new Error(data.message || 'Failed to create job');
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error.message || 'An error occurred while creating the job',
                icon: 'error'
            });
        });
    }

    function openProductionModal(jobId) {
        fetch(`api/get_job_details.php?id=${jobId}`)
            .then(r => r.json())
            .then(data => {
            if (data.status === 'success') {
                const job = data.job;

                document.getElementById('production-job-id').value       = job.job_id;
                document.getElementById('production-product').value      = job.product_name;
                document.getElementById('production-product').dataset.productId = job.product_id;
                document.getElementById('production-quantity').value     = job.quantity;
                if (job.machine_id) {
                document.getElementById('machine-select').value        = job.machine_id;
                }

                checkMaterialAvailability(
                job.job_id,
                job.product_id,
                job.job_quantity
                );

                document.getElementById('production-modal').style.display = 'block';
            } else {
                Swal.fire('Error', data.message, 'error');
            }
            });
    }


    function checkMaterialAvailability(jobId, productId, quantity) {
        fetch('api/check_materials.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            const resultContainer = document.getElementById('material-check-result');
            const startBtn = document.getElementById('start-production-btn');
            const requestBtn = document.getElementById('request-materials-btn');
            
            if (data.status === 'success') {
                if (data.allMaterialsAvailable) {
                    resultContainer.innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> All materials are available for production
                        </div>
                    `;
                    startBtn.disabled = false;
                    requestBtn.style.display = 'none';
                } else {
                    resultContainer.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Insufficient materials: ${data.missingMaterials.join(', ')}
                        </div>
                    `;
                    startBtn.disabled = true;
                    requestBtn.style.display = 'block';
                }
            } else {
                resultContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> Error checking materials: ${data.message}
                    </div>
                `;
                startBtn.disabled = true;
                requestBtn.style.display = 'none';
            }
        });
    }

    function startProduction() {
        const jobId = document.getElementById('production-job-id').value;
        const employeeId = document.getElementById('employee-id').value.trim();
        const machineId = document.getElementById('machine-select').value;
        const lineId = document.getElementById('line-select').value;
        const shiftId = document.getElementById('shift-select').value;
        
        // Validasi form
        if (!employeeId || !machineId || !lineId || !shiftId) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill all required fields',
                icon: 'error'
            });
            return;
        }
        
        // Tampilkan loading
        const startBtn = document.getElementById('start-production-btn');
        const originalText = startBtn.innerHTML;
        startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting...';
        startBtn.disabled = true;

        // 1. Pertama, cek ketersediaan material
        fetch('api/check_materials.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: document.getElementById('production-product').dataset.productId,
                quantity: document.getElementById('production-quantity').value
            })
        })
        .then(response => {
        // First check if the response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                throw new Error(`Server returned non-JSON response: ${text.substring(0, 100)}...`);
            });
        }
        return response.json();
        })
        .then(materialData => {
            if (materialData.status !== 'success') {
                throw new Error(materialData.message || 'Failed to check materials');
            }
            
            // 2. Jika material tidak cukup, tampilkan pesan
            if (!materialData.allMaterialsAvailable) {
                const missingList = materialData.missingMaterials.join('\n• ');
                Swal.fire({
                    title: 'Insufficient Materials',
                    html: `The following materials are insufficient:<br><br>• ${missingList}`,
                    icon: 'warning',
                    confirmButtonText: 'Go to Inventory',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `inventory.php?job_id=${jobId}&missing_materials=1`;
                    }
                });
                return;
            }
            
            // 3. Jika material cukup, mulai produksi
            return fetch('api/start_production.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    job_id: jobId,
                    employee_id: employeeId,
                    machine_id: machineId,
                    line_id: lineId,
                    shift_id: shiftId
                })
            });
        })
        .then(response => {
            if (!response) return; // Skip jika sudah handle material tidak cukup
            return response.json();
        })
        .then(productionData => {
            if (productionData?.status === 'success') {
                // Simpan data produksi ke sessionStorage
                sessionStorage.setItem('currentProduction', JSON.stringify({
                    jobId: jobId,
                    employeeId: employeeId,
                    machineId: machineId,
                    lineId: lineId,
                    shiftId: shiftId,
                    startTime: new Date().toISOString()
                }));
                
                // Redirect ke halaman production
                window.location.href = `run_production.php?job_id=${jobId}`;
            } else if (productionData) {
                throw new Error(productionData.message || 'Failed to start production');
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error.message || 'An error occurred while starting production',
                icon: 'error'
            });
        })
        .finally(() => {
            // Reset button state
            startBtn.innerHTML = originalText;
            startBtn.disabled = false;
        });
    }

    function requestMaterials() {
        const jobId = document.getElementById('production-job-id').value;
        window.location.href = `inventory.php?job_id=${jobId}`;
    }

    function completeJob(jobId) {
        fetch('api/complete_job.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                job_id: jobId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: 'Job completed successfully',
                    icon: 'success'
                }).then(() => {
                    loadJobs();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to complete job',
                    icon: 'error'
                });
            }
        });
    }

    function viewJobDetails(jobId) {
        fetch(`api/get_job_details.php?id=${jobId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const job = data.job;
                    let html = `
                        <h3>${job.product_name}</h3>
                        <p><strong>Job ID:</strong> ${job.job_id}</p>
                        <p><strong>Quantity:</strong> ${job.quantity}</p>
                        <p><strong>Deadline:</strong> ${job.deadline || 'N/A'}</p>
                        <p><strong>Status:</strong> ${job.status || 'N/A'}</p>
                        <p><strong>Created At:</strong> ${job.created_at || 'N/A'}</p>
                    `;
                    
                    if (job.machine_name) {
                        html += `<p><strong>Machine:</strong> ${job.machine_name}</p>`;
                    }
                    
                    if (job.started_at) {
                        html += `<p><strong>Started At:</strong> ${job.started_at}</p>`;
                    }
                    
                    if (job.completed_at) {
                        html += `<p><strong>Completed At:</strong> ${job.completed_at}</p>`;
                    }
                    
                    if (job.line_name) {
                        html += `<p><strong>Production Line:</strong> ${job.line_name}</p>`;
                    }
                    
                    if (job.shift_name) {
                        html += `<p><strong>Shift:</strong> ${job.shift_name}</p>`;
                    }
                    
                    if (job.employee_id) {
                        html += `<p><strong>Operator:</strong> ${job.employee_id}</p>`;
                    }
                    
                    // Add materials list if available
                    if (data.materials && data.materials.length > 0) {
                        html += `<h4>Materials Required:</h4><ul>`;
                        data.materials.forEach(material => {
                            html += `<li>${material.material_name}: ${material.required_quantity} ${material.uom_name}</li>`;
                        });
                        html += `</ul>`;
                    }
                    
                    Swal.fire({
                        title: 'Job Details',
                        html: html,
                        icon: 'info',
                        width: '600px'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to load job details',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to load job details: ' + error.message,
                    icon: 'error'
                });
            });
    }

    function jobCancel(jobId) {
        fetch('api/cancel_job.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                job_id: jobId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: 'Job cancelled successfully',
                    icon: 'success'
                }).then(() => {
                    loadJobs();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to cancel job',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred: ' + error.message,
                icon: 'error'
            });
        });
    }
</script>
</body>
</html>