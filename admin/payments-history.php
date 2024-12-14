<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

$userprofile = $_SESSION['username'];
$userAcessStatus = $_SESSION['acess'];
$accountingYearId = $_SESSION['accountingYearId'];

if ($userprofile != true) {
    header('location: login.php');
    exit;
}

// Get Users Data
$transactionData = $dbReference->joinTables("tbl_payments_history", "tbl_users", "tbl_payments_history.user_id", "tbl_users.user_id", ["tbl_payments_history.active " => 1], "payment_id", "DESC");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Payment History</title>
    <!-- Fonts and icons -->
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
        /* Custom CSS styles for the table */
        .table thead th {
            background-color: #f6f6f6;
            border-top: none;
            border-bottom: 1px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f2f2f2;
        }

        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 10px;
            cursor: pointer;
            border: 2px solid #fff;
        }

        .bg-pending {
            background-color: #FFA500;
        }

        .bg-paid {
            background-image: linear-gradient(310deg, #2dce89 0%, #2dcecc 100%);
        }

        .bg-gray {
            background-color: gray;
        }

        .bg-pink {
            background-color: pink;
        }

        .bg-red {
            background-color: red;
        }

        .bg-blue {
            background-color: blue;
        }

        .selected {
            border: 2px solid black;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">
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
                                <h6>Transaction</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="row p-3">
                                <div class="col-md-6 mb-2">
                                    <label for="monthSelect" class="form-label">Search by Months:</label>
                                    <select class="form-select" id="monthSelect">
                                        <option value="" selected disabled>Select a Month</option>
                                        <option value="-01-" <?= date('m') == '01' ? 'selected' : '' ?>>January</option>
                                        <option value="-02-" <?= date('m') == '02' ? 'selected' : '' ?>>February</option>
                                        <option value="-03-" <?= date('m') == '03' ? 'selected' : '' ?>>March</option>
                                        <option value="-04-" <?= date('m') == '04' ? 'selected' : '' ?>>April</option>
                                        <option value="-05-" <?= date('m') == '05' ? 'selected' : '' ?>>May</option>
                                        <option value="-06-" <?= date('m') == '06' ? 'selected' : '' ?>>June</option>
                                        <option value="-07-" <?= date('m') == '07' ? 'selected' : '' ?>>July</option>
                                        <option value="-08-" <?= date('m') == '08' ? 'selected' : '' ?>>August</option>
                                        <option value="-09-" <?= date('m') == '09' ? 'selected' : '' ?>>September</option>
                                        <option value="-10-" <?= date('m') == '10' ? 'selected' : '' ?>>October</option>
                                        <option value="-11-" <?= date('m') == '11' ? 'selected' : '' ?>>November</option>
                                        <option value="-12-" <?= date('m') == '12' ? 'selected' : '' ?>>December</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="yearSelect" class="form-label">Search by Years:</label>
                                    <select class="form-select" id="yearSelect">
                                        <option value="" selected disabled>Select a Year</option>
                                        <option value="-2021" <?= date('Y') == 2021 ? 'selected' : '' ?>>2021</option>
                                        <option value="-2022" <?= date('Y') == 2022 ? 'selected' : '' ?>>2022</option>
                                        <option value="-2023" <?= date('Y') == 2023 ? 'selected' : '' ?>>2023</option>
                                        <option value="-2024" <?= date('Y') == 2024  ? 'selected' : '' ?>>2024</option>
                                        <option value="-2025" <?= date('Y') == 2025 ? 'selected' : '' ?>>2025</option>
                                    </select>
                                </div>
                            </div>

                            <div style="text-align: center;" id="processing">
                                <img src="../assets/img/loader.gif" alt="" style="width: 60px;">
                            </div>


                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0" id="payments-table" style="display: none;">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Name</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Number</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Payment Amount</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Payment Date</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Color</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Additional Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactionData as $data) : ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            <img src="../assets/img/user.png" class="avatar avatar-sm me-3" alt="user1">
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo $data['name']; ?></h6>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?php echo $data['number']; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?php echo $data['total_payment_amount']; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?php echo $helper->getFormatedDate($data['payment_date']); ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <div class="color-option bg-<?php echo $data['payment_color'] ? $data['payment_color'] : 'paid'; ?>" id="<?php echo $data['payment_color']; ?>"></div>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?php echo $data['additional_comments']; ?>
                                                    </span>
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
    </main>

    <!-- Core JS Files -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            filterPayments();
            $("#monthSelect, #yearSelect").on("input change", function() {
                $("#processing").show();
                $("#payments-table").hide();

                setTimeout(() => {
                    filterPayments();
                }, 1000);
            });

            function filterPayments() {
                $("#processing").hide();

                var monthSelect = $("#monthSelect").val();
                var yearSelect = $("#yearSelect").val();

                $("tbody tr").each(function() {
                    var showRow = true;

                    var month = $(this).find("td:eq(3) span").text().toLowerCase();
                    var year = $(this).find("td:eq(3) span").text();

                    if (monthSelect !== "" && monthSelect !== null) {
                        showRow = showRow && month.includes(monthSelect);
                    }

                    if (yearSelect !== "" && yearSelect !== null) {
                        showRow = showRow && year.includes(yearSelect);
                    }

                    $(this).toggle(showRow);
                });

                $("#payments-table").show();
            }
        });
    </script>

    <script>
        function mydate() {
            d = new Date(document.getElementById("dt").value);
            dt = d.getDate();
            mn = d.getMonth();
            mn++;
            yy = d.getFullYear();
            document.getElementById("ndt").value = dt + "/" + mn + "/" + yy
            document.getElementById("ndt").hidden = false;
            document.getElementById("dt").hidden = true;
        }
    </script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script>

</body>

</html>