<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $roomType = $_POST['room_type'];
    $availableRoomData = $dbReference->conditionGetData("tbl_rooms_data", "DISTINCT room_category", "(room_capacity - room_filled) != 0 AND room_type = '$roomType'");
    if ($availableRoomData) {
        echo json_encode($availableRoomData);
    } else {
        $availableRoomData = [];
        echo json_encode($availableRoomData);        
    }
    
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
