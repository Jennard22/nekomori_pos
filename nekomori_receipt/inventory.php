<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NekoMori Collectibles — Inventory</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="sidebar" id="sidebar"></nav>

  <main class="main">
    <div class="page-header">
      <div class="page-title">
        <svg viewBox="0 0 24 24" fill="#4a6e3a" width="32" height="32"><path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zm0 12H4V9h16v10zM4 5h16V3H4v2z"/></svg>
        <h1>Inventory</h1>
      </div>
      <div class="search-wrap">
        <input type="text" placeholder="Search product or category..." oninput="searchTable(this.value)">
        <button><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></button>
      </div>
    </div>

    <div class="toolbar">
      <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='flex'">
        <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
          <path d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
        </svg>
        Add Product
      </button>
    </div>

    <!-- Add Product Modal -->
    <div id="addModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
      <div style="background:white; padding:2rem; border-radius:12px; width:400px;">
        <h2 style="margin-bottom:1rem;">Add New Product</h2>
        <input type="text"   id="product_name"  placeholder="Product Name *"  style="width:100%;margin-bottom:0.5rem;padding:0.5rem;"><br>
        <input type="text"   id="description"   placeholder="Description"      style="width:100%;margin-bottom:0.5rem;padding:0.5rem;"><br>
        <input type="text"   id="category"      placeholder="Category"         style="width:100%;margin-bottom:0.5rem;padding:0.5rem;"><br>
        <input type="number" id="quantity"      placeholder="Quantity *"       style="width:100%;margin-bottom:0.5rem;padding:0.5rem;"><br>
        <input type="number" id="unit_price"    placeholder="Unit Price *"     style="width:100%;margin-bottom:0.5rem;padding:0.5rem;" step="0.01"><br>
        <input type="number" id="selling_price" placeholder="Selling Price *"  style="width:100%;margin-bottom:1rem;padding:0.5rem;" step="0.01"><br>
        <div style="display:flex; gap:0.5rem;">
          <button class="btn btn-primary" onclick="submitAddProduct()">Add</button>
          <button class="btn btn-outline" onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
        </div>
        <p id="addMessage" style="margin-top:0.5rem; color:green;"></p>
      </div>
    </div>

    <!-- Edit Product Modal -->
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
  <div style="background:white; padding:2rem; border-radius:12px; width:400px;">
    <h2 style="margin-bottom:1rem;">Edit Product</h2>
    <input type="hidden" id="edit_product_id">
    <input type="text"   id="edit_product_name"  placeholder="Product Name *"  style="width:100%;margin-bottom:0.5rem;padding:0.5rem;"><br>
    <input type="text"   id="edit_description"   placeholder="Description"      style="width:100%;margin-bottom:0.5rem;padding:0.5rem;"><br>
    <input type="text"   id="edit_category"      placeholder="Category"         style="width:100%;margin-bottom:0.5rem;padding:0.5rem;"><br>
    <input type="number" id="edit_quantity"      placeholder="Quantity *"       style="width:100%;margin-bottom:0.5rem;padding:0.5rem;"><br>
    <input type="number" id="edit_unit_price"    placeholder="Unit Price *"     style="width:100%;margin-bottom:0.5rem;padding:0.5rem;" step="0.01"><br>
    <input type="number" id="edit_selling_price" placeholder="Selling Price *"  style="width:100%;margin-bottom:1rem;padding:0.5rem;" step="0.01"><br>
    <div style="display:flex; gap:0.5rem;">
      <button class="btn btn-primary" onclick="submitEditProduct()">Update</button>
      <button class="btn btn-outline" onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
    </div>
    <p id="editMessage" style="margin-top:0.5rem; color:green;"></p>
  </div>
