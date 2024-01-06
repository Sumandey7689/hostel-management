<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_POST['userId'];

    $roomTrackData = $dbReference->getData("tbl_room_tracking", "*", ["user_id" => $userId]);

    echo json_encode($roomTrackData);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
