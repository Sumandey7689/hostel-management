<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

$userprofile = $_SESSION['username'];
$userAcessStatus = $_SESSION['acess'];
if ($userprofile != true) {
    header('location: login.php');
    exit;
}

// Get Users Data
$userList = $dbReference->getData("tbl_users", "*", ["active" => "1"]);
$error_msg = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update User
    if (isset($_POST['UserSubmit'])) {

        $updateStatus = $dbReference->getData("tbl_users", "user_id", ["number" => $_POST['number']]);
        // Update User
        if (!$updateStatus && !empty($_POST['name']) && !empty($_POST['number']) && !empty($_POST['location_type']) && !empty($_POST['location_type']) && !empty($_POST['subject']) && !empty($_POST['year']) && !empty($_POST['semester'])) {
            $dbReference->updateData("tbl_users", ["name" => $_POST['name'], "number" => $_POST['number'], "location_type" => $_POST['location_type'], "subject" => $_POST['subject'], "year" => $_POST['year'], "semester" => $_POST['semester'], "organizationname" => $_POST['organizationname']], ["user_id" => $_POST['user_id']]);
            header('location: boarders.php');
            exit;
        } else if ($updateStatus[0]['user_id'] == $_POST['user_id'] &&  !empty($_POST['name']) && !empty($_POST['number']) && !empty($_POST['location_type']) && !empty($_POST['location_type']) && !empty($_POST['year']) && !empty($_POST['semester'])) {
            $dbReference->updateData("tbl_users", ["name" => $_POST['name'], "number" => $_POST['number'], "location_type" => $_POST['location_type'], "subject" => $_POST['subject'], "year" => $_POST['year'], "semester" => $_POST['semester'], "organizationname" => $_POST['organizationname']], ["user_id" => $_POST['user_id']]);
            header('location: boarders.php');
            exit;
        }
    }

    // // Add User
    if (isset($_POST['AddUserSubmit'])) {
        if (empty($_POST['add-user_id']) && !$dbReference->getData("tbl_users", "number", ["number" => $_POST['add-number']])) {

            if (!empty($_POST['add-name']) && !empty($_POST['add-number']) && !empty($_POST['add-location_type']) && !empty($_POST['add-location_type']) && !empty($_POST['add-organizationname'])) {
                $dbReference->insertData("tbl_users", ["name" => $_POST['add-name'], "number" => $_POST['add-number'], "location_type" => $_POST['add-location_type'], "subject" => $_POST['add-subject'], "year" => $_POST['add-year'], "semester" => $_POST['add-semester'], "organizationname" => $_POST['add-organizationname']]);
                $currentUserId = ($dbReference->getData("tbl_users", "user_id", ["number" => $_POST['add-number']]))[0]['user_id'];
                $dbReference->insertData("tbl_payments_months", ["user_id" => $currentUserId]);

                if (!empty($_POST['add-street_address']) && !empty($_POST['add-city']) && !empty($_POST['add-state']) && !empty($_POST['add-postal_code'])) {
                    $dbReference->insertData("tbl_addresses", ["user_id" => $currentUserId, "street_address" => $_POST['add-street_address'], "city" => $_POST['add-city'], "state" => $_POST['add-state'], "postal_code" => $_POST['add-postal_code'], "country" => "india"]);
                }

                if (!empty($_POST['add-payment_date']) && !empty($_POST['add-payment_due_date']) && !empty($_POST['add-total_payment_amount'])) {

                    $dbReference->insertData("tbl_payments", ["user_id" => $currentUserId, "payment_date" => $_POST['add-payment_date'], "payment_due_date" => $_POST['add-payment_due_date'], "total_payment_amount" => $_POST['add-total_payment_amount']]);

                    $dbReference->insertData("tbl_payments_history", ["user_id" => $currentUserId, "payment_date" => $_POST['add-payment_date'], "total_payment_amount" => $_POST['add-total_payment_amount'], "payment_month" => (new DateTime($_POST['add-payment_date']))->format('F')]);

                    $monthKey = strtolower((new DateTime($_POST['add-payment_date']))->format('F'));
                    $dbReference->updateData("tbl_payments_months", ["$monthKey" => "1"], ["user_id" => $currentUserId]);
                }

                if (!empty($_POST['add-room_type']) && !empty($_POST['add-room_category']) && !empty($_POST['add-room_number'])) {
                    $roomData = $dbReference->getData("tbl_rooms_data", "room_id, room_filled", ["room_type" => $_POST['add-room_type'], "room_category" => $_POST['add-room_category'], "room_number" => $_POST['add-room_number']]);

                    $dbReference->insertData("tbl_users_room", ["room_id" => $roomData[0]['room_id'], "user_id" => $currentUserId]);
                    $dbReference->updateData("tbl_rooms_data", ["room_filled" => ($roomData[0]['room_filled'] + 1)], ["room_id" => $roomData[0]['room_id']]);
                }


                header('location: boarders.php');
                exit;
            } else {
                $error_msg = true;
            }
        }
    }

    // Add / Update Payments
    else if (isset($_POST['PaymetsSubmit'])) {
        if (!empty($_POST['payment_date']) && !empty($_POST['payment_due_date']) && !empty($_POST['total_payment_amount'])) {
            if (!($dbReference->getData("tbl_payments", "*", ["user_id" => $_POST['user_id']]))) {
                $dbReference->insertData("tbl_payments", ["user_id" => $_POST['user_id'], "payment_date" => $_POST['payment_date'], "payment_due_date" => $_POST['payment_due_date'], "total_payment_amount" => $_POST['total_payment_amount']]);

                $dbReference->insertData("tbl_payments_history", ["user_id" => $_POST['user_id'], "payment_date" => $_POST['payment_date'], "total_payment_amount" => $_POST['total_payment_amount'], "payment_month" => (new DateTime($_POST['payment_date']))->format('F')]);

                $monthKey = strtolower((new DateTime($_POST['payment_date']))->format('F'));
                $dbReference->updateData("tbl_payments_months", ["$monthKey" => "1"], ["user_id" => $_POST['user_id']]);
            } else {
                $dbReference->updateData("tbl_payments", ["payment_date" => $_POST['payment_date'], "payment_due_date" => $_POST['payment_due_date'], "total_payment_amount" => $_POST['total_payment_amount']], ["user_id" => $_POST['user_id']]);
            }
            header('location: boarders.php');
            exit;
        }
    }

    // Add Address
    else if (isset($_POST['AddressSubmit'])) {
        if (!empty($_POST['street_address']) && !empty($_POST['city']) && !empty($_POST['state']) && !empty($_POST['postal_code'])) {
            if (!($dbReference->getData("tbl_addresses", "*", ["user_id" => $_POST['user_id']]))) {
                $dbReference->insertData("tbl_addresses", ["user_id" => $_POST['user_id'], "street_address" => $_POST['street_address'], "city" => $_POST['city'], "state" => $_POST['state'], "postal_code" => $_POST['postal_code'], "country" => "india"]);
            } else {
                $dbReference->updateData("tbl_addresses", ["street_address" => $_POST['street_address'], "city" => $_POST['city'], "state" => $_POST['state'], "postal_code" => $_POST['postal_code'], "country" => "india"], ["user_id" => $_POST['user_id']]);
            }
            header('location: boarders.php');
            exit;
        }
    }

    // Add / Update Room
    else if (isset($_POST['RoomSubmit'])) {
        if (!empty($_POST['room_type']) && !empty($_POST['room_category']) && !empty($_POST['room_number'])) {
            $roomType = $_POST['room_type'];
            $room_category = $_POST['room_category'];
            $room_number = $_POST['room_number'];
            $roomData = $dbReference->getData("tbl_rooms_data", "room_id, room_filled", ["room_type" => $roomType, "room_category" => $room_category, "room_number" => $room_number]);

            $userCurrentData = $dbReference->getData("tbl_users_room", "*", ["user_id" => $_POST['user_id']]);
            if (!$userCurrentData) {
                $roomInsertStatus = $dbReference->insertData("tbl_users_room", ["room_id" => $roomData[0]['room_id'], "user_id" => $_POST['user_id']]);

                if ($roomInsertStatus) {
                    $dbReference->updateData("tbl_rooms_data", ["room_filled" => ($roomData[0]['room_filled'] + 1)], ["room_id" => $roomData[0]['room_id']]);
                }
            } else {
                $roomId = $userCurrentData[0]['room_id'];
                $updatedRoomFilled = $dbReference->getData("tbl_rooms_data", "room_filled", ["room_id" => $roomId])[0]['room_filled'] - 1;

                if($updatedRoomFilled) {
                    $dbReference->updateData("tbl_rooms_data", ["room_filled" => $updatedRoomFilled], ["room_id" => $roomId]);
                }

                $roomUpdateStatus = $dbReference->updateData("tbl_users_room", ["room_id" => $roomData[0]['room_id']], ["user_id" => $_POST['user_id']]);

                if ($roomUpdateStatus) {
                    $dbReference->updateData("tbl_rooms_data", ["room_filled" => $roomData[0]['room_filled'] + 1], ["room_id" => $roomData[0]['room_id']]);
                }
                $dbReference->insertData("tbl_room_tracking", ["user_id" => $_POST['user_id'], "room_type" => $_POST['room_type'], "room_category" => $_POST['room_category'], "room_number" => $_POST['room_number']], ["user_id" => $_POST['user_id']]);
            }
            header('location: boarders.php');
            exit;
        }
    }

    // Restore User
    else if (isset($_POST['user_id']) && isset($_POST['restore'])) {
        $dbReference->updateData("tbl_payments_months", ["active" => "1"], ["user_id" => $_POST['user_id']]);
        $dbReference->updateData("tbl_payments_history", ["active" => "1"], ["user_id" => $_POST['user_id']]);

        $dbReference->deleteData("tbl_users_room", ["user_id" => $_POST['user_id']]);
        $dbReference->updateData("tbl_users", ["active" => "1"], ["user_id" => $_POST['user_id']]);
        header('location: boarders.php');
        exit;
    }

    // Delete User
    else if (isset($_POST['user_id'])) {

        $dbReference->updateData("tbl_payments_months", ["active" => "0"], ["user_id" => $_POST['user_id']]);
        $dbReference->updateData("tbl_payments_history", ["active" => "0"], ["user_id" => $_POST['user_id']]);

        $roomInfo = $dbReference->getData("tbl_users_room", "*", ["user_id" => $_POST['user_id']]);
        $roomId = $roomInfo[0]['room_id'];

        $roomFilled = ($dbReference->getData("tbl_rooms_data", "room_filled", ["room_id" => $roomId]))[0]['room_filled'] - 1;
        $dbReference->updateData("tbl_rooms_data", ["room_filled" => $roomFilled], ["room_id" => $roomId]);

        if ($roomInfo) {
            $dbReference->insertData("tbl_room_tracking", ["user_id" => $_POST['user_id'], "room_type" => $roomInfo[0]['room_type'], "room_category" => $roomInfo[0]['room_category'], "room_number" => $roomInfo[0]['room_number']], ["user_id" => $_POST['user_id']]);
        }

        $dbReference->updateData("tbl_users_room", ["active" => "0"], ["user_id" => $_POST['user_id']]);
        $dbReference->updateData("tbl_users", ["active" => "0"], ["user_id" => $_POST['user_id']]);
        header('location: boarders.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Boarders</title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />

    <style>
        .room-info {
            padding: 10px;
        }

        .room-details {
            display: block;
            font-size: 12px;
            color: #777;
        }

        .room-category {
            margin-right: 5px;
        }

        .room-number {
            font-weight: bold;
        }
    </style>
</head>

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 bg-primary position-absolute w-100"></div>

    <?php include '../includes/navbar.php' ?>
    <main class="main-content position-relative border-radius-lg ">
        <?php include '../includes/header.php' ?>
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6>Boarders</h6>
                                <?php if ($userAcessStatus) { ?>
                                    <div class="m-t-0 text-right">
                                        <span class="btn btn-default waves-effect waves-light" id="addUser" style="background-color: green; color: white;"><i class="fa fa-plus"></i> Add</span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="row p-3">
                                <div class="col-md-4 mb-2">
                                    <label for="collegeOfficeSelect" class="form-label">Select a Purpose Type:</label>
                                    <select class="form-select" id="collegeOfficeSelect">
                                        <option value="" selected disabled>Select a Purpose Type</option>
                                        <option value="school">School</option>
                                        <option value="college">College</option>
                                        <option value="office">Office</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label for="nameSearch" class="form-label">Search by Name:</label>
                                        <input type="text" class="form-control" id="nameSearch" placeholder="Enter name">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label for="roomSearch" class="form-label">Search by Room Info:</label>
                                        <input type="text" class="form-control" id="roomSearch" placeholder="Enter room number">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Number</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Purpose Type</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Room Info</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Address</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Payments</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Room</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($userList as $user) :
                                            $userRoomData = $dbReference->getData("tbl_users_room", "*", ["user_id" => $user['user_id']]); ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            <img src="../assets/img/user.png" class="avatar avatar-sm me-3" alt="user1">
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo $user['name']; ?></h6>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <span class="text-secondary text-xs font-weight-bold"><?php echo $user['number']; ?></span>
                                                    </div>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold"><?php echo $user['location_type']; ?></span>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold"><?php echo $userRoomData ? $userRoomData[0]['room_number'] . " (" . $userRoomData[0]['room_type'] . ")" : ''; ?></span>
                                                </td>

                                                <!-- Address Status -->
                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $addressStatus = $dbReference->getData("tbl_addresses", "user_id", ["user_id" => $user["user_id"]]);

                                                    $statusBadgeClass = ($addressStatus) ? 'bg-gradient-success' : 'bg-gradient-danger';
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($addressStatus) ? '' : ' AddressStatus';
                                                                                echo ($addressStatus && $userAcessStatus) ? ' addressUpdateStatus' : ''; ?>" data-user_id="<?php echo $user['user_id']; ?>" style="width: 100px; cursor: pointer;">
                                                        <?php echo ($addressStatus) ? 'Updated' : 'Update'; ?>
                                                    </span>
                                                </td>

                                                <!-- Payments Status -->
                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $paymentsStatus = $dbReference->getData("tbl_payments", "user_id", ["user_id" => $user["user_id"]]);

                                                    $statusBadgeClass = ($paymentsStatus) ? 'bg-gradient-success' : 'bg-gradient-danger';
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($paymentsStatus) ? '' : ' paymetsStatus';
                                                                                echo ($paymentsStatus && $userAcessStatus) ? ' paymentUpdateStatus' : ''; ?>" data-user_id="<?php echo $user['user_id']; ?>" style="width: 100px; cursor: pointer;">
                                                        <?php echo ($paymentsStatus) ? 'Updated' : 'Update'; ?>
                                                    </span>
                                                </td>

                                                <!-- Room Status -->
                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $roomStatus = $dbReference->getData("tbl_users_room", "user_id", ["user_id" => $user["user_id"]]);

                                                    $statusBadgeClass = ($roomStatus) ? 'bg-gradient-success' : 'bg-gradient-danger';
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($roomStatus) ? '' : ' roomStatus';
                                                                                echo ($roomStatus && $userAcessStatus) ? ' roomUpdateStatus' : ''; ?>" data-user_id="<?php echo $user['user_id']; ?>" style="width: 100px; cursor: pointer;">
                                                        <?php echo ($roomStatus) ? 'Updated' : 'Update'; ?>
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    <span class="edit-row" style="cursor: pointer;" onclick="createForm('view-boarders.php', 'POST', {'user_id': <?php echo $user['user_id'] ?>})" data-toggle="tooltip" data-placement="top">
                                                        <i class="fas fa-eye" style="color: #f05050;"></i>
                                                    </span>
                                                    <?php if ($userAcessStatus) {
                                                        $roomTrackingStatus = $dbReference->getData("tbl_room_tracking", "user_id", ["user_id" => $user["user_id"]]);
                                                        $statusBadgeColor = ($roomTrackingStatus) ? '#f05050' : '#2DCEBE'; ?>
                                                        &nbsp;&nbsp;

                                                        <span class="edit-row <?php echo ($roomTrackingStatus) ? ' trackStatus' : ''; ?>" data-user_id="<?php echo $user['user_id']; ?>" style=" cursor: pointer;">
                                                            <i class="fas fa-bed" style="color: <?php echo $statusBadgeColor ?>"></i>
                                                        </span>

                                                        &nbsp;&nbsp;
                                                        <span class="edit-row updateUser" style="cursor: pointer;" data-toggle="tooltip" data-placement="top" data-user_id="<?php echo $user['user_id']; ?>" data-name="<?php echo $user['name']; ?>" data-number="<?php echo $user['number']; ?>" data-location_type="<?php echo $user['location_type']; ?>" data-subject="<?php echo $user['subject']; ?>" data-year="<?php echo $user['year']; ?>" data-semester="<?php echo $user['semester']; ?>" data-organizationname="<?php echo $user['organizationname']; ?>">
                                                            <i class="fas fa-edit" style="color: #29b6f6;"></i>
                                                        </span>
                                                        &nbsp;&nbsp;
                                                        <span class="edit-row" style="cursor: pointer;" onclick="confirmDelete(<?php echo $user['user_id']; ?>)" data-toggle="tooltip" data-placement="top">
                                                            <i class="fas fa-trash" style="color: #f05050;"></i>
                                                        </span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AddUser Modal -->
        <div class="modal fade" id="AddUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">User Information</h5>
                    </div>
                    <div class="modal-body">
                        <form method="post" class="row g-3">

                            <!-- User Information -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="add-name">Name:</label>
                                    <input type="text" class="form-control" name="add-name" id="add-name" placeholder="Enter name">
                                </div>
                                <div class="form-group">
                                    <label for="add-number">Number:</label>
                                    <input type="text" class="form-control" name="add-number" id="add-number" placeholder="Enter number">
                                </div>
                                <div class="form-group">
                                    <label for="add-location_type">Purpose Type:</label>
                                    <div class="input-group">
                                        <select class="form-control" name="add-location_type" id="add-location_type">
                                            <option value="" selected disabled>Select a Purpose Type</option>
                                            <option value="school">School</option>
                                            <option value="College">College</option>
                                            <option value="Office">Office</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Second column -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="add-subject">Subject:</label>
                                    <input type="text" class="form-control" name="add-subject" id="add-subject" placeholder="Enter subject">
                                </div>

                                <div class="form-group">
                                    <label for="add-year">Year:</label>
                                    <div class="input-group">
                                        <select class="form-control" name="add-year" id="add-year">
                                            <option value="" selected disabled>Select Year</option>
                                            <option value="N/A">N/A</option>
                                            <option value="1st">1st</option>
                                            <option value="2nd">2nd</option>
                                            <option value="3rd">3rd</option>
                                            <option value="4th">4th</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="add-semester">Semester:</label>
                                    <div class="input-group">
                                        <select class="form-control" name="add-semester" id="add-semester">
                                            <option value="" selected disabled>Select Semester</option>
                                            <option value="N/A">N/A</option>
                                            <option value="1st">1st</option>
                                            <option value="2nd">2nd</option>
                                            <option value="3rd">3rd</option>
                                            <option value="4th">4th</option>
                                            <option value="5th">5th</option>
                                            <option value="6th">6th</option>
                                            <option value="7th">7th</option>
                                            <option value="8th">8th</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="add-organizationname">Organization / College Name:</label>
                                <input type="text" class="form-control" name="add-organizationname" id="add-organizationname" placeholder="Enter Organization / College Name">
                            </div>

                            <!-- Address Information -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="add-street_address">Street Address:</label>
                                    <input type="text" class="form-control" name="add-street_address" id="add-street_address" placeholder="Enter street address">
                                </div>
                                <div class="form-group">
                                    <label for="add-city">City:</label>
                                    <input type="text" class="form-control" name="add-city" id="add-city" placeholder="Enter city">
                                </div>
                                <div class="form-group">
                                    <label for="add-state">State:</label>
                                    <input type="text" class="form-control" name="add-state" id="add-state" placeholder="Enter state">
                                </div>
                                <div class="form-group">
                                    <label for="add-postal_code">Postal Code:</label>
                                    <input type="text" class="form-control" name="add-postal_code" id="add-postal_code" placeholder="Enter postal code">
                                </div>
                            </div>

                            <!-- Payments Information -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="add-payment_date">Payment Date:</label>
                                    <input type="date" class="form-control" name="add-payment_date" id="add-payment_date">
                                </div>
                                <div class="form-group">
                                    <label for="add-payment_due_date">Due Date:</label>
                                    <input type="date" class="form-control" name="add-payment_due_date" id="add-payment_due_date">
                                </div>
                                <div class="form-group">
                                    <label for="add-total_payment_amount">Room Rent Amount:</label>
                                    <input type="text" class="form-control" name="add-total_payment_amount" id="add-total_payment_amount" placeholder="Enter Rent amount">
                                </div>
                            </div>

                            <!-- Room Information -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="add-room_type_dropdown">Building Type</label>
                                    <select class="form-control" name="add-room_type" id="add-room_type_dropdown">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="add-room_category_dropdown">Room Category:</label>
                                    <select class="form-control" name="add-room_category" id="add-room_category_dropdown"></select>
                                </div>
                                <div class="form-group">
                                    <label for="add-room_number_dropdown">Room Number:</label>
                                    <select class="form-control" name="add-room_number" id="add-room_number_dropdown"></select>
                                </div>
                            </div>

                            <button type="submit" name="AddUserSubmit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Modal -->
        <div class="modal fade" id="UpdateUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">User Information</h5>
                    </div>
                    <div class="modal-body">
                        <form method="post" class="row g-3">
                            <!-- Hidden input for user ID -->
                            <input type="hidden" name="user_id" id="user_user_id" value="">

                            <!-- First column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name:</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter name">
                                </div>
                                <div class="form-group">
                                    <label for="number">Number:</label>
                                    <input type="text" class="form-control" name="number" id="number" placeholder="Enter number">
                                </div>
                                <div class="form-group">
                                    <label for="location_type">Purpose Type:</label>
                                    <div class="input-group">
                                        <select class="form-control" name="location_type" id="location_type">
                                            <option value="" selected disabled>Select a Purpose Type</option>
                                            <option value="school">School</option>
                                            <option value="College">College</option>
                                            <option value="Office">Office</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Second column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject">Subject:</label>
                                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Enter subject">
                                </div>

                                <div class="form-group">
                                    <label for="year">Year:</label>
                                    <div class="input-group">
                                        <select class="form-control" name="year" id="year">
                                            <option value="" selected disabled>Select Year</option>
                                            <option value="N/A">N/A</option>
                                            <option value="1st">1st</option>
                                            <option value="2nd">2nd</option>
                                            <option value="3rd">3rd</option>
                                            <option value="4th">4th</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="semester">Semester:</label>
                                    <div class="input-group">
                                        <select class="form-control" name="semester" id="semester">
                                            <option value="" selected disabled>Select Semester</option>
                                            <option value="N/A">N/A</option>
                                            <option value="1st">1st</option>
                                            <option value="2nd">2nd</option>
                                            <option value="3rd">3rd</option>
                                            <option value="4th">4th</option>
                                            <option value="5th">5th</option>
                                            <option value="6th">6th</option>
                                            <option value="7th">7th</option>
                                            <option value="8th">8th</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name">Organization / College Name:</label>
                                <input type="text" class="form-control" name="organizationname" id="organizationname" placeholder="Enter Organization / College Name">
                            </div>

                            <button type="submit" name="UserSubmit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Modal -->
        <div class="modal fade" id="AddressModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Address Information</h5>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <!-- Hidden input for user ID -->
                            <input type="hidden" name="user_id" id="address_user_id" value="">

                            <div class="form-group">
                                <label for="street_address">Street Address:</label>
                                <input type="text" class="form-control" name="street_address" id="street_address" placeholder="Enter street address">
                            </div>
                            <div class="form-group">
                                <label for="city">City:</label>
                                <input type="text" class="form-control" name="city" id="city" placeholder="Enter city">
                            </div>
                            <div class="form-group">
                                <label for="state">State:</label>
                                <input type="text" class="form-control" name="state" id="state" placeholder="Enter state">
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Postal Code:</label>
                                <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="Enter postal code">
                            </div>

                            <button type="submit" name="AddressSubmit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Modal -->
        <div class="modal fade" id="PaymentsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Payments Information</h5>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <!-- Hidden input for user ID -->
                            <input type="hidden" name="user_id" id="payments_user_id" value="">

                            <div class="form-group">
                                <label for="payment_date">Payment Date:</label>
                                <input type="date" class="form-control" name="payment_date" id="payment_date">
                            </div>

                            <div class="form-group">
                                <label for="payment_due_date">Due Date:</label>
                                <input type="date" class="form-control" name="payment_due_date" id="payment_due_date">
                            </div>
                            <div class="form-group">
                                <label for="total_payment_amount">Room Rent Amount:</label>
                                <input type="text" class="form-control" name="total_payment_amount" id="total_payment_amount" placeholder="Enter Rent amount">
                            </div>
                            <button type="submit" name="PaymetsSubmit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Modal -->
        <div class="modal fade" id="RoomModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Room Information</h5>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <!-- Hidden input for user ID -->
                            <input type="hidden" name="user_id" id="room_user_id" value="">

                            <div class="form-group">
                                <label for="room_type">Building Type</label>
                                <select class="form-control" name="room_type" id="room_type_dropdown">
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="room_category">Room Category:</label>
                                <select class="form-control" name="room_category" id="room_category_dropdown"></select>
                            </div>

                            <div class="form-group">
                                <label for="room_number">Room Number:</label>
                                <select class="form-control" name="room_number" id="room_number_dropdown"></select>
                            </div>

                            <button type="submit" name="RoomSubmit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Tracking User Modal -->
        <div class="modal fade" id="RoomTrackingUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Rooms Information</h5>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Previous Rooms Information</h6>
                        <ul id="roomListContainer" class="list-group"></ul>
                    </div>
                </div>
            </div>
        </div>

    </main>


    <!--   Core JS Files   -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="../assets/js/main/boarders.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if ($error_msg) { ?>
                Swal.fire({
                    title: "Data Missing",
                    text: "Please fill in all the required information before submitting.",
                    icon: "error",
                });
            <?php } ?>
        });
    </script>


    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script>

</body>

</html>