</div>

    <div class="table-card">
      <table id="inventoryTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Description</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Selling Price</th>
            <th>Date Added</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          include "config.php";

          $result = $conn->query("SELECT * FROM products ORDER BY date_added DESC LIMIT 2000");

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $category = htmlspecialchars($row['category']);
              $badgeClass = "badge-accessories";
              if (stripos($category, "pokemon") !== false || stripos($category, "tcg") !== false)
                $badgeClass = "badge-tcg";
              elseif (stripos($category, "sealed") !== false)
                $badgeClass = "badge-sealed";
              elseif (stripos($category, "toy") !== false)
                $badgeClass = "badge-toys";

              echo "
              <tr>
                <td>{$row['product_id']}</td>
                <td>" . htmlspecialchars($row['product_name']) . "</td>
                <td><span class='badge {$badgeClass}'>{$category}</span></td>
                <td>" . htmlspecialchars($row['description']) . "</td>
                <td>{$row['quantity']}</td>
                <td>₱" . number_format($row['unit_price'], 2) . "</td>
                <td>₱" . number_format($row['selling_price'], 2) . "</td>
                <td>{$row['date_added']}</td>
                <td>
                  <div class='action-group'>
                    <button class='btn btn-outline btn-sm' onclick='editProduct({$row['product_id']})'>Edit</button>
                    <button class='btn btn-danger btn-sm' onclick='deleteProduct({$row['product_id']})'>Delete</button>
                  </div>
                </td>
              </tr>";
            }
          } else {
            echo "<tr><td colspan='9' style='text-align:center'>No products found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </main>

  <script src="sidebar.js"></script>
  <script src="paginate.js"></script>

  <script>
    renderSidebar('inventory', '<?php echo $_SESSION["username"]; ?>');

    const pager = initPager('inventoryTable', 50);
    function searchTable(query) { pager.search(query); }
            //add product function

        
    function submitAddProduct() {
      const formData = new FormData();
      formData.append("product_name",  document.getElementById("product_name").value);
      formData.append("description",   document.getElementById("description").value);
      formData.append("category",      document.getElementById("category").value);
      formData.append("quantity",      document.getElementById("quantity").value);
      formData.append("unit_price",    document.getElementById("unit_price").value);
      formData.append("selling_price", document.getElementById("selling_price").value);

      fetch("api/add-product.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
          document.getElementById("addMessage").textContent = data.message;
          if (data.success) setTimeout(() => location.reload(), 1000);
        });
    }

    function deleteProduct(id) {
      if (!confirm("Are you sure you want to delete this product?")) return;

      const formData = new FormData();
      formData.append("product_id", id);

      fetch("api/delete-product.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          if (data.success) location.reload();
        });
    }

    function editProduct(id) {
  // Fetch current product data
  fetch('api/get-product.php?id=' + id)
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const p = data.product;
        document.getElementById('edit_product_id').value    = p.product_id;
        document.getElementById('edit_product_name').value  = p.product_name;
        document.getElementById('edit_description').value   = p.description;
        document.getElementById('edit_category').value      = p.category;
        document.getElementById('edit_quantity').value      = p.quantity;
        document.getElementById('edit_unit_price').value    = p.unit_price;
        document.getElementById('edit_selling_price').value = p.selling_price;

        document.getElementById('editModal').style.display = 'flex';
      } else {
        alert('Failed to load product.');
      }
    });
}

function submitEditProduct() {
  const formData = new FormData();
  formData.append("product_id",    document.getElementById("edit_product_id").value);
  formData.append("product_name",  document.getElementById("edit_product_name").value);
  formData.append("description",   document.getElementById("edit_description").value);
  formData.append("category",      document.getElementById("edit_category").value);
  formData.append("quantity",      document.getElementById("edit_quantity").value);
  formData.append("unit_price",    document.getElementById("edit_unit_price").value);
  formData.append("selling_price", document.getElementById("edit_selling_price").value);

  fetch("api/update-product.php", { method: "POST", body: formData })
    .then(res => res.json())
    .then(data => {
      document.getElementById("editMessage").textContent = data.message;
      if (data.success) setTimeout(() => location.reload(), 1000);
    });
}
  </script>
</body>
</html>