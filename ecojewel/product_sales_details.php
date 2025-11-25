<?php
include 'db_connect.php';

$product_name = $_GET['product'] ?? '';
$year = $_GET['year'] ?? date('Y');

// Query product sales
$query = "
SELECT 
  o.id AS order_id,
  o.order_date,
  oi.quantity,
  oi.price,
  (oi.quantity * oi.price) AS total
FROM order_items oi
JOIN products p ON oi.product_id = p.id
JOIN orders o ON oi.order_id = o.id
WHERE p.name = '$product_name'
  AND YEAR(o.order_date) = $year
ORDER BY o.order_date DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($product_name) ?> Sales - <?= $year ?></title>
<style>
  body {
    font-family: Poppins, sans-serif;
    background: #fff8f0;
    margin: 0;
    padding: 20px;
  }
  h1 {
    color: #b8962f;
    text-align: center;
  }
  table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }
  th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
  }
  th {
    background-color: #d4af37;
    color: white;
  }
  tr:nth-child(even) {
    background-color: #f9f9f9;
  }
  a {
    text-decoration: none;
    background-color: #d4af37;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    display: inline-block;
    margin-bottom: 15px;
  }
  a:hover {
    background-color: #b8962f;
  }
</style>
</head>
<body>

<a href="admin_dashboard.php?year=<?= $year ?>">⬅ Back to Dashboard</a>
<h1>💎 <?= htmlspecialchars($product_name) ?> Sales (<?= $year ?>)</h1>
<a href="admin_dashboard.php?year=<?= $year ?>">⬅ Back to Dashboard</a>
<a href="download_product_sales_pdf.php?product=<?= urlencode($product_name) ?>&year=<?= $year ?>" target="_blank">📄 Download PDF</a>
<h1>💎 <?= htmlspecialchars($product_name) ?> Sales (<?= $year ?>)</h1>


<table>
  <tr>
    <th>Order ID</th>
    <th>Date</th>
    <th>Quantity</th>
    <th>Price (₱)</th>
    <th>Total (₱)</th>
  </tr>

  <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td>#<?= $row['order_id'] ?></td>
        <td><?= date('M d, Y', strtotime($row['order_date'])) ?></td>
        <td><?= $row['quantity'] ?></td>
        <td><?= number_format($row['price'], 2) ?></td>
        <td><?= number_format($row['total'], 2) ?></td>
      </tr>
    <?php endwhile; ?>
  <?php else: ?>
      <tr><td colspan="5">No sales found for this product in <?= $year ?>.</td></tr>
  <?php endif; ?>
</table>

</body>
</html>
