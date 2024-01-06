<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_POST['user_id'];

    $addressData = $dbReference->getData("tbl_addresses", "*", ["user_id" => $userId]);

    echo json_encode($addressData);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
