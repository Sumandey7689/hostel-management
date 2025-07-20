<?php
class Helper
{
    public function getFormatedDate($date)
    {
        return date('d-m-Y', strtotime($date));
    }

    public function generateReceiptNo($accountingYearId, $db)
    {
        $con = $db->getConnection(); 

        $latestSql = "
            SELECT receipt_no 
            FROM tbl_payments_history 
            WHERE accounting_year_id = $accountingYearId 
            AND receipt_no IS NOT NULL 
            ORDER BY payment_id DESC 
            LIMIT 1
        ";
        $latestResult = $con->query($latestSql);
        $latestRow = $latestResult && $latestResult->num_rows > 0 ? $latestResult->fetch_assoc() : null;

        $yearSql = "SELECT year FROM tbl_accounting_year_master WHERE id = $accountingYearId LIMIT 1";
        $yearResult = $con->query($yearSql);
        $yearRow = $yearResult && $yearResult->num_rows > 0 ? $yearResult->fetch_assoc() : null;
        $yearShort = isset($yearRow['year']) ? substr($yearRow['year'], -2) : date('y');

        if ($latestRow && preg_match('/REC(\d{6})\d{2}$/', $latestRow['receipt_no'], $matches)) {
            $newSerial = (int)$matches[1] + 1;
        } else {
            $newSerial = 1;
        }

        return 'REC' . str_pad($newSerial, 6, '0', STR_PAD_LEFT) . $yearShort;
    }
}
