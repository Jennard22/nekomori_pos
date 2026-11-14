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
  <title>NekoMori Collectibles — Home</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="sidebar" id="sidebar"></nav>

  <main class="main">
    <div class="page-header">
      <div class="page-title">
        <svg viewBox="0 0 24 24" fill="#4a6e3a" width="32" height="32"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <h1>Home</h1>
      </div>
    </div>

    <div class="shortcut-grid">
      <a href="new_order.php" class="shortcut-card">
        <div class="shortcut-icon">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 6H17.82C17.4 4.84 16.3 4 15 4H9C7.7 4 6.6 4.84 6.18 6H5C3.9 6 3 6.9 3 8V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V8C21 6.9 20.1 6 19 6ZM9 6H15V7H9V6ZM12 17L7 12H10V9H14V12H17L12 17Z"/></svg>
        </div>
        <span class="shortcut-label">New Order</span>
      </a>

      <a href="transaction.php" class="shortcut-card">
        <div class="shortcut-icon">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        </div>
        <span class="shortcut-label">Transaction</span>
      </a>

      <a href="inventory.php" class="shortcut-card">
        <div class="shortcut-icon">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zm0 12H4V9h16v10zM4 5h16V3H4v2z"/></svg>
        </div>
        <span class="shortcut-label">Inventory</span>
      </a>
    </div>
  </main>

 <script src="sidebar.js"></script>
<script>
   renderSidebar('home', '<?php echo $_SESSION["username"]; ?>');
</script>
</body>
</html>
