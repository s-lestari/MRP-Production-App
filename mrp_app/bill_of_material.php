<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bill Of Material</title>
<link rel="stylesheet" href="css/style-bill-of-material.css">
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
<h1>Bill of Material</h1>
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
<div class="content-bom">
<h2>Bill of Material</h2>
<button class="add-product-button">Add Product</button>
<!-- Popup Add Product -->
<div id="addProductModal" class="modal" style="display:none;">
<div class="modal-content">
<h2>Add Product</h2>
<form id="addProductForm">
<input type="hidden" name="product_id" id="productId">
<label>Product:</label>
<input type="text" name="product_name" id="productName" required><br>

<label>Version:</label>
<input type="text" name="version" id="version" required><br>

<label>Choose Materials (Type 1):</label>
<select id="materialType1" name="materials_type1[]" multiple required></select><br>

<label>Choose Materials (Type 2 - for Colors):</label>
<div id="materialType2"></div><br>

<label>Number of Colors:</label>
<input type="number" name="number_of_colors" id="numberOfColors" readonly><br>

<button type="submit" id="saveButton">Save</button>
<button type="button" onclick="closeModal()">Cancel</button>
</form>
</div>
</div>

<div class="product-list">
<!-- Product items will be loaded here dynamically -->
</div>
</div>  
</div>    
</section>
</main>
</div>

<script>
    document.querySelector('.add-product-button').addEventListener('click', function() {
        document.getElementById('addProductForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('saveButton').textContent = 'Save';
        openModal();
    });

    function openModal() {
        document.getElementById('addProductModal').style.display = 'block';
        loadMaterials();
    }

    function closeModal() {
        document.getElementById('addProductModal').style.display = 'none';
    }

    // Load material options from API
    function loadMaterials() {
    return fetch('api/getMaterials.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const type1Select = document.getElementById('materialType1');
            const type2Container = document.getElementById('materialType2');
            
            type1Select.innerHTML = '';
            type2Container.innerHTML = '';
            
            data.type1.forEach(material => {
                const option = document.createElement('option');
                option.value = material.material_id;
                option.textContent = material.material_name;
                type1Select.appendChild(option);
            });
            
            data.type2.forEach(material => {
                const div = document.createElement('div');
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = material.material_id;
                checkbox.name = 'materials_type2[]';
                checkbox.id = 'color_' + material.material_id;
                
                const label = document.createElement('label');
                label.htmlFor = 'color_' + material.material_id;
                label.appendChild(checkbox);
                label.appendChild(document.createTextNode(' ' + material.material_name));
                
                div.appendChild(label);
                type2Container.appendChild(div);
            });
        })
        .catch(error => {
            console.error('Error fetching materials:', error);
            alert('Failed to load materials.');
            throw error;
        });
}

    // Update number of colors dynamically
    document.getElementById('materialType2').addEventListener('change', function() {
        const selectedColors = document.querySelectorAll('#materialType2 input[type="checkbox"]:checked').length;
        document.getElementById('numberOfColors').value = selectedColors;
    });

    // Form submission handler
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const productId = document.getElementById('productId').value;
        if (productId) {
            updateProduct(productId);
        } else {
            createProduct();
        }
    });

    // Create new Product and BoM
    function createProduct() {
        const formData = new FormData(document.getElementById('addProductForm'));
        
        const type1Select = document.getElementById('materialType1');
        const selectedType1 = Array.from(type1Select.selectedOptions).map(option => option.value);
        formData.delete('materials_type1[]');
        selectedType1.forEach(value => formData.append('materials_type1[]', value));
        
        const selectedType2 = Array.from(document.querySelectorAll('#materialType2 input[type="checkbox"]:checked'))
            .map(checkbox => checkbox.value);
        formData.delete('materials_type2[]');
        selectedType2.forEach(value => formData.append('materials_type2[]', value));
        
        fetch('api/addBoMAndProduct.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Product and BoM added successfully!');
                closeModal();
                fetchBoM();
            } else {
                alert('Error: ' + (result.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add product and BoM.');
        });
    }

    // Read (fetch and display all products)
    function fetchBoM() {
    fetch('api/getBoMAndProduct.php')
    .then(response => response.json())
    .then(result => {
        const productList = document.querySelector('.product-list');
        productList.innerHTML = '';

        if (result.success && result.data && result.data.length > 0) {
            result.data.forEach(product => {
                const version = product.version || 'Not specified';
                const materials = product.materials_type1;
                const colors = product.materials_type2 === 'No colors' 
                    ? 0 
                    : product.materials_type2.split(', ').length;

                const productItem = document.createElement('div');
                productItem.className = 'product-item';
                productItem.innerHTML = `
                    <div class="product-name">${product.product_name}</div>
                    <table class="product-details">
                        <tr><th>Product</th><td>${product.product_name}</td></tr>
                        <tr><th>Version</th><td>${version}</td></tr>
                        <tr><th>Materials</th><td>${materials}</td></tr>
                        <tr><th>Colors</th><td>${product.materials_type2}</td></tr>
                        <tr><th>Number of Colors</th><td>${colors}</td></tr>
                    </table>
                    <div class="action-buttons">
                        <button class="edit-button" data-id="${product.product_id}">Edit</button>
                        <button class="delete-button" data-id="${product.product_id}">Delete</button>
                    </div>
                `;
                productList.appendChild(productItem);
            });
            attachActionButtons();
        } else {
            productList.innerHTML = '<p class="no-data">No products found</p>';
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        document.querySelector('.product-list').innerHTML = 
            '<p class="error">Error loading products</p>';
    });
}

    // Attach Edit and Delete button events
    function attachActionButtons() {
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this product?')) {
                    deleteProduct(productId);
                }
            });
        });
        
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                editProduct(productId);
            });
        });
    }

