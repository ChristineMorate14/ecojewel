<?php
require 'vendor/autoload.php';
include 'db_connect.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$year = $_GET['year'] ?? date('Y');

// === Shop Info ===
$shop_name = "Elegant Gems Jewelry Shop";
$shop_address = "123 Gold Street, Manila, Philippines";
$shop_contact = "📞 0917-123-4567 | ✉️ elegantgems@gmail.com";
$admin_name = "Christine Marie Morate";

// === Query: Product Sales ===
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
ORDER BY total_sales DESC";
$result_products = mysqli_query($conn, $query_products);

// === Query: Monthly Sales ===
$query_monthly = "
SELECT 
  MONTH(o.order_date) AS month,
  SUM(oi.quantity * oi.price) AS monthly_sales
FROM order_items oi
JOIN orders o ON oi.order_id = o.id
WHERE YEAR(o.order_date) = $year
GROUP BY MONTH(o.order_date)
ORDER BY MONTH(o.order_date)";
$result_monthly = mysqli_query($conn, $query_monthly);

$monthly_data = array_fill(1, 12, 0);
while ($row = mysqli_fetch_assoc($result_monthly)) {
  $monthly_data[(int)$row['month']] = (float)$row['monthly_sales'];
}

$total_yearly_sales = array_sum($monthly_data);

// === Create Spreadsheet ===
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// === Header Section ===
$sheet->setCellValue('A1', $shop_name);
$sheet->mergeCells('A1:C1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('B8962F');

$sheet->setCellValue('A2', $shop_address);
$sheet->mergeCells('A2:C2');

$sheet->setCellValue('A3', $shop_contact);
$sheet->mergeCells('A3:C3');

$sheet->setCellValue('A5', "💎 Full Year Sales Report ($year)");
$sheet->mergeCells('A5:C5');
$sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);

// === Monthly Sales Summary ===
$sheet->setCellValue('A7', 'Month');
$sheet->setCellValue('B7', 'Total Sales (₱)');
$sheet->getStyle('A7:B7')->getFont()->setBold(true);

$rowNum = 8;
$months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];

foreach ($months as $m => $name) {
  $sheet->setCellValue("A$rowNum", $name);
  $sheet->setCellValue("B$rowNum", $monthly_data[$m]);
  $rowNum++;
}

$sheet->setCellValue("A$rowNum", 'Grand Total');
$sheet->setCellValue("B$rowNum", $total_yearly_sales);
$sheet->getStyle("A$rowNum:B$rowNum")->getFont()->setBold(true);
$rowNum += 2;

// === Product Sales Section ===
$sheet->setCellValue("A$rowNum", 'Product Performance');
$sheet->mergeCells("A$rowNum:C$rowNum");
$sheet->getStyle("A$rowNum")->getFont()->setBold(true)->setSize(12);
$rowNum += 1;

$sheet->setCellValue("A$rowNum", 'Product Name');
$sheet->setCellValue("B$rowNum", 'Quantity Sold');
$sheet->setCellValue("C$rowNum", 'Total Sales (₱)');
$sheet->getStyle("A$rowNum:C$rowNum")->getFont()->setBold(true);

$rowNum++;

if (mysqli_num_rows($result_products) > 0) {
  while ($row = mysqli_fetch_assoc($result_products)) {
    $sheet->setCellValue("A$rowNum", $row['product_name']);
    $sheet->setCellValue("B$rowNum", $row['total_quantity']);
    $sheet->setCellValue("C$rowNum", $row['total_sales']);
    $rowNum++;
  }
}

$sheet->setCellValue("A$rowNum", 'Grand Total');
$sheet->setCellValue("B$rowNum", '');
$sheet->setCellValue("C$rowNum", $total_yearly_sales);
$sheet->getStyle("A$rowNum:C$rowNum")->getFont()->setBold(true);
$rowNum += 2;

// === Footer ===
$sheet->setCellValue("C$rowNum", "Prepared by: $admin_name");
$rowNum++;
$sheet->setCellValue("C$rowNum", "Generated on: " . date('F d, Y h:i A'));

// === Auto Size Columns ===
foreach (range('A', 'C') as $col) {
  $sheet->getColumnDimension($col)->setAutoSize(true);
}

// === Download Excel File ===
$filename = "Full_Year_Sales_Report_{$year}.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
