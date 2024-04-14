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
$userList = $dbReference->getData("tbl_payments_months", "*", ["active" => "1"]);

if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = md5(uniqid(rand(), true));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Payments
    if (isset($_POST['form_token']) && $_POST['form_token'] === $_SESSION['form_token']) {
        unset($_SESSION['form_token']);
        if (isset($_POST['PaymentsSubmit'])) {
            if (!empty($_POST['user_id']) && !empty($_POST['payment_date']) && !empty($_POST['payment_month']) && !empty($_POST['total_payment_amount'])) {

                $baseDate = new DateTime(($dbReference->getData("tbl_payments", "payment_due_date", ["user_id" => $_POST['user_id']]))[0]['payment_due_date']);
                if ($baseDate->format('Y-m') <= date('Y-m', strtotime($_POST['payment_date']))) {
                    $updatedDate = $baseDate->add(new DateInterval('P1M'))->format('Y-m-d');
                } else {
                    $updatedDate = $baseDate->format('Y-m-d');
                }
                $dbReference->updateData("tbl_payments", ["payment_due_date" => $updatedDate], ["user_id" => $_POST["user_id"]]);

                $dbReference->insertData("tbl_payments_history", ["user_id" => $_POST['user_id'], "payment_date" => $_POST['payment_date'], "total_payment_amount" => ($_POST['total_payment_amount']) + ($_POST['late_payment_fees']), "payment_month" => $_POST['payment_month'], "payment_color" => $_POST['payment_color'], "additional_comments" => $_POST['additional_comments']]);

                $dbReference->updateData("tbl_payments_months", [strtolower($_POST['payment_month']) => "1"], ["user_id" => $_POST['user_id']]);
                header('location: payments.php');
                exit;
            }
        }
    }
}

function getPaymentDate($dbReference, $user_id, $month)
{
    return $dbReference->getData("tbl_payments_history", "payment_date, payment_color", ["user_id" => $user_id, "payment_month" => $month], "payment_date");
}