// Delete product - Fixed version
function deleteProduct(productId) {
    
    fetch('api/deleteBoMAndProduct.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            alert('Product and its BOM data deleted successfully!');
            fetchBoM(); // Refresh the product list
        } else {
            throw new Error(result.message || 'Failed to delete product');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting product: ' + error.message);
    });
}

// Edit product
function editProduct(productId) {
    fetch(`api/getBoMAndProduct.php?product_id=${productId}`)
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(response => {
        if (!response.success || !response.data) {
            throw new Error(response.message || 'Product not found');
        }

        const productData = response.data;
        
        // Isi form dengan data produk
        document.getElementById('productId').value = productData.product.product_id;
        document.getElementById('productName').value = productData.product.product_name;
        document.getElementById('version').value = productData.version || '';

        // Load materials dan set yang terpilih
        loadMaterials().then(() => {
            // Set material type 1 (bahan utama)
            const type1Select = document.getElementById('materialType1');
            if (productData.materials_type1 && productData.materials_type1.length > 0) {
                const selectedIds = productData.materials_type1.map(m => m.material_id.toString());
                Array.from(type1Select.options).forEach(option => {
                    option.selected = selectedIds.includes(option.value);
                });
            }

            // Set material type 2 (warna)
            const type2Container = document.getElementById('materialType2');
            if (productData.materials_type2 && productData.materials_type2.length > 0) {
                const selectedIds = productData.materials_type2.map(m => m.material_id.toString());
                const checkboxes = type2Container.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectedIds.includes(checkbox.value);
                });
                document.getElementById('numberOfColors').value = productData.materials_type2.length;
            }

            document.getElementById('saveButton').textContent = 'Update';
            openModal();
        });
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load product data: ' + error.message);
    });
}

// Update product and BoM
function updateProduct(productId) {
    const formData = new FormData(document.getElementById('addProductForm'));
    
    // Handle materials type 1
    const type1Select = document.getElementById('materialType1');
    const selectedType1 = Array.from(type1Select.selectedOptions).map(option => option.value);
    formData.delete('materials_type1[]');
    selectedType1.forEach(value => formData.append('materials_type1[]', value));
    
    // Handle materials type 2
    const selectedType2 = Array.from(document.querySelectorAll('#materialType2 input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.value);
    formData.delete('materials_type2[]');
    selectedType2.forEach(value => formData.append('materials_type2[]', value));
    
    // Tambahkan product_id ke formData
    formData.append('product_id', productId);

    fetch('api/updateBoMAndProduct.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(result => {
        if (result.success) {
            alert('Product updated successfully!');
            closeModal();
            fetchBoM();
        } else {
            throw new Error(result.message || 'Failed to update product');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating product: ' + error.message);
    });
}

    // Initial fetch on page load
    document.addEventListener('DOMContentLoaded', fetchBoM);
</script>
</body>
</html>