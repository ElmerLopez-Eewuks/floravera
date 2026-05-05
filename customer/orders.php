<?php
session_start();
if(!isset($_SESSION['user_id'])){header("Location: ../login.php");exit();}
include '../dbconnect.php';
$uid=$_SESSION['user_id'];

$orders=$conn->query("SELECT o.*,
    GROUP_CONCAT(p.emoji SEPARATOR '') AS emojis,
    GROUP_CONCAT(p.name SEPARATOR ', ') AS items
    FROM orders o
    JOIN order_items oi ON oi.order_id=o.id
    JOIN products p ON p.id=oi.product_id
    WHERE o.customer_id=$uid
    GROUP BY o.id ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Orders – Floravera</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link href="../style.css" rel="stylesheet">
</head>
<body>
<nav class="fv-navbar navbar fixed-top px-4 d-flex align-items-center justify-content-between">
  <a class="fv-logo" href="../index.php">✿ <span>Flora</span>vera</a>
  <a href="cart.php" class="btn-fv-fill btn-sm" style="text-decoration:none">🛒 Cart</a>
</nav>
<div class="fv-dash-wrap">
  <?php include 'partials/sidebar.php'; ?>
  <div class="fv-main">
    <div class="fv-topbar"><div class="fv-topbar-title">My Orders</div></div>
    <div class="fv-dash-content">

      <?php if(isset($_GET['placed'])): ?>
        <div class="alert alert-success">✅ Order placed successfully! We'll process it shortly.</div>
      <?php endif; ?>

      <div class="fv-card">
        <?php if($orders->num_rows===0): ?>
          <div class="text-center py-5 text-muted">No orders yet. <a href="dashboard.php">Start shopping</a>!</div>
        <?php else: ?>
          <table class="table checkout-table align-middle">
            <thead><tr><th>#</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php while($o=$orders->fetch_assoc()): ?>
            <tr>
              <td class="text-muted small">#<?= $o['id'] ?></td>
              <td>
                <div style="font-size:13px;font-weight:500"><?= htmlspecialchars(mb_strimwidth($o['items'],0,50,'...')) ?></div>
              </td>
              <td class="fw-600" style="color:var(--fv-pink)">₱<?= number_format($o['total'],2) ?></td>
              <td><span class="fv-status fv-status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
              <td class="text-muted small"><?= date('M d, Y',strtotime($o['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
