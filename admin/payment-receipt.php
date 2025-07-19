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

$paymentId = isset($_GET['id']) ? $_GET['id'] : '';

if (!$paymentId) {
    die("Error: Payment ID is required");
}

$conditions = [
    "tbl_payments_history.active " => 1,
    "tbl_payments_history.payment_id" => $paymentId
];

$transactionData = $dbReference->joinTables(
    "tbl_payments_history",
    "tbl_users",
    "tbl_payments_history.user_id",
    "tbl_users.user_id",
    $conditions,
    "payment_id",
    "DESC"
);

if (empty($transactionData)) {
    die("Error: Payment record not found");
}

$data = $transactionData[0];

// Create A4 format PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Hostel Management System');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
// A4 page height is automatically set when creating the PDF with 'A4' format
$pdf->AddPage();
// Function to draw decorative dotted line
function drawDottedLine($pdf, $y) {
    $pdf->SetLineStyle(array('width' => 0.3, 'dash' => '2,2'));
    $pdf->Line(15, $y, 195, $y);
    $pdf->SetLineStyle(array('width' => 0.2, 'dash' => 0));
}

// Header with decorative elements
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 8, 'HOSTEL MANAGEMENT', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 6, 'PAYMENT RECEIPT', 0, 1, 'C');
drawDottedLine($pdf, $pdf->GetY());
$pdf->Ln(3);

// Receipt Details in compact format
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(35, 5, 'Receipt No:', 0, 0);
$pdf->Cell(150, 5, $data['payment_id'], 0, 1);

$pdf->Cell(35, 5, 'Date:', 0, 0);
$pdf->Cell(150, 5, date('d/m/Y', strtotime($data['payment_date'])), 0, 1);

$pdf->Cell(35, 5, 'Name:', 0, 0);
$pdf->Cell(150, 5, $data['name'], 0, 1);

$pdf->Cell(35, 5, 'Mobile:', 0, 0);
$pdf->Cell(150, 5, $data['number'], 0, 1);

$pdf->Ln(2);
drawDottedLine($pdf, $pdf->GetY());
$pdf->Ln(3);

// Payment Details
$pdf->Cell(150, 5, 'Amount:', 0, 0);
$pdf->Cell(30, 5, 'R.s ' . number_format($data['total_payment_amount'], 2), 0, 1, 'R');

if (!empty($data['additional_comments'])) {
    $pdf->Ln(2);
    $pdf->Cell(35, 5, 'Remarks:', 0, 0);
    $pdf->MultiCell(150, 5, $data['additional_comments'], 0, 'L');
}

$pdf->Ln(2);
drawDottedLine($pdf, $pdf->GetY());
$pdf->Ln(3);

// Footer with decorative elements
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(0, 5, 'Thank you for your payment', 0, 1, 'C');
$pdf->Cell(0, 5, 'This is a computer generated receipt', 0, 1, 'C');

$filename = "payment_receipt_" . date('Y-m-d', strtotime($data['payment_date'])) . ".pdf";
$pdf->Output($filename, 'I');
