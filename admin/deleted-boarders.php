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
$userList = $dbReference->getData("tbl_users", "*", ["active" => "0"]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete User
    if (isset($_POST['user_id'])) {
        $dbReference->deleteData("tbl_addresses", ["user_id" => $_POST['user_id']]);

        $dbReference->deleteData("tbl_payments", ["user_id" => $_POST['user_id']]);
        $dbReference->deleteData("tbl_payments_months", ["user_id" => $_POST['user_id']]);
        $dbReference->deleteData(" tbl_payments_history", ["user_id" => $_POST['user_id']]);

        $dbReference->deleteData("tbl_users_room", ["user_id" => $_POST['user_id']]);
        $dbReference->deleteData("tbl_room_tracking", ["user_id" => $_POST['user_id']]);

        $dbReference->deleteData("tbl_users", ["user_id" => $_POST['user_id']]);
        header('location: deleted-boarders.php');
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
                                <h6>Left Boarders</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Number</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Location Type</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($userList as $user) : ?>
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

                                                <td class="text-center">
                                                    <span class="edit-row" style="cursor: pointer;" onclick="createForm('view-boarders.php', 'POST', {'user_id': <?php echo $user['user_id'] ?>})" data-toggle="tooltip" data-placement="top">
                                                        <i class="fas fa-eye" style="color: #f05050;"></i>
                                                    </span>
                                                    &nbsp;&nbsp;
                                                    <span class="edit-row paymentTransaction" style="cursor: pointer;" data-user_id="<?php echo $user['user_id']; ?>" data-active="0" data-toggle="tooltip" data-placement="top">
                                                        <i class="fa fa-credit-card" style="color: #29b6f6;"></i>
                                                    </span>
                                                    &nbsp;&nbsp;
                                                    <?php if ($userAcessStatus) { ?>
                                                        <span class="edit-row" style="cursor: pointer;" onclick="confirmRestore(<?php echo $user['user_id'] ?>)" data-toggle="tooltip" data-placement="top">
                                                            <i class="fa fa-refresh" style="color: #29b6f6;"></i>
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

        <!-- Payments Transaction Modal -->
        <div class="modal fade" id="PaymentTransactionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Transaction Information</h5>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Newest</h6>
                        <ul id="yourListContainer" class="list-group"></ul>
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

    <script src="../assets/js/main/payments.js"></script>

    <script>
        function createForm(url, method, params) {
            const form = document.createElement('form');
            form.method = method;
            form.action = url;

            for (const key in params) {
                if (params.hasOwnProperty(key)) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = params[key];
                    form.appendChild(input);
                }
            }

            document.body.appendChild(form);
            form.submit();
        }

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
                    createForm('deleted-boarders.php', 'POST', {
                        'user_id': userId
                    });
                }
            });
        }

        function confirmRestore(userId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will restore the boarder!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    createForm('boarders.php', 'POST', {
                        'user_id': userId,
                        'restore': '1'
                    });
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