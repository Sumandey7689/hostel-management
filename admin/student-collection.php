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
$userList = $dbReference->getData("tbl_users", "*", ["active" => "1"]);
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
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #d2d6da;
            border-radius: 0.5rem;
            padding: 0.5rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #5e72e4;
            border: none;
            color: white;
            border-radius: 4px;
            padding: -1px 8px;
            margin: 2px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 10px;
        }

        .select2-dropdown {
            border: 1px solid #d2d6da;
            border-radius: 0.5rem;
        }

        .select2-search__field {
            padding: 8px !important;
        }

        .select2-results__option {
            padding: 8px 12px;
        }

        .select2-results__option--highlighted {
            background-color: #5e72e4 !important;
        }

        .select2-container .select2-search--inline .select2-search__field {
            box-sizing: border-box;
            border: none;
            font-size: 100%;
            margin-top: -10px;
            margin-left: 5px;
            padding: 0;
            max-width: 100%;
            resize: none;
            height: 30px;
            vertical-align: bottom;
            font-family: sans-serif;
            overflow: hidden;
            word-break: keep-all;
        }

        .select2-dropdown.select2-dropdown--below {
            width: 31.3% !important;
            max-width: 41.666667%;
            min-width: 250px;
        }

        @media (max-width: 768px) {
            .select2-dropdown.select2-dropdown--below {
                width: 100% !important;
                max-width: 100%;
            }
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">
    <div class="min-height-300 bg-primary position-absolute w-100"></div>

    <?php include '../includes/navbar.php' ?>
    <main class="main-content position-relative border-radius-lg ">
        <?php include '../includes/header.php' ?>
        <div class="container-fluid py-4">
            <div class="row" style="zoom: 90%;">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6>Student Wise Collection</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="row p-3">
                                <div class="col-md-5 mb-2">
                                    <label for="studentSelect" class="form-label fw-bold">Search by Student:</label>
                                    <div class="select2-container">
                                        <select class="form-select select2-dropdown" name="students[]" id="studentSelect" multiple="multiple" style="width: 100%;">
                                            <option></option>
                                            <?php foreach ($userList as $user) : ?>
                                                <option value="<?= $user['user_id'] ?>"><?= $user['name'] ?> - <?= $user['number'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5 mb-2">
                                    <label for="yearSelect" class="form-label">Search by Years:</label>
                                    <select class="form-select" id="yearSelect">
                                        <option value="" selected disabled>Select a Year</option>
                                        <option value="2021" <?= date('Y') == 2021 ? 'selected' : '' ?>>2021</option>
                                        <option value="2022" <?= date('Y') == 2022 ? 'selected' : '' ?>>2022</option>
                                        <option value="2023" <?= date('Y') == 2023 ? 'selected' : '' ?>>2023</option>
                                        <option value="2024" <?= date('Y') == 2024  ? 'selected' : '' ?>>2024</option>
                                        <option value="2025" <?= date('Y') == 2025 ? 'selected' : '' ?>>2025</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <a href="javascript:void(0)" onclick="generatePDF()" class="btn btn-primary" style="font-size: 10px;">
                                            <i class="fas fa-file-pdf me-2"></i>Generate PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function generatePDF() {
            const student = $('#studentSelect').val() || '';
            const year = $('#yearSelect').val() || '';

            window.open(`student-payment-pdf.php?student=${student}&year=${year}`, '_blank');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#studentSelect').select2({
                placeholder: "Select students",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    <!-- Core JS Files -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>

    <!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script>

</body>

</html>