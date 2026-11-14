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
  <title>NekoMori Collectibles — New Order</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .order-layout {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 24px;
      align-items: start;
    }

    .order-card {
      background: var(--white);
      border: 1.5px solid var(--border);
      border-radius: var(--radius);
      padding: 28px;
      box-shadow: var(--shadow-sm);
    }

    .order-card h2 {
      font-family: 'DM Serif Display', serif;
      font-size: 17px;
      font-weight: 400;
      color: var(--text-dark);
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 16px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .form-group.full { grid-column: 1 / -1; }

    .form-group label {
      font-size: 12px;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    .form-group input,
    .form-group select {
      padding: 10px 14px;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      color: var(--text-dark);
      outline: none;
      background: #fafcf8;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus {
      border-color: var(--green-accent);
      box-shadow: 0 0 0 3px rgba(122,158,106,0.12);
    }

    .add-row {
      display: flex;
      gap: 10px;
      align-items: flex-end;
      margin-bottom: 20px;
    }

    .add-row .form-group { flex: 1; }
    .add-row .form-group.qty { max-width: 100px; }

    /* Items table */
    .items-table-wrap {
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      overflow: hidden;
      margin-bottom: 8px;
    }

    .items-table-wrap table thead th { font-size: 11px; padding: 10px 12px; }
    .items-table-wrap table tbody td { padding: 9px 12px; font-size: 13px; }

    .remove-btn {
      background: none;
      border: none;
      color: #e74c3c;
      cursor: pointer;
      font-size: 16px;
      padding: 2px 6px;
      border-radius: 4px;
      transition: background 0.15s;
    }
    .remove-btn:hover { background: #fdecea; }

    .empty-items {
      text-align: center;
      color: var(--text-muted);
      font-size: 13px;
      padding: 24px;
    }

    /* Summary card */
    .summary-line {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid #f0f0f0;
      font-size: 14px;
    }

    .summary-line:last-of-type { border-bottom: none; }
    .summary-line .label { color: var(--text-muted); }
    .summary-line .value { font-weight: 500; color: var(--text-dark); }

    .summary-total {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 0 0;
      margin-top: 4px;
      border-top: 2px solid var(--border);
    }

    .summary-total .label {
      font-size: 15px;
      font-weight: 600;
      color: var(--text-dark);
    }

    .summary-total .value {
      font-family: 'DM Serif Display', serif;
      font-size: 22px;
      color: var(--green-dark);
    }

    .discount-input {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .discount-input input {
      width: 100px;
      padding: 6px 10px;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      font-family: 'DM Sans', sans-serif;
      font-size: 13px;
      outline: none;
      text-align: right;
    }

    .discount-input input:focus { border-color: var(--green-accent); }

    .mop-select {
      padding: 6px 10px;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      font-family: 'DM Sans', sans-serif;
      font-size: 13px;
      color: var(--text-dark);
      background: #fafcf8;
      outline: none;
    }
    .mop-select:focus { border-color: var(--green-accent); }

    .btn-complete {
      width: 100%;
      margin-top: 20px;
      padding: 14px;
      background: var(--green-btn);
      color: var(--white);
      border: none;
      border-radius: var(--radius-sm);
      font-family: 'DM Sans', sans-serif;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      box-shadow: 0 4px 14px rgba(78,124,63,0.25);
    }

    .btn-complete:hover { background: var(--green-btn-hover); transform: translateY(-1px); }
    .btn-complete:disabled { background: #b0c8a0; cursor: not-allowed; transform: none; }
  </style>
</head>
<body>
  <nav class="sidebar" id="sidebar"></nav>

  <main class="main">
    <div class="page-header">
      <div class="page-title">
        <svg viewBox="0 0 24 24" fill="#4a6e3a" width="32" height="32"><path d="M19 6H17.82C17.4 4.84 16.3 4 15 4H9C7.7 4 6.6 4.84 6.18 6H5C3.9 6 3 6.9 3 8V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V8C21 6.9 20.1 6 19 6ZM9 6H15V7H9V6ZM12 17L7 12H10V9H14V12H17L12 17Z"/></svg>
        <h1>New Order</h1>
      </div>
      <div class="search-wrap">
        <input type="text" id="productSearch" placeholder="Search product..." oninput="filterProducts()">
        <button><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></button>
      </div>
    </div>

    <div class="order-layout">
      <!-- Left: Order form -->
      <div class="order-card">
        <h2>Order Details</h2>

        <div class="form-row">
          <div class="form-group full">
            <label>Customer Name (optional)</label>
            <input type="text" id="customerName" placeholder="e.g. Juan dela Cruz">
          </div>
        </div>

        <div class="add-row">
          <div class="form-group">
            <label>Select Product</label>
            <select id="productSelect">
              <option value="">— Choose a product —</option>
               <?php
               include "config.php";
               $result = $conn->query("SELECT * FROM products WHERE quantity > 0 ORDER BY product_name ASC");
               while ($row = $result->fetch_assoc()) {
               echo "<option value='{$row['product_id']}' 
               data-price='{$row['selling_price']}' 
               data-stock='{$row['quantity']}'>"
              . htmlspecialchars($row['product_name']) . " — ₱" . number_format($row['selling_price'], 2) .
               "</option>";
             }
             ?>
            </select>
          </div>
          <div class="form-group qty">
            <label>Qty</label>
            <input type="number" id="qtyInput" min="1" value="1" placeholder="1">
          </div>
          <button class="btn btn-primary" onclick="addItem()">+ Add</button>
        </div>

        <!-- Items list -->
        <div class="items-table-wrap">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="itemsBody">
              <tr id="emptyRow">
                <td colspan="5" class="empty-items">No items added yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Right: Summary -->
      <div class="order-card">
        <h2>Order Summary</h2>

        <div class="summary-line">
          <span class="label">Subtotal</span>
          <span class="value" id="subtotalDisplay">₱0.00</span>
        </div>

        <div class="summary-line">
          <span class="label">Discount</span>
          <div class="discount-input">
            <span>₱</span>
            <input type="number" id="discountInput" min="0" value="0" placeholder="0.00" oninput="computeTotal()">
          </div>
        </div>

        <div class="summary-line">
          <span class="label">Payment Method</span>
          <select id="mopSelect" class="mop-select">
            <option value="Cash">Cash</option>
            <option value="GCash">GCash</option>
            <option value="Maya">Maya</option>
            <option value="Card">Credit/Debit Card</option>
            <option value="Bank Transfer">Bank Transfer</option>
          </select>
        </div>

        <div class="summary-total">
          <span class="label">Total</span>
          <span class="value" id="totalDisplay">₱0.00</span>
        </div>

        <button class="btn-complete" id="completeBtn" onclick="completeTransaction()" disabled>
          Complete Transaction
        </button>
      </div>
    </div>
  </main>

  <script src="sidebar.js"></script>
  <script>
    renderSidebar('new_order', '<?php echo $_SESSION["username"]; ?>');

    let items = [];

    function filterProducts() {
      const q = document.getElementById('productSearch').value.toLowerCase();
      const opts = document.getElementById('productSelect').options;
      for (let i = 1; i < opts.length; i++) {
        opts[i].style.display = opts[i].text.toLowerCase().includes(q) ? '' : 'none';
      }
    }
          //add item to order
          //validates the quantity and stock 
    function addItem() {
      const select = document.getElementById('productSelect');
      const qty = parseInt(document.getElementById('qtyInput').value) || 1;
      const opt = select.options[select.selectedIndex];

      if (!select.value) { alert('Please select a product.'); return; }

      const price = parseFloat(opt.dataset.price);
      const stock = parseInt(opt.dataset.stock);
      const id = select.value;
      const name = opt.text.split(' — ')[0];

      if (qty < 1) { alert('Quantity must be at least 1.'); return; }
      if (qty > stock) { alert(`Only ${stock} in stock!`); return; }

      const existing = items.find(i => i.id === id);
      if (existing) {
        existing.qty += qty;
        existing.subtotal = existing.qty * existing.price;
      } else {
        items.push({ id, name, qty, price, subtotal: qty * price });
      }

      renderItems();
      computeTotal();
      select.value = '';
      document.getElementById('qtyInput').value = 1;
    }
       //for removing item from order
    function removeItem(id) {
      items = items.filter(i => i.id !== id);
      renderItems();
      computeTotal();
    }
       //cart render function
    function renderItems() {
      const body = document.getElementById('itemsBody');
      if (items.length === 0) {
        body.innerHTML = `<tr id="emptyRow"><td colspan="5" class="empty-items">No items added yet.</td></tr>`;
        document.getElementById('completeBtn').disabled = true;
        return;
      }

      body.innerHTML = items.map(item => `
        <tr>
          <td>${item.name}</td>
          <td>${item.qty}</td>
          <td>₱${item.price.toFixed(2)}</td>
          <td>₱${item.subtotal.toFixed(2)}</td>
          <td><button class="remove-btn" onclick="removeItem('${item.id}')">✕</button></td>
        </tr>
      `).join('');

      document.getElementById('completeBtn').disabled = false;
    }
        //computes the total with or without discount
    function computeTotal() {
      const subtotal = items.reduce((sum, i) => sum + i.subtotal, 0);
      const discount = parseFloat(document.getElementById('discountInput').value) || 0;
      const total = Math.max(0, subtotal - discount);

      document.getElementById('subtotalDisplay').textContent = `₱${subtotal.toFixed(2)}`;
      document.getElementById('totalDisplay').textContent = `₱${total.toFixed(2)}`;
    }
  // complete transaction prompt//
  function completeTransaction() {
  if (items.length === 0) { alert('No items in order.'); return; }

  const subtotal = items.reduce((sum, i) => sum + i.subtotal, 0);
  const discount = parseFloat(document.getElementById('discountInput').value) || 0;
  const total    = Math.max(0, subtotal - discount);

  const formData = new FormData();
  formData.append("customer_name", document.getElementById('customerName').value || 'Walk-in');
  formData.append("subtotal",      subtotal.toFixed(2));
  formData.append("discount",      discount.toFixed(2));
  formData.append("total_amount",  total.toFixed(2));
  formData.append("mop",           document.getElementById('mopSelect').value);
  formData.append("items",         JSON.stringify(items));

  fetch("api/complete-transaction.php", { method: "POST", body: formData })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Go to the receipt page; it opens the print dialog automatically
        window.location.href = "receipt.php?id=" + data.transaction_id + "&print=1";
      } else {
        alert("Error: " + data.message);
      }
    });
}
  </script>
</body>
</html>
