<?php

require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

$userAccessStatus = $_SESSION['acess'];
$accountingYearId = $_SESSION['accountingYearId'];

$yearMaster = $dbReference->getData("tbl_accounting_year_master", "*", ["id" => $accountingYearId])[0];

if ($userAccessStatus != 1) {
    echo json_encode(['monthly_profits' => null]);
    exit();
}

$monthlyProfits = [];
$currentYear = $yearMaster['year'];

for ($month = 1; $month <= 12; $month++) {
    $startOfMonth = date("Y-m-01", strtotime("$currentYear-$month-01"));
    $endOfMonth = date("Y-m-t", strtotime("$currentYear-$month-01"));

    $sql = "SELECT
    COALESCE(
        (
        SELECT
            SUM(total_payment_amount)
        FROM
            tbl_payments_history
        WHERE
            accounting_year_id = $accountingYearId AND payment_date BETWEEN '$startOfMonth' AND '$endOfMonth'
    ),
    0
    ) - COALESCE(
        (
        SELECT
            SUM(amount)
        FROM
            tbl_expenses
        WHERE
            expenses_date BETWEEN '$startOfMonth 00:00:00' AND '$endOfMonth 23:59:59'
    ),
    0
    ) AS net_balance;";

    $result = $dbReference->getConnection()->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        $netBalance = $row['net_balance'];

        if ($netBalance !== null) {
            $monthlyProfits[date("F Y", strtotime("2023-$month-01"))] = $netBalance;
        } else {
            $monthlyProfits[date("F Y", strtotime("2023-$month-01"))] = 0;
        }
    } else {
        $monthlyProfits[date("F Y", strtotime("2023-$month-01"))] = 'Query failed';
    }
}

echo json_encode(['monthly_profits' => $monthlyProfits]);
