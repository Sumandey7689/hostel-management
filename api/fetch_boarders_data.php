<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $roomId = $_POST['roomId'];

    $boardersData = $dbReference->joinSQLDataWithConditions(
        "tbl_users",
        "tbl_users.name, tbl_users.number, tbl_users.active, tbl_users.user_id",
        "tbl_users_room",
        "tbl_users_room.user_id = tbl_users.user_id", ["tbl_users_room.room_id" => $roomId]
    );

    echo json_encode($boardersData);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
