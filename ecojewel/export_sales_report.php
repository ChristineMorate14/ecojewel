<?php
require('db_connect.php');
require_once __DIR__ . '/vendor/autoload.php'; // for Composer libraries

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

// Select year (from dropdown or default current)
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$format = $_GET['format'] ?? 'excel'; // 'excel' or 'pdf'

// Query summary data
$total_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS total_sales FROM orders WHERE YEAR(order_date) = $selected_year"))['total_sales'] ?? 0;
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_orders FROM orders WHERE YEAR(order_date) = $selected_year"))['total_orders'] ?? 0;
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS pending_orders FROM orders WHERE status='Pending' AND YEAR(order_date) = $selected_year"))['pending_orders'] ?? 0;
$completed_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS completed_orders FROM orders WHERE status='Completed' AND YEAR(order_date) = $selected_year"))['completed_orders'] ?? 0;

// Monthly sales breakdown
$result = mysqli_query($conn, "SELECT MONTH(order_date) AS month, SUM(total_amount) AS total_sales 
                               FROM orders 
                               WHERE YEAR(order_date) = $selected_year 
                               GROUP BY MONTH(order_date)");

$months = [];
$sales = [];
while ($row = mysqli_fetch_assoc($result)) {
  $months[] = date("F", mktime(0, 0, 0, $row['month'], 1));
  $sales[] = $row['total_sales'];
}

// EXPORT TO EXCEL
if ($format === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Title
    $sheet->setCellValue('A1', 'Elegant Gems Jewelry Shop');
    $sheet->setCellValue('A2', "Sales Report - $selected_year");
    $sheet->mergeCells('A1:B1');
    $sheet->mergeCells('A2:B2');

    // Summary
    $sheet->setCellValue('A4', 'Total Sales');
    $sheet->setCellValue('B4', '₱' . number_format($total_sales, 2));
    $sheet->setCellValue('A5', 'Total Orders');
    $sheet->setCellValue('B5', $total_orders);
    $sheet->setCellValue('A6', 'Pending Orders');
    $sheet->setCellValue('B6', $pending_orders);
    $sheet->setCellValue('A7', 'Completed Orders');
    $sheet->setCellValue('B7', $completed_orders);

    // Monthly Data
    $sheet->setCellValue('A9', 'Month');
    $sheet->setCellValue('B9', 'Sales (₱)');
    $row_num = 10;
    for ($i = 0; $i < count($months); $i++) {
        $sheet->setCellValue("A$row_num", $months[$i]);
        $sheet->setCellValue("B$row_num", $sales[$i]);
        $row_num++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = "ElegantGems_SalesReport_$selected_year.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer->save('php://output');
    exit;
}

// EXPORT TO PDF
else {
    $html = "
    <h2 style='text-align:center;'>Elegant Gems Jewelry Shop</h2>
    <h3 style='text-align:center;'>Sales Report - $selected_year</h3>
    <table border='1' cellspacing='0' cellpadding='6' width='100%'>
      <tr><td><b>Total Sales</b></td><td>₱" . number_format($total_sales, 2) . "</td></tr>
      <tr><td><b>Total Orders</b></td><td>$total_orders</td></tr>
      <tr><td><b>Pending Orders</b></td><td>$pending_orders</td></tr>
      <tr><td><b>Completed Orders</b></td><td>$completed_orders</td></tr>
    </table><br>
    <h4>📅 Monthly Breakdown</h4>
    <table border='1' cellspacing='0' cellpadding='6' width='100%'>
      <tr><th>Month</th><th>Total Sales (₱)</th></tr>";
    
    for ($i = 0; $i < count($months); $i++) {
        $html .= "<tr><td>{$months[$i]}</td><td>₱" . number_format($sales[$i], 2) . "</td></tr>";
    }

    $html .= "</table>";

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("ElegantGems_SalesReport_$selected_year.pdf", ["Attachment" => true]);
    exit;
}
?>
