<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_POST['user_id'];

    $roomData = $dbReference->getData("tbl_users_room", "*", ["user_id" => $userId]);

    echo json_encode($roomData);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
