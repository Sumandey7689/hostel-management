<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = New Helper();

$userprofile = $_SESSION['username'];
$userAcessStatus = $_SESSION['acess'];
$accountingYearId = $_SESSION['accountingYearId'];

if ($userprofile != true) {
    header('location: login.php');
    exit;
}

$userProfileData = [];
$userAddressData = [];
$userPaymentsData = [];
$userRoomData = [];

// Get Users Data
if (isset($_POST['user_id'])) {
    $userProfileData = $dbReference->getData("tbl_users", "*", ["user_id" => $_POST['user_id']]);
    $userAddressData = $dbReference->getData("tbl_addresses", "*", ["user_id" => $_POST['user_id']]);
    $userPaymentsData = $dbReference->getData("tbl_payments", "*", ["user_id" => $_POST['user_id']]);
    $userRoomData = $dbReference->joinTables("tbl_users_room", "tbl_rooms_data", "tbl_users_room.room_id", "tbl_rooms_data.room_id", ["tbl_users_room.user_id" => $_POST['user_id']]);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Master User</title>
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
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <h4>User Profile</h4>
                                    </div>
                                    <div class="card-body align-items-center mb-0">
                                        <div class="card-body align-items-center mb-0">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5 class="card-title">Personal Information</h5>
                                                    <div class="form-group">
                                                        <label for="name">Name:</label>
                                                        <input type="text" class="form-control" value="<?php echo $userProfileData ? $userProfileData[0]['name'] : ''; ?>" id="name" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="number">Number:</label>
                                                        <input type="text" class="form-control" value="<?php echo $userProfileData ? $userProfileData[0]['number'] : ''; ?>" id="number" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="location_type">Purpose Type:</label>
                                                        <input type="text" class="form-control" value="<?php echo $userProfileData ? $userProfileData[0]['location_type'] . ', ' . $userProfileData[0]['organizationname'] : ''; ?>" id="location_type" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="subject">Organization / College:</label>
                                                        <textarea class="form-control" id="" readonly><?php echo $userProfileData ? $userProfileData[0]['subject'] : ''; echo $userProfileData[0]['year'] ? ', ' . $userProfileData[0]['year'] . ' year' : ''; echo $userProfileData[0]['semester'] ? ', ' . $userProfileData[0]['semester'] . ' Semester' : ''; ?> </textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">

                                                    <h5 class="card-title">Room Information</h5>
                                                    <div class="form-group">
                                                        <label for="room_category">Building Type:</label>
                                                        <input type="text" class="form-control" id="room_category" value="<?php echo $userRoomData ? $userRoomData[0]['room_type'] : ''; ?>" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="room_category">Room Category:</label>
                                                        <input type="text" class="form-control" id="room_category" value="<?php echo $userRoomData ? $userRoomData[0]['room_category'] : ''; ?>" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="room_number">Room Number:</label>
                                                        <input type="text" class="form-control" value="<?php echo $userRoomData ? $userRoomData[0]['room_number'] : ''; ?>" id="room_number" readonly>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="full_address">Full Address:</label>
                                                        <textarea class="form-control" id="full_address" readonly><?php echo $userAddressData ? $userAddressData[0]['street_address'] . ', ' . $userAddressData[0]['city'] . ', ' . $userAddressData[0]['state'] . ', ' . $userAddressData[0]['postal_code'] : ''; ?></textarea>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5 class="card-title">Payment Information</h5>

                                                    <div class="form-group">
                                                        <label for="payment_due_date">Next Due Date:</label>
                                                        <input type="text" class="form-control" value="<?php echo $userPaymentsData ? $helper->getFormatedDate($userPaymentsData[0]['payment_due_date']) : ''; ?>" id="payment_due_date" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="total_payment_amount">Room Rent Amount:</label>
                                                        <input type="text" class="form-control" value="<?php echo $userPaymentsData ? $userPaymentsData[0]['total_payment_amount'] : ''; ?>" id="total_payment_amount" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5 class="card-title">Additional Information</h5>
                                                    <div class="form-group">
                                                        <label for="date_of_joining">Date of Joining:</label>
                                                        <input type="text" class="form-control" value="<?php echo $userPaymentsData ? $helper->getFormatedDate($userPaymentsData[0]['payment_date']) : ''; ?>" id="payment_date" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="total_payment_amount">Additional Comments:</label>
                                                        <input type="text" class="form-control" value="<?php echo $userPaymentsData ? $userPaymentsData[0]['additional_comments'] : ''; ?>" id="additional_comments" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        function confirmDelete(userId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "boarders.php?user_id=" + userId;
                }
            });
        }
    </script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script>

</body>

</html>