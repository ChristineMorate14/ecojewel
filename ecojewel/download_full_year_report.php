<?php
require 'vendor/autoload.php';
include 'db_connect.php';

use Dompdf\Dompdf;

$year = $_GET['year'] ?? date('Y');

// === Shop Info ===
$shop_name = "Elegant Gems Jewelry Shop";
$shop_address = "123 Gold Street, Manila, Philippines";
$shop_contact = "📞 0917-123-4567 | ✉️ elegantgems@gmail.com";
$admin_name = "Christine Marie Morate";

// === Query: Product-wise total sales ===
$query_products = "
SELECT 
  p.name AS product_name,
  SUM(oi.quantity) AS total_quantity,
  SUM(oi.quantity * oi.price) AS total_sales
FROM order_items oi
JOIN products p ON oi.product_id = p.id
JOIN orders o ON oi.order_id = o.id
WHERE YEAR(o.order_date) = $year
GROUP BY p.id
ORDER BY total_sales DESC
";
$result_products = mysqli_query($conn, $query_products);

// === Query: Monthly total sales ===
$query_monthly = "
SELECT 
  MONTH(o.order_date) AS month,
  SUM(oi.quantity * oi.price) AS monthly_sales
FROM order_items oi
JOIN orders o ON oi.order_id = o.id
WHERE YEAR(o.order_date) = $year
GROUP BY MONTH(o.order_date)
ORDER BY MONTH(o.order_date)
";
$result_monthly = mysqli_query($conn, $query_monthly);

$monthly_data = array_fill(1, 12, 0);
while ($row = mysqli_fetch_assoc($result_monthly)) {
  $monthly_data[(int)$row['month']] = (float)$row['monthly_sales'];
}

$total_yearly_sales = array_sum($monthly_data);

// === Prepare chart image (using QuickChart API) ===
$months = [
  1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',
  7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'
];
$labels = json_encode(array_values($months));
$data = json_encode(array_values($monthly_data));

$chart_url = "https://quickchart.io/chart?c=" . urlencode(json_encode([
  'type' => 'bar',
  'data' => [
    'labels' => array_values($months),
    'datasets' => [[
      'label' => "Monthly Sales ($year)",
      'data' => array_values($monthly_data),
      'backgroundColor' => '#d4af37'
    ]]
  ],
  'options' => [
    'plugins' => [
      'legend' => ['display' => false],
      'title' => ['display' => true, 'text' => 'Monthly Sales Overview', 'color' => '#b8962f', 'font' => ['size' => 16]]
    ],
    'scales' => [
      'x' => ['ticks' => ['color' => '#555']],
      'y' => ['ticks' => ['color' => '#555']]
    ]
  ]
]));

// === Build HTML ===
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
    .chart-container { text-align: center; margin: 25px 0; }
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
  <h4>💎 Full Year Sales Summary Report ($year)</h4>
</div>

<div class='chart-container'>
  <img src='$chart_url' width='500' height='250'>
</div>

<h4 style='margin-bottom:5px;color:#b8962f;'>📦 Product Performance</h4>
<table>
  <thead>
    <tr>
      <th>Product Name</th>
      <th>Total Quantity Sold</th>
      <th>Total Sales (₱)</th>
    </tr>
  </thead>
  <tbody>";

if (mysqli_num_rows($result_products) > 0) {
  while ($row = mysqli_fetch_assoc($result_products)) {
    $html .= "
    <tr>
      <td>{$row['product_name']}</td>
      <td>{$row['total_quantity']}</td>
      <td>₱" . number_format($row['total_sales'], 2) . "</td>
    </tr>";
  }
} else {
  $html .= "<tr><td colspan='3'>No sales data available.</td></tr>";
}

$html .= "
    <tr>
      <td colspan='2' style='text-align:right;font-weight:bold;'>Grand Total</td>
      <td style='font-weight:bold;'>₱" . number_format($total_yearly_sales, 2) . "</td>
    </tr>
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
$dompdf->stream("Full_Year_Sales_Report_{$year}.pdf", ["Attachment" => false]);
exit;
?>
