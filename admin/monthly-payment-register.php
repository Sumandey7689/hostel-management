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
    "payment_date",
    "ASC"
);

// Group transactions by date
$dateGrouped = [];
foreach ($transactionData as $transaction) {
    $date = date('Y-m-d', strtotime($transaction['payment_date']));
    if (!isset($dateGrouped[$date])) {
        $dateGrouped[$date] = [
            'transactions' => [],
            'total' => 0
        ];
    }
    $dateGrouped[$date]['transactions'][] = $transaction;
    $dateGrouped[$date]['total'] += $transaction['total_payment_amount'];
}

ksort($dateGrouped);

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Hostel Management System');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10, 10); // Set equal margins on all sides
$pdf->AddPage();

// Get page width excluding margins
$pageWidth = $pdf->getPageWidth() - 20;

// Adjust column widths to fit page
$widths = [10, 40, 20, 20, 20, 20, 25, 35]; // Modified widths
$headers = ['SL', 'Name', 'Mobile', 'Receipt No.', 'Date', 'Status', 'Comments', 'Amount'];

// Header
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 6, 'SHANTI GIRLS HOSTEL', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'Payment Register', 0, 1, 'C');

if ($month && $year) {
    $monthName = date('F', mktime(0, 0, 0, $month, 1));
    $pdf->Cell(0, 5, "$monthName $year", 0, 1, 'C');
}

$pdf->Ln(2);

$totalAmount = 0;
$rowCount = 0;

foreach ($dateGrouped as $date => $group) {
    $pdf->SetFont('helvetica', 'B', 9);

    // Table headers
    $pdf->SetFillColor(240, 240, 240);
    $x = $pdf->GetX();
    foreach ($headers as $i => $header) {
        $pdf->Cell($widths[$i], 6, $header, 1, 0, 'C', true);
    }
    $pdf->Ln();

    // Reset for data
    $pdf->SetFont('helvetica', '', 8);

    foreach ($group['transactions'] as $data) {
        $rowCount++;

        if ($pdf->GetY() > 270) {
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetFillColor(240, 240, 240);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 6, $header, 1, 0, 'C', true);
            }
            $pdf->Ln();
            $pdf->SetFont('helvetica', '', 8);
        }

        $pdf->Cell($widths[0], 5, $rowCount, 1, 0, 'C');
        $pdf->Cell($widths[1], 5, $data['name'], 1, 0, 'L');
        $pdf->Cell($widths[2], 5, $data['number'], 1, 0, 'C');
        $pdf->Cell($widths[3], 5, $data['receipt_no'], 1, 0, 'C');
        $pdf->Cell($widths[4], 5, date('d/m/Y', strtotime($data['payment_date'])), 1, 0, 'C');

        // Status cell with circle
        $statusX = $pdf->GetX();
        $statusY = $pdf->GetY();
        $pdf->Cell($widths[5], 5, '', 1, 0, 'C');
        
        // Draw status circle
        $statusColor = strtolower($data['payment_color']);
        switch ($statusColor) {
            case 'paid':
                $pdf->SetFillColor(45, 206, 137); break;
            case 'pending':
                $pdf->SetFillColor(255, 165, 0); break;
            case 'gray':
                $pdf->SetFillColor(128, 128, 128); break;
            case 'pink':
                $pdf->SetFillColor(255, 192, 203); break;
            case 'red':
                $pdf->SetFillColor(255, 0, 0); break;
            case 'blue':
                $pdf->SetFillColor(0, 0, 255); break;
            default:
                $pdf->SetFillColor(45, 206, 137);
        }
        
        $circleX = $statusX + ($widths[5] / 2);
        $circleY = $statusY + 2.5;
        $pdf->Circle($circleX, $circleY, 2, 0, 360, 'F');

        $pdf->Cell($widths[6], 5, $data['additional_comments'], 1, 0, 'C');
        $pdf->Cell($widths[7], 5, 'Rs. ' . number_format($data['total_payment_amount'], 2), 1, 0, 'R');
        $pdf->Ln();

        $totalAmount += $data['total_payment_amount'];
    }

    // Daily total
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell($pageWidth - 35, 6, 'Date Wise Collection: ' . date('d/m/Y', strtotime($date)), 1, 0, 'L');
    $pdf->Cell(35, 6, 'Rs. ' . number_format($group['total'], 2), 1, 1, 'R');
    $pdf->Ln(2);
}

// Summary
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell($pageWidth, 6, 'Summary', 1, 1, 'C', true);
$pdf->Cell($pageWidth - 25, 6, 'Total Collection', 1, 0, 'L');
$pdf->Cell(25, 6, 'Rs. ' . number_format($totalAmount, 2), 1, 1, 'R');

if ($month && $year) {
    $monthName = date('F', mktime(0, 0, 0, $month, 1));
    $filename = "payment_register_{$monthName}_{$year}.pdf";
} else {
    $filename = "payment_register.pdf";
}

$pdf->Output($filename, 'I');
