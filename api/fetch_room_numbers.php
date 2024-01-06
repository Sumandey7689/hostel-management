<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $roomType = $_POST['room_type'];
    $room_category = $_POST['room_category'];

    $roomNumber = $dbReference->conditionGetData("tbl_rooms_data", "room_number", "(room_capacity - room_filled) != 0 AND room_type = '$roomType' AND room_category = '$room_category'");
    if ($roomNumber) {
        echo json_encode($roomNumber);
    } else {
        $roomNumber = [];
        echo json_encode($roomNumber);        
    }
    
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
