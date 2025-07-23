<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Production Monitoring</title>
  <link rel="stylesheet" href="css/style-run-production.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <h1>Production</h1>
        <div class="search-box"><input type="text" placeholder="Search..." /></div>
        <div class="logo"><img src="images/logo.png" alt="Logo" /></div>
        <div class="header-right">
          <i class="fas fa-bell notification-icon"></i>
          <img src="images/profile.png" alt="Profile" class="profile-pic" />
        </div>
      </header>

      <section>
        <div class="container">
          <div class="product-header">
            <div class="tab"></div>
            <div class="product-info">Material: </div>
            <button id="stopProductionBtn" class="button-stop">Stop Production</button>
          </div>

          <div class="grid">
            <div class="target-actual">
              <div class="row">
                <label>Target</label>
                <span>
                  <div data-target="0"></div>
                  <div data-target="0"></div>
                  <div data-target="0"></div>
                  <div data-target="0"></div>
                  <div data-target="0"></div>
                  <div data-target="0"></div>
                  <div data-target="0"></div>
                  <div data-target="0"></div>
                </span>
              </div>
              <div class="row">
                <label>Actual</label>
                <span>
                  <div data-actual="0"></div>
                  <div data-actual="0"></div>
                  <div data-actual="0"></div>
                  <div data-actual="0"></div>
                  <div data-actual="0"></div>
                  <div data-actual="0"></div>
                  <div data-actual="0"></div>
                  <div data-actual="0"></div>
                </span>
              </div>
            </div>

            <div class="grid">
              <div class="box">
                <div>Actual</div>
                <div class="actual-count-per-job" style="font-size: 24px;">0</div>
              </div>
              <div class="box">
                <div>Capacity</div>
                <div class="capacity-count" style="font-size: 24px;">0</div>
              </div>
            </div>
          </div>

          <div class="barcode">
            <label>Barcode</label>
            <input type="text" id="barcodeInput" placeholder="Input Barcode" />
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      fetchJobData();
      setupBarcodeScanner();
    });

    document.getElementById("stopProductionBtn").addEventListener("click", () => {
      Swal.fire({
        title: 'Yakin?',
        text: 'Produksi akan dihentikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hentikan!'
      }).then(result => {
        if (result.isConfirmed) {
          window.location.href = "production.php";
        }
      });
    });

    function fetchJobData() {
      const tab = document.querySelector('.product-header .tab');
      tab.textContent = 'Loading job data...';

      fetch('api/get_jobs.php')
        .then(res => res.ok ? res.json() : Promise.reject('Failed to load'))
        .then(data => updateProductionUI(data))
        .catch(err => {
          console.error(err);
          Swal.fire('Error', 'Failed to load job data', 'error');
          tab.textContent = 'Error loading job data';
        });
    }

    function updateProductionUI(jobs) {
      const jobId = new URLSearchParams(window.location.search).get('job_id') || JSON.parse(sessionStorage.getItem('currentProduction'))?.jobId;
      if (!jobId) return;

      const job = jobs.find(j => j.job_id == jobId);
      if (!job) return;

      document.querySelector('.product-header .tab').textContent = `Job ID: ${job.job_id} - ${job.product_name}`;
      document.querySelector('.product-info').innerHTML = `Material: ${job.materials_required || 'N/A'}`;

      const targets = document.querySelectorAll('[data-target]');
      const total = job.quantity;
      const perSegment = Math.ceil(total / targets.length);
      targets.forEach((el, i) => {
        const val = Math.min(perSegment, total - (i * perSegment));
        el.textContent = val > 0 ? val : 0;
      });

      sessionStorage.setItem('currentJobData', JSON.stringify(job));
    }

    function setupBarcodeScanner() {
      const input = document.getElementById('barcodeInput');
      input.focus();

      input.addEventListener('keypress', e => {
        if (e.key === 'Enter') {
          e.preventDefault();
          const barcode = input.value.trim();
          if (barcode) {
            processBarcode(barcode);
            input.value = '';
          }
        }
      });
    }

    function processBarcode(barcode) {
        const job = JSON.parse(sessionStorage.getItem('currentJobData'));
        if (!job) {
            Swal.fire('Error', 'No job data available', 'error');
            return;
        }

        fetch('api/update_jobs.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                job_id: job.job_id,
                product_id: job.product_id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Get current actual count from the display (not from dataset)
                const actualEl = document.querySelector('.actual-count-per-job');
                const currentActual = parseInt(actualEl.textContent) || 0;
                const newActual = currentActual + 1;
                
                // Update the total counter
                actualEl.textContent = newActual;
                
                // Update progress bars - use the actual DOM elements
                const actualDivs = document.querySelectorAll('[data-actual]');
                let remaining = newActual;
                
                // First reset all to 0
                actualDivs.forEach(el => el.textContent = '0');
                
                // Then fill up to current actual
                const targetDivs = document.querySelectorAll('[data-target]');
                targetDivs.forEach((targetEl, index) => {
                    const targetValue = parseInt(targetEl.textContent) || 0;
                    if (remaining > 0) {
                        const actualValue = Math.min(targetValue, remaining);
                        actualDivs[index].textContent = actualValue;
                        remaining -= actualValue;
                    }
                });

                if (data.completed) {
                    Swal.fire('Completed!', 'Production target reached!', 'success');
                }
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
                Swal.fire('Error', 'Failed to update production', 'error');
            });
    }


</script>
</body>
</html>
