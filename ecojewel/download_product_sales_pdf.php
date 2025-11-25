<?php
require 'vendor/autoload.php'; // DomPDF autoload
include 'db_connect.php';

use Dompdf\Dompdf;

$product_name = $_GET['product'] ?? '';
$year = $_GET['year'] ?? date('Y');

// === Fetch product sales ===
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

// === Calculate total sales ===
$total_sales = 0;
while ($row = mysqli_fetch_assoc($result)) {
  $total_sales += $row['total'];
  $sales_data[] = $row;
}
mysqli_data_seek($result, 0); // reset pointer

// === Shop Info ===
$shop_name = "Elegant Gems Jewelry Shop";
$shop_address = "123 Gold Street, Manila, Philippines";
$shop_contact = "📞 0917-123-4567 | ✉️ elegantgems@gmail.com";
$admin_name = "Christine Marie Morate"; // You can change this later

// === HTML Layout for PDF ===
$html = "
<html>
<head>
  <style>
    body { font-family: Poppins, sans-serif; color: #333; font-size: 12px; }
    h2, h4 { color: #b8962f; text-align: center; margin: 5px 0; }
    .header { text-align: center; margin-bottom: 20px; }
    .logo { width: 70px; height: 70px; border-radius: 50%; margin-bottom: 10px; }
    .info { text-align: center; font-size: 11px; color: #555; }
    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background-color: #d4af37; color: white; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .footer { margin-top: 40px; text-align: right; font-size: 11px; color: #777; }
    .signature { margin-top: 60px; text-align: right; }
    .signature-line { display: inline-block; border-top: 1px solid #000; padding-top: 5px; width: 200px; }
  </style>
</head>
<body>

<div class='header'>
  <img src='https://i.ibb.co/4Y9Z6Sj/gold-logo.png' class='logo'>
  <h2>$shop_name</h2>
  <div class='info'>
    $shop_address<br>$shop_contact
  </div>
  <hr style='margin-top:10px; border: 0; border-top: 2px solid #d4af37;'>
  <h4>💎 $product_name Sales Report ($year)</h4>
</div>

<table>
  <thead>
    <tr>
      <th>Order ID</th>
      <th>Date</th>
      <th>Quantity</th>
      <th>Price (₱)</th>
      <th>Total (₱)</th>
    </tr>
  </thead>
  <tbody>";

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $html .= "
    <tr>
      <td>#{$row['order_id']}</td>
      <td>" . date('M d, Y', strtotime($row['order_date'])) . "</td>
      <td>{$row['quantity']}</td>
      <td>" . number_format($row['price'], 2) . "</td>
      <td>" . number_format($row['total'], 2) . "</td>
    </tr>";
  }
  $html .= "
    <tr>
      <td colspan='4' style='text-align:right; font-weight:bold;'>Total Sales</td>
      <td style='font-weight:bold;'>₱" . number_format($total_sales, 2) . "</td>
    </tr>";
} else {
  $html .= "<tr><td colspan='5' align='center'>No sales found for this product.</td></tr>";
}

$html .= "
  </tbody>
</table>

<div class='signature'>
  <p>Prepared by:</p>
  <div class='signature-line'>$admin_name</div>
</div>

<div class='footer'>
  Generated on " . date('F d, Y h:i A') . "
</div>

</body>
</html>
";

// === Generate PDF ===
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("{$product_name}_sales_report_{$year}.pdf", ["Attachment" => false]);
exit;
?>
