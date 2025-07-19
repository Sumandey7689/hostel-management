<?php
require '../database.php';
require '../helper.php';

$tcpdfPath = '../vendor/autoload.php';
if (!file_exists($tcpdfPath)) {
    die("Error: TCPDF is not installed. Please run 'composer require tecnickcom/tcpdf' in the project root directory.");
}
require $tcpdfPath;

$dbReference = new Database();
$helper = new Helper();

$userprofile = $_SESSION['username'];
$userAcessStatus = $_SESSION['acess'];
$accountingYearId = $_SESSION['accountingYearId'];

if ($userprofile != true) {
    header('location: login.php');
    exit;
}

$date = isset($_GET['date']) ? $_GET['date'] : '';
$month = '';
$year = '';

if ($date) {
    $parts = explode('/', $date);
    if (count($parts) == 2) {
        $month = $parts[0];
        $year = $parts[1];
    }
}

$conditions = ["tbl_payments_history.active " => 1];
if ($month && $year) {
    $conditions["DATE_FORMAT(tbl_payments_history.payment_date, '%m')"] = $month;
    $conditions["DATE_FORMAT(tbl_payments_history.payment_date, '%Y')"] = $year;
}

$transactionData = $dbReference->joinTables(
    "tbl_payments_history",
    "tbl_users",
    "tbl_payments_history.user_id",
    "tbl_users.user_id",
    $conditions,
    "payment_id",
    "DESC"
);

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Hostel Management System');
// $pdf->SetTitle('Payment Register');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 15, 15, 15);
$pdf->AddPage();

// Add compact header
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, 'HOSTEL MANAGEMENT', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 6, 'Payment Register', 0, 1, 'C');

if ($month && $year) {
    $pdf->SetFont('helvetica', '', 10);
    $monthName = date('F', mktime(0, 0, 0, $month, 1));
    $pdf->Cell(0, 6, "$monthName $year", 0, 1, 'C');
}

$pdf->Ln(2);
$pdf->SetDrawColor(236, 240, 241);
$pdf->SetY(40);
$pdf->SetFillColor(52, 152, 219);
$pdf->SetTextColor(255);
$pdf->SetFont('helvetica', 'B', 10);

$headers = ['SL', 'Name', 'Mobile', 'Date', 'Remarks', 'Amount'];
$widths = [12, 45, 25, 25, 50, 30];

foreach ($headers as $i => $header) {
    $pdf->Cell($widths[$i], 7, $header, 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFillColor(245, 247, 250);
$pdf->SetTextColor(0);
$pdf->SetFont('helvetica', '', 9);

$fill = false;
$totalAmount = 0;
$rowCount = 0;

foreach ($transactionData as $data) {
    $rowCount++;

    if ($pdf->GetY() > 250) {
        $pdf->AddPage();
        $pdf->SetFillColor(52, 152, 219);
        $pdf->SetTextColor(255);
        $pdf->SetFont('helvetica', 'B', 10);
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $header, 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFillColor(245, 247, 250);
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 9);
    }

    $pdf->Cell($widths[0], 6, $rowCount, 1, 0, 'C', $fill);
    $pdf->Cell($widths[1], 6, $data['name'], 1, 0, 'L', $fill);
    $pdf->Cell($widths[2], 6, $data['number'], 1, 0, 'C', $fill);
    $pdf->Cell($widths[3], 6, date('d/m/Y', strtotime($data['payment_date'])), 1, 0, 'C', $fill);
    $pdf->Cell($widths[4], 6, $data['additional_comments'], 1, 0, 'L', $fill);
    $pdf->Cell($widths[5], 6, 'R.s ' . number_format($data['total_payment_amount'], 2), 1, 0, 'C', $fill);
    $pdf->Ln();

    $fill = !$fill;
    $totalAmount += $data['total_payment_amount'];
}

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(array_sum($widths) - 30, 7, 'Total Amount:', 1, 0, 'L');
$pdf->Cell(30, 7, 'R.s ' . number_format($totalAmount, 2), 1, 1, 'R');

$pdf->Ln(5);
$pdf->SetFillColor(52, 152, 219);
$pdf->SetTextColor(255);
$pdf->Cell(0, 7, 'Summary', 1, 1, 'C', true);
$pdf->SetTextColor(0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, "Total Transactions: $rowCount", 1, 1, 'L');
$pdf->Cell(0, 6, 'Total Collection: R.s ' . number_format($totalAmount, 2), 1, 1, 'L');
if ($month && $year) {
    $monthName = date('F', mktime(0, 0, 0, $month, 1));
    $filename = "payment_register_{$monthName}_{$year}.pdf";
} else {
    $filename = "payment_register.pdf";
}

$pdf->Output($filename, 'I');
