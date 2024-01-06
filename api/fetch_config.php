<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $config = $dbReference->getData("tbl_config", "late_fine, reset_date", ["id" => "1"]);
    $config['reset_date'] = $helper->getFormatedDate($config[0]['reset_date']);

    echo json_encode($config);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
