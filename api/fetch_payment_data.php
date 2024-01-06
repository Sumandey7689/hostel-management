<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];

    $paymentData = $dbReference->getData("tbl_payments", "*", ["user_id" => $userId]);
    $configData = $dbReference->getData("tbl_config", "late_fine", ["id" => "1"]);

    if (!empty($paymentData)) {
        $paymentDueDate = $paymentData[0]['payment_due_date'];
        $gracePeriod = $paymentData[0]['grace_period'];

        $adjustedDueDate = strtotime($paymentDueDate . " + $gracePeriod days");

        $currentDate = time();
        if ($currentDate > $adjustedDueDate) {
            $differenceInSeconds = $currentDate - $adjustedDueDate;
            $lateDays = floor($differenceInSeconds / (60 * 60 * 24));

            $lateFeeAmount = $lateDays * $configData[0]['late_fine'];

            $paymentData[0]['late_fee'] = $lateFeeAmount;
        } else {
            $paymentData[0]['late_fee'] = 0;
        }

        echo json_encode($paymentData);
    } else {
        echo json_encode(['error' => 'No payment data found for the user.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
