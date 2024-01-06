<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $expenses_id = $_POST['expenses_id'];
    $expensesData = $dbReference->getData("tbl_expenses", "*", ["expenses_id" => $expenses_id]);
    if ($expensesData) {
        echo json_encode($expensesData);
    } else {
        $expensesData = [];
        echo json_encode($expensesData);        
    }
    
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
