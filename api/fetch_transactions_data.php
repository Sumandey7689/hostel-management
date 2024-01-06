<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_POST['user_id'];
    $active = $_POST['active'];

    $transactions = $dbReference->getData("tbl_payments_history", "*", ["user_id" => $userId, "active" => $active]);

    foreach ($transactions as &$transaction) {
        $transaction['payment_date'] = $helper->getFormatedDate($transaction['payment_date']);
    }

    echo json_encode($transactions);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
