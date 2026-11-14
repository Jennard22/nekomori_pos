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
  <title>NekoMori Collectibles — Transaction</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="sidebar" id="sidebar"></nav>

  <main class="main">
    <div class="page-header">
      <div class="page-title">
        <svg viewBox="0 0 24 24" fill="#4a6e3a" width="32" height="32"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        <h1>Transaction</h1>
      </div>
      <div class="search-wrap">
        <input type="text" placeholder="Search by customer, ID or MOP..." oninput="searchTable(this.value)">
        <button><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></button>
      </div>
    </div>

    <?php
    include "config.php";
    $totalSales  = $conn->query("SELECT SUM(total_amount) AS total FROM transactions")->fetch_assoc();
    $todaySales  = $conn->query("SELECT SUM(total_amount) AS total FROM transactions WHERE DATE(date) = CURDATE()")->fetch_assoc();
    $totalOrders = $conn->query("SELECT COUNT(*) AS count FROM transactions")->fetch_assoc();
    $total = $totalSales['total']  ?? 0;
    $today = $todaySales['total']  ?? 0;
    $count = $totalOrders['count'] ?? 0;
    ?>

    <div class="toolbar" style="display:flex; align-items:center; justify-content:space-between;">
      <a href="new_order.php" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z"/></svg>
        New Order
      </a>

      <div style="display:flex; gap:12px; align-items:center;">
        <div style="background:white; border-radius:10px; padding:10px 20px; border:1.5px solid #dce8d4; text-align:center;">
          <p style="font-size:11px; color:#888; margin:0;">Today's Sales</p>
          <p style="font-size:15px; font-weight:700; color:#4a6e3a; margin:0;">₱<?= number_format($today, 2) ?></p>
        </div>
        <div style="background:white; border-radius:10px; padding:10px 20px; border:1.5px solid #dce8d4; text-align:center;">
          <p style="font-size:11px; color:#888; margin:0;">Total Orders</p>
          <p style="font-size:15px; font-weight:700; color:#4a6e3a; margin:0;"><?= $count ?></p>
        </div>
        <div style="background:white; border-radius:10px; padding:10px 20px; border:1.5px solid #dce8d4; text-align:center;">
          <p style="font-size:11px; color:#888; margin:0;">Total Sales</p>
          <p style="font-size:15px; font-weight:700; color:#4a6e3a; margin:0; font-family:'DM Serif Display', serif;">₱<?= number_format($total, 2) ?></p>
        </div>
      </div>
    </div>

    <div class="table-card">
      <table id="transactionTable">
        <thead>
          <tr>
            <th>Transaction ID</th>
            <th>Customer Name</th>
            <th>Subtotal</th>
            <th>Discount</th>
            <th>Total</th>
            <th>MOP</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = $conn->query("SELECT * FROM transactions ORDER BY date DESC LIMIT 2000");

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $customer = !empty($row['customer_name']) ? htmlspecialchars($row['customer_name']) : '—';
              $tid = str_pad($row['transaction_id'], 4, '0', STR_PAD_LEFT);

              echo "
              <tr>
                <td>#$tid</td>
                <td>$customer</td>
                <td>₱" . number_format($row['subtotal'], 2) . "</td>
                <td>₱" . number_format($row['discount'], 2) . "</td>
                <td>₱" . number_format($row['total_amount'], 2) . "</td>
                <td><span class='badge badge-mop'>" . htmlspecialchars($row['mop'] ?? 'Cash') . "</span></td>
                <td>{$row['date']}</td>
                <td>
                  <div class='action-group'>
                    <button class='btn btn-outline btn-sm' onclick='viewTransaction({$row['transaction_id']})'>View</button>
                    <a class='btn btn-outline btn-sm' href='receipt.php?id={$row['transaction_id']}' target='_blank'>Receipt</a>
                    <button class='btn btn-danger btn-sm' onclick='deleteTransaction({$row['transaction_id']})'>Delete</button>
                  </div>
                </td>
              </tr>";
            }
          } else {
            echo "<tr><td colspan='8' style='text-align:center'>No transactions yet.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- View Transaction Modal -->
    <div id="viewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
      <div style="background:white; padding:2rem; border-radius:12px; width:500px; max-height:80vh; overflow-y:auto;">
        <h2 style="margin-bottom:1rem;">Transaction Details</h2>
        <div id="viewModalContent">Loading...</div>
        <button class="btn btn-outline" style="margin-top:1rem;" onclick="document.getElementById('viewModal').style.display='none'">Close</button>
      </div>
    </div>

  </main>

  <script src="sidebar.js"></script>
  <script src="paginate.js"></script>
  <script>
    renderSidebar('transaction', '<?php echo $_SESSION["username"]; ?>');

    const pager = initPager('transactionTable', 50);
    function searchTable(query) { pager.search(query); }

    function viewTransaction(id) {
      document.getElementById('viewModal').style.display = 'flex';
      document.getElementById('viewModalContent').innerHTML = 'Loading...';

      fetch('api/get-transaction.php?id=' + id)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            let rows = data.items.map(item => `
              <tr>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                <td>₱${parseFloat(item.subtotal).toFixed(2)}</td>
              </tr>
            `).join('');

            document.getElementById('viewModalContent').innerHTML = `
              <table style="width:100%; border-collapse:collapse; margin-bottom:1rem;">
                <thead>
                  <tr style="border-bottom:1px solid #ddd;">
                    <th style="text-align:left; padding:8px;">Product</th>
                    <th style="text-align:left; padding:8px;">Qty</th>
                    <th style="text-align:left; padding:8px;">Unit Price</th>
                    <th style="text-align:left; padding:8px;">Subtotal</th>
                  </tr>
                </thead>
                <tbody>${rows}</tbody>
              </table>
              <p><strong>Subtotal:</strong> ₱${parseFloat(data.transaction.subtotal).toFixed(2)}</p>
              <p><strong>Discount:</strong> ₱${parseFloat(data.transaction.discount).toFixed(2)}</p>
              <p><strong>Total:</strong> ₱${parseFloat(data.transaction.total_amount).toFixed(2)}</p>
              <p><strong>Payment Method:</strong> ${data.transaction.mop || 'Cash'}</p>
              <a class="btn btn-primary" href="receipt.php?id=${id}" target="_blank">View / Print Receipt</a>
            `;
          } else {
            document.getElementById('viewModalContent').innerHTML = 'Failed to load.';
          }
        });
    }

    function deleteTransaction(id) {
      if (!confirm("Are you sure you want to delete this transaction?")) return;

      const formData = new FormData();
      formData.append("transaction_id", id);

      fetch("api/delete-transaction.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          if (data.success) location.reload();
        });
    }
  </script>
</body>
</html>