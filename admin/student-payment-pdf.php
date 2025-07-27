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

if (!$userprofile) {
    header('location: login.php');
    exit;
}
$studentIds = isset($_GET['student']) ? explode(',', $_GET['student']) : [];
$year = isset($_GET['year']) ? $_GET['year'] : '';

$studentIds = array_filter(array_map('intval', $studentIds));

$conditions = ["tbl_payments_history.active" => 1];
if (!empty($studentIds) && $year) {
    $conditions["tbl_payments_history.user_id"] = ['IN', $studentIds];
    $conditions["DATE_FORMAT(tbl_payments_history.payment_date, '%Y')"] = $year;
}

$transactionData = $dbReference->joinTablesInCondition(
    "tbl_payments_history",
    "tbl_users",
    "tbl_payments_history.user_id",
    "tbl_users.user_id",
    $conditions,
    "payment_date",
    "ASC"
);
$studentGrouped = [];
foreach ($transactionData as $transaction) {
    $studentId = $transaction['user_id'];
    if (!isset($studentGrouped[$studentId])) {
        $studentGrouped[$studentId] = [
            'transactions' => [],
            'total' => 0,
            'name' => $transaction['name'],
            'number' => $transaction['number']
        ];
    }
    $studentGrouped[$studentId]['transactions'][] = $transaction;
    $studentGrouped[$studentId]['total'] += $transaction['total_payment_amount'];
}

ksort($studentGrouped);

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Hostel Management System');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10, 10);
$pdf->AddPage();

$pageWidth = $pdf->getPageWidth() - 15; 

$widths = [15, 30, 30, 25, 15, 15, 30, 35];
$headers = ['SL', 'Receipt No.', 'Payment Date', 'Month', 'Year', 'Status', 'Comments', 'Amount'];

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 6, 'SHANTI GIRLS HOSTEL', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'Student Payment Register', 0, 1, 'C');

if ($year) {
    $pdf->Cell(0, 5, "Year: $year", 0, 1, 'C');
}
$pdf->Ln(2);

$totalAmount = 0;
$rowCount = 0;

foreach ($studentGrouped as $studentId => $student) {
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(220, 220, 220);
    
    $pdf->Cell($pageWidth, 6, 'Student Name: ' . $student['name'] . ' | Mobile: ' . $student['number'], 1, 1, 'L', true);
    
    $pdf->SetFillColor(240, 240, 240);
    foreach ($headers as $i => $header) {
        $pdf->Cell($widths[$i], 6, $header, 1, 0, 'C', true);
    }
    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 8);
    $studentRowCount = 0;
    foreach ($student['transactions'] as $data) {
        $studentRowCount++;

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

        $pdf->Cell($widths[0], 5, $studentRowCount, 1, 0, 'C');
        $pdf->Cell($widths[1], 5, $data['receipt_no'], 1, 0, 'C');
        $pdf->Cell($widths[2], 5, date('d/m/Y', strtotime($data['payment_date'])), 1, 0, 'C');
        $pdf->Cell($widths[3], 5, $data['payment_month'], 1, 0, 'C');
        $pdf->Cell($widths[4], 5, date('Y', strtotime($data['payment_date'])), 1, 0, 'C');

        $statusX = $pdf->GetX();
        $statusY = $pdf->GetY();
        $pdf->Cell($widths[5], 5, '', 1, 0, 'C');

        $statusColor = strtolower($data['payment_color']);
        switch ($statusColor) {
            case 'paid':   $pdf->SetFillColor(45, 206, 137); break;
            case 'pending':$pdf->SetFillColor(255, 165, 0);  break;
            case 'gray':   $pdf->SetFillColor(128, 128, 128); break;
            case 'pink':   $pdf->SetFillColor(255, 192, 203); break;
            case 'red':    $pdf->SetFillColor(255, 0, 0);     break;
            case 'blue':   $pdf->SetFillColor(0, 0, 255);     break;
            default:       $pdf->SetFillColor(45, 206, 137);
        }

        $circleX = $statusX + ($widths[5] / 2);
        $circleY = $statusY + 2.5;
        $pdf->Circle($circleX, $circleY, 2, 0, 360, 'F');

        $pdf->Cell($widths[6], 5, $data['additional_comments'], 1, 0, 'L');
        $pdf->Cell($widths[7], 5, 'Rs. ' . number_format($data['total_payment_amount'], 2), 1, 0, 'R');
        $pdf->Ln();

        $totalAmount += $data['total_payment_amount'];
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell($pageWidth - 35, 6, 'Student Total Collection', 1, 0, 'L');
    $pdf->Cell(35, 6, 'Rs. ' . number_format($student['total'], 2), 1, 1, 'R');
    $pdf->Ln(2);
}

$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell($pageWidth, 6, 'Summary', 1, 1, 'C', true);
$pdf->Cell($pageWidth - 35, 6, 'Total Collection', 1, 0, 'L');
$pdf->Cell(35, 6, 'Rs. ' . number_format($totalAmount, 2), 1, 1, 'R');

$filename = "student_payment_register.pdf";
$pdf->Output($filename, 'I');
