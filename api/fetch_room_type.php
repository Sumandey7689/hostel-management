<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $roomType = $dbReference->getData("tbl_rooms_data", "DISTINCT room_type");
    if ($roomType) {
        echo json_encode($roomType);
    } else {
        $roomType = [];
        echo json_encode($roomType);        
    }
    
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
