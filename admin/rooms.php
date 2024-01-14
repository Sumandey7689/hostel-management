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
$RoomDataExist = false;
$RoomDeleteFalse = false;

$dbReference->refreshRoomData();
// Get Users Data
$roomsData = $dbReference->getData("tbl_rooms_data");

function customRoomSort($a, $b)
{
    if ($a['room_type'] === 'New' && $b['room_type'] !== 'New') {
        return -1;
    } else {
        return 1;
    }
    return strnatcmp($a['room_type'], $b['room_type']);
}

usort($roomsData, 'customRoomSort');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['RoomSubmit'])) {
        // Add Room
        if (empty($_POST['room_id']) && !$dbReference->getData("tbl_rooms_data", "*", ["room_type" => $_POST['room_type'], "room_category" => $_POST['room_category'], "room_number" => $_POST['room_number']])) {
            if (!empty($_POST['room_type']) && !empty($_POST['room_category']) && !empty($_POST['room_number']) && !empty($_POST['room_capacity'])) {
                $dbReference->insertData("tbl_rooms_data", ["room_type" => $_POST['room_type'], "room_category" => $_POST['room_category'], "room_number" => $_POST['room_number'], "room_capacity" => $_POST['room_capacity']]);
                header('location: rooms.php');
                exit;
            }
        } else {
            // Update Room
            $updateStatus = $dbReference->getData("tbl_rooms_data", "room_id, room_filled", ["room_type" => $_POST['room_type'], "room_category" => $_POST['room_category'], "room_number" => $_POST['room_number']]);
            $roomNoExistStatus = $dbReference->getData("tbl_rooms_data", "*", ["room_type" => $_POST['room_type'], "room_category" => $_POST['room_category'], "room_number" => $_POST['room_number']]);

            if (!$roomNoExistStatus && !$updateStatus && !empty($_POST['room_type']) && !empty($_POST['room_category']) && !empty($_POST['room_number']) && !empty($_POST['room_capacity'])) {
                $dbReference->updateData("tbl_rooms_data", ["room_type" => $_POST['room_type'], "room_category" => $_POST['room_category'], "room_number" => $_POST['room_number'], "room_capacity" => $_POST['room_capacity']], ["room_id" => $_POST['room_id']]);
                header('location: rooms.php');
                exit;
            } else if ($roomNoExistStatus[0]['room_id'] == $_POST['room_id'] && $updateStatus[0]['room_id'] == $_POST['room_id'] && $updateStatus[0]['room_filled'] <= $_POST['room_capacity'] && !empty($_POST['room_type']) && !empty($_POST['room_category']) && !empty($_POST['room_number']) && !empty($_POST['room_capacity'])) {
                $dbReference->updateData("tbl_rooms_data", ["room_type" => $_POST['room_type'], "room_category" => $_POST['room_category'], "room_number" => $_POST['room_number'], "room_capacity" => $_POST['room_capacity']], ["room_id" => $_POST['room_id']]);
                header('location: rooms.php');
                exit;
            } else {
                $RoomDataExist = true;
            }
        }
    }

    // Delete Room
    else if (isset($_POST['room_id'])) {
        if ((($dbReference->getData("tbl_rooms_data", "room_filled", ["room_id" => $_POST['room_id']]))[0]['room_filled']) == 0) {
            $dbReference->deleteData("tbl_rooms_data", ["room_id" => $_POST['room_id']]);
            header('location: rooms.php');
            exit;
        } else {
            $RoomDeleteFalse = true;
        }
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
    <title>Rooms</title>
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
        .available-button {
            display: inline-block;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            background-color: #4CAF50;
            color: #fff;
            border: 2px solid #4CAF50;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .not-available-button {
            display: inline-block;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            cursor: not-allowed;
            background-color: #e74c3c;
            color: #fff;
            border: 2px solid #e74c3c;
            border-radius: 5px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
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
                                        <span class="btn btn-default waves-effect waves-light" id="addRoom" style="background-color: green; color: white;"><i class="fa fa-plus"></i>
                                            Add</span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">

                            <div class="row p-3">
                                <div class="col-md-3 mb-2">
                                    <label for="roomTypeSelect" class="form-label">Select a Building Type:</label>
                                    <select class="form-select" id="roomTypeSelect">
                                        <option value="" selected disabled>Select a Building Type</option>
                                        <option value="new">New</option>
                                        <option value="old">Old</option>
                                        <option value="gb">GB</option>
                                        <option value="ar-1">AR-1</option>
                                        <option value="ar-2">AR-2</option>
                                        <option value="mm">MM</option>
                                        <option value="an">AN</option>
                                        <option value="tv">TV</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-2">
                                    <label for="availabilitySelect" class="form-label">Select Available Type:</label>
                                    <select class="form-select" id="availabilitySelect">
                                        <option value="" selected disabled>Select Available Type</option>
                                        <option value="available">Available</option>
                                        <option value="unavailable">Unavailable</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label for="categorySearch" class="form-label">Search by Category:</label>
                                        <input type="text" class="form-control" id="categorySearch" placeholder="Enter Category">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label for="roomNumberSearch" class="form-label">Search by Room Number:</label>
                                        <input type="text" class="form-control" id="roomNumberSearch" placeholder="Enter Room Number">
                                    </div>
                                </div>

                            </div>

                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Building Type</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Room Category</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Room Number</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Availability</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Available</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Occupied</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($roomsData as $room) : ?>
                                            <tr>

                                                <td>
                                                    <div class="align-middle text-center">
                                                        <span class="text-secondary text-xs font-weight-bold">
                                                            <?php echo $room['room_type']; ?>
                                                        </span>
                                                    </div>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?php echo $room['room_category']; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?php echo $room['room_number']; ?>
                                                    </span>
                                                </td>

                                                <!-- Room Status -->
                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $roomStatus = ($room['room_capacity'] - $room['room_filled']) != 0 ? '1' : '0';

                                                    $statusBadgeClass = ($roomStatus) ? 'available-button' : 'not-available-button {';
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass; ?>" style="width: 130px;">
                                                        <?php echo ($roomStatus) ? 'Available' : 'Unavailable'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-xs font-weight-bold" style="color: #29b6f6">
                                                        <?php echo ($room['room_capacity'] - $room['room_filled']); ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-xs font-weight-bold" style="color: #e74c3c">
                                                        <?php echo $room['room_filled']; ?>
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    <span class="edit-row roomUser" style="cursor: pointer;" data-room_id="<?php echo $room['room_id']; ?>" data-toggle="tooltip" data-placement="top">
                                                        <i class="fas fa-eye" style="color: #f05050;"></i>
                                                    </span>
                                                    <?php if ($userAcessStatus) { ?>
                                                        &nbsp;&nbsp;
                                                        <span class="edit-row updateRoom" style="cursor: pointer;" data-toggle="tooltip" data-placement="top" data-room_id="<?php echo $room['room_id']; ?>" data-room_type="<?php echo $room['room_type']; ?>" data-room_category="<?php echo $room['room_category']; ?>" data-room_number="<?php echo $room['room_number']; ?>" data-room_capacity="<?php echo $room['room_capacity']; ?>">
                                                            <i class="fas fa-edit" style="color: #29b6f6;"></i>
                                                        </span>
                                                        &nbsp;&nbsp;
                                                        <span class="edit-row" style="cursor: pointer;" onclick="confirmDelete(<?php echo $room['room_id']; ?>)" data-toggle="tooltip" data-placement="top">
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
                            <input type="hidden" name="room_id" id="room_id" value="">

                            <div class="form-group">
                                <label for="room_type">Building Type:</label>
                                <select class="form-control" name="room_type" id="room_type">
                                    <option value="" selected disabled>Select Building Type</option>
                                    <option value="New">New</option>
                                    <option value="Old">Old</option>
                                    <option value="GB">GB</option>
                                    <option value="AR-1">AR-1</option>
                                    <option value="AR-2">AR-2</option>
                                    <option value="MM">MM</option>
                                    <option value="AN">AN</option>
                                    <option value="TV">TV</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="room_category">Room Category:</label>
                                <input type="text" class="form-control" name="room_category" id="room_category" placeholder="Enter Room Category">
                            </div>

                            <div class="form-group">
                                <label for="room_number">Room Number:</label>
                                <input type="text" class="form-control" name="room_number" id="room_number" placeholder="Enter Room Number">
                            </div>

                            <div class="form-group">
                                <label for="room_capacity">Room Capacity:</label>
                                <input type="number" class="form-control" name="room_capacity" id="room_capacity" placeholder="Enter Room Capacity">
                            </div>

                            <button type="submit" name="RoomSubmit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room User Modal -->
        <div class="modal fade" id="RoomUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Rooms Information</h5>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Current Boarders</h6>
                        <ul id="currentListContainer" class="list-group" style="cursor: pointer;"></ul>
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3 pt-4">Left Boarders</h6>
                        <ul id="leftListContainer" class="list-group"></ul>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if ($RoomDataExist) { ?>
                Swal.fire({
                    title: "Room Already Exists",
                    text: "The room you are trying to add already exists.",
                    icon: "error",
                });
            <?php } else if ($RoomDeleteFalse) { ?>
                Swal.fire({
                    title: "Deletion Failed",
                    text: "Room is already occupied",
                    icon: "error",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Cancel it!'
                });
            <?php } ?>
        });
    </script>

    <script src="../assets/js/main/rooms.js"></script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script>

</body>

</html>