function getDueDateBoolean($dbReference, $userId)
{
    $paymentData = $dbReference->getData("tbl_payments", "*", ["user_id" => $userId]);

    if (!empty($paymentData)) {
        $paymentDueDate = $paymentData[0]['payment_due_date'];

        $adjustedDueDate = strtotime($paymentDueDate . " + 0 days");

        $currentDate = time();
        if ($currentDate > $adjustedDueDate) {
            return "true";
        } else {
            return "false";
        }
    } else {
        return "false";
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
    <title>Payments</title>
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
        .bg-pending {
            background-color: #FFA500;
        }

        .bg-paid {
            background-color: #00FF00;
        }

        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 10px;
            cursor: pointer;
            border: 2px solid #fff;
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
                                <h6>Payments</h6>
                            </div>
                        </div>
                        <div class="card-body px-2 pt-0 pb-2">
                            <div class="row p-3">
                                <div class="col-md-4 mb-2">
                                    <label for="monthSelect" class="form-label">Search by Months:</label>
                                    <select class="form-select" id="monthSelect">
                                        <option value="" selected disabled>Select a Month</option>
                                        <option value="3">January</option>
                                        <option value="4">February</option>
                                        <option value="5">March</option>
                                        <option value="6">April</option>
                                        <option value="7">May</option>
                                        <option value="8">June</option>
                                        <option value="9">July</option>
                                        <option value="10">August</option>
                                        <option value="11">September</option>
                                        <option value="12">October</option>
                                        <option value="13">November</option>
                                        <option value="14">December</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label for="nameSearch" class="form-label">Search by Name:</label>
                                        <input type="text" class="form-control" id="nameSearch" placeholder="Enter Name">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label for="numberSearch" class="form-label">Search by Number:</label>
                                        <input type="text" class="form-control" id="numberSearch" placeholder="Enter Number">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive p-2">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Number</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date of joining</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">January</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">February</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">March</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">April</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">May</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">June</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">July</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">August</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">September</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">October</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">November</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">December</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($userList as $user) :
                                            $currentUserName = $dbReference->getData("tbl_users", "name, number", ["user_id" => $user['user_id']]);
                                            $dateOfJoining =  ($dbReference->getData("tbl_payments", "payment_date", ["user_id" => $user['user_id']]))[0]["payment_date"] ?? '';
                                            $dateOfJoining = $dateOfJoining != '' ? $helper->getFormatedDate($dateOfJoining) : '';

                                            $dueDate = getDueDateBoolean($dbReference, $user['user_id']); ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            <img src="../assets/img/user.png" class="avatar avatar-sm me-3" alt="user1">
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo $currentUserName[0]['name'] ?></h6>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo $currentUserName[0]['number'] ?></h6>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <h6 class="text-xs font-weight-bold"><?php echo $dateOfJoining ?></h6>
                                                </td>

                                                <!-- Month Status -->
                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['january'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "January")) && (getPaymentDate($dbReference, $user['user_id'], "January")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "January")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="January" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "January")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <!-- Month Status -->
                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['february'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "February")) && (getPaymentDate($dbReference, $user['user_id'], "February")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "February")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="February" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "February")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['march'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "March")) && (getPaymentDate($dbReference, $user['user_id'], "March")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "March")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="March" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "March")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['april'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "April")) && (getPaymentDate($dbReference, $user['user_id'], "April")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "April")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="April" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "April")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['may'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "May")) && (getPaymentDate($dbReference, $user['user_id'], "May")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "May")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="May" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "May")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['june'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "June")) && (getPaymentDate($dbReference, $user['user_id'], "June")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "June")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="June" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "June")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['july'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "July")) && (getPaymentDate($dbReference, $user['user_id'], "July")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "July")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="July" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "July")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['august'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "August")) && (getPaymentDate($dbReference, $user['user_id'], "August")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "August")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="August" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "August")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['september'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "September")) && (getPaymentDate($dbReference, $user['user_id'], "September")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "September")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="September" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "September")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['october'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "October")) && (getPaymentDate($dbReference, $user['user_id'], "October")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "October")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="October" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "October")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['november'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "November")) && (getPaymentDate($dbReference, $user['user_id'], "November")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "November")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="November" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "November")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <?php
                                                    $status = $user['december'] ? '1' : '0';
                                                    if ((getPaymentDate($dbReference, $user['user_id'], "December")) && (getPaymentDate($dbReference, $user['user_id'], "December")[0]['payment_color'])) {
                                                        $statusBadgeClass = "bg-" . getPaymentDate($dbReference, $user['user_id'], "December")[0]['payment_color'];
                                                    } else {
                                                        $statusBadgeClass =  ($status) ? 'bg-gradient-success' : 'bg-pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-sm <?php echo $statusBadgeClass;
                                                                                echo ($status) ? '' : ' paymentModel'; ?>" data-user_id="<?php echo $user['user_id']; ?>" data-user_month="December" style="width: 80px; cursor: pointer;">
                                                        <?php echo ($status) ? $helper->getFormatedDate(getPaymentDate($dbReference, $user['user_id'], "December")[0]['payment_date']) : 'Pending'; ?>
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    <span class="edit-row paymentTransaction" style="cursor: pointer;" data-user_id="<?php echo $user['user_id']; ?>" data-active="1" data-toggle="tooltip" data-placement="top">
                                                        <i class="fa fa-credit-card" style="color: #29b6f6;"></i>
                                                    </span>
                                                </td>

                                                <td style="display: none;">
                                                    <span class="duehit"><?php echo $dueDate ?></span>
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
                            <input type="hidden" name="user_id" id="user_id" value="">
                            <input type="hidden" name="payment_month" id="payment_month">
                            <input type="hidden" name="payment_color" id="color_name">
                            <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">

                            <div class="form-group">
                                <label for="payment_date">Payment Date:</label>
                                <input type="date" class="form-control" name="payment_date" id="payment_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="form-group">
                                <label for="total_payment_amount">Rent Amount:</label>
                                <input type="number" class="form-control" name="total_payment_amount" id="total_payment_amount" <?php echo ($userAcessStatus) ? '' : 'readonly'; ?>>
                            </div>

                            <div class="form-group">
                                <label for="late_payment_fees">Late Payments Fees:</label>
                                <input type="number" class="form-control" name="late_payment_fees" id="late_payment_fees" <?php echo ($userAcessStatus) ? '' : 'readonly'; ?>>
                            </div>

                            <div class="form-group">
                                <label for="Additional Comments">Additional Comments:</label>
                                <textarea class="form-control" name="additional_comments" id="additional_comments" placeholder="Enter additional comments"></textarea>
                            </div>

                            <!-- Color selection buttons -->
                            <div class="form-group">
                                <label>Select Color:</label>
                                <div class="row">
                                    <div class="color-option bg-gray" id="gray"></div>
                                    <div class="color-option bg-pink" id="pink"></div>
                                    <div class="color-option bg-red" id="red"></div>
                                    <div class="color-option bg-blue" id="blue"></div>
                                </div>
                            </div>

                            <button type="submit" name="PaymentsSubmit" class="btn btn-primary">Submit</button>
                        </form>
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


    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script>

</body>

</html>