<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
include "config.php";

$id = (int)($_GET['id'] ?? 0);
$autoPrint = isset($_GET['print']);

$stmt = $conn->prepare("SELECT t.*, e.first_name, e.last_name
                        FROM transactions t
                        LEFT JOIN employees e ON e.user_id = t.user_id
                        WHERE t.transaction_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();

if (!$t) {
    http_response_code(404);
    die("Receipt not found.");
}

$stmt = $conn->prepare("SELECT ti.quantity, ti.unit_price, ti.subtotal, p.product_name
                        FROM transaction_items ti
                        JOIN products p ON p.product_id = ti.product_id
                        WHERE ti.transaction_id = ?
                        ORDER BY ti.item_id");
$stmt->bind_param("i", $id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$receiptNo = str_pad($t['transaction_id'], 6, '0', STR_PAD_LEFT);
$cashier   = trim(($t['first_name'] ?? '') . ' ' . ($t['last_name'] ?? '')) ?: '—';
$peso = fn($n) => '₱' . number_format((float)$n, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt #<?= $receiptNo ?> — NekoMori Collectibles</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; background: #eef2ea; font-family: 'Courier New', monospace; color: #000; }
    .actions { display: flex; gap: 10px; justify-content: center; padding: 16px; }
    .actions button, .actions a {
      padding: 10px 18px; border: none; border-radius: 8px; cursor: pointer;
      font: 600 14px sans-serif; text-decoration: none; background: #4e7c3f; color: #fff;
    }
    .actions a.secondary { background: #fff; color: #4a6e3a; border: 1.5px solid #4a6e3a; }

    .receipt { width: 80mm; margin: 0 auto 24px; background: #fff; padding: 6mm 5mm; font-size: 12px; line-height: 1.4; box-shadow: 0 2px 10px rgba(0,0,0,.15); }
    .center { text-align: center; }
    .logo { width: 22mm; height: auto; }
    .shop { font-size: 16px; font-weight: bold; margin: 4px 0 0; }
    .rule { border: none; border-top: 1px dashed #000; margin: 8px 0; }
    .row { display: flex; justify-content: space-between; gap: 8px; }
    .item-name { font-weight: bold; word-break: break-word; }
    .item-detail { display: flex; justify-content: space-between; padding-left: 8px; }
    .total { font-size: 15px; font-weight: bold; }

    @media print {
      @page { size: 80mm auto; margin: 0; }
      body { background: #fff; }
      .actions { display: none; }
      .receipt { box-shadow: none; margin: 0; width: 80mm; }
    }
  </style>
</head>
<body>
  <div class="actions">
    <button onclick="window.print()">🖨 Print Receipt</button>
    <a class="secondary" href="new_order.php">New Order</a>
    <a class="secondary" href="transaction.php">Transactions</a>
  </div>

  <div class="receipt">
    <div class="center">
      <img class="logo" src="nekomori_logo.png" alt="">
      <p class="shop">NekoMori Collectibles</p>
      <div>Official Receipt</div>
    </div>
    <hr class="rule">
    <div class="row"><span>Receipt #</span><span><?= $receiptNo ?></span></div>
    <div class="row"><span>Date</span><span><?= htmlspecialchars(date('M d, Y h:i A', strtotime($t['date']))) ?></span></div>
    <div class="row"><span>Cashier</span><span><?= htmlspecialchars($cashier) ?></span></div>
    <div class="row"><span>Customer</span><span><?= htmlspecialchars($t['customer_name'] ?: 'Walk-in') ?></span></div>
    <hr class="rule">

    <?php foreach ($items as $it): ?>
      <div class="item-name"><?= htmlspecialchars($it['product_name']) ?></div>
      <div class="item-detail">
        <span><?= (int)$it['quantity'] ?> x <?= $peso($it['unit_price']) ?></span>
        <span><?= $peso($it['subtotal']) ?></span>
      </div>
    <?php endforeach; ?>

    <hr class="rule">
    <div class="row"><span>Subtotal</span><span><?= $peso($t['subtotal']) ?></span></div>
    <div class="row"><span>Discount</span><span>-<?= $peso($t['discount']) ?></span></div>
    <div class="row total"><span>TOTAL</span><span><?= $peso($t['total_amount']) ?></span></div>
    <div class="row"><span>Payment</span><span><?= htmlspecialchars($t['mop'] ?? 'Cash') ?></span></div>
    <hr class="rule">
    <div class="center">Thank you for shopping!<br>Please come again.</div>
  </div>

  <?php if ($autoPrint): ?>
  <script>
    window.addEventListener('load', () => setTimeout(() => window.print(), 300));
  </script>
  <?php endif; ?>
</body>
</html>
