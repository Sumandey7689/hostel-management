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
$expensesData = $dbReference->getData("tbl_expenses");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addItemSubmit'])) {
        if (empty($_POST['expenses_id'])) {
            $dbReference->insertData("tbl_expenses", ["username" => $userprofile, "item" => $_POST['item'], "amount" => $_POST['amount']]);
        } else {
            $dbReference->updateData("tbl_expenses", ["username" => $userprofile, "item" => $_POST['item'], "amount" => $_POST['amount']], ["expenses_id" => $_POST['expenses_id']]);
        }
        header('location: expenses.php');
        exit;
    }

    // Delete ExpensesItem
    else if (isset($_POST['expenses_id'])) {
        $dbReference->deleteData("tbl_expenses", ["expenses_id" => $_POST['expenses_id']]);
        header('location: expenses.php');
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
                                <h6>Expenses</h6>
                                <div class="m-t-0 text-right">
                                    <span class="btn btn-default waves-effect waves-light" id="addItem" style="background-color: green; color: white;"><i class="fa fa-plus"></i> Add</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="row p-3">
                                <div class="col-md-6 mb-2">
                                    <label for="monthSelect" class="form-label">Search by Months:</label>
                                    <select class="form-select" id="monthSelect">
                                        <option value="" selected disabled>Select a Month</option>
                                        <option value="-01-">January</option>
                                        <option value="-02-">February</option>
                                        <option value="-03-">March</option>
                                        <option value="-04-">April</option>
                                        <option value="-05-">May</option>
                                        <option value="-06-">June</option>
                                        <option value="-07-">July</option>
                                        <option value="-08-">August</option>
                                        <option value="-09-">September</option>
                                        <option value="-10-">October</option>
                                        <option value="-11-">November</option>
                                        <option value="-12-">December</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="yearSelect" class="form-label">Search by Years:</label>
                                    <select class="form-select" id="yearSelect">
                                        <option value="" selected disabled>Select a Year</option>
                                        <option value="-2023">2023</option>
                                        <option value="-2024">2024</option>
                                        <option value="-2025">2025</option>
                                        <option value="-2026">2026</option>
                                    </select>
                                </div>
                            </div>

                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Submited By</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Item</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Amount</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Date</th>
                                            <?php if ($userAcessStatus) { ?>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($expensesData as $expenses) : ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            <img src="../assets/img/user.png" class="avatar avatar-sm me-3" alt="user1">
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo $expenses['username']; ?></h6>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?php echo $expenses['item']; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?php echo $expenses['amount']; ?>
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?php echo $helper->getFormatedDate($expenses['expenses_date']); ?>
                                                    </span>
                                                </td>

                                                <?php if ($userAcessStatus) { ?>
                                                    <td class="text-center">
                                                        &nbsp;&nbsp;
                                                        <span class="edit-row expensesItem" style="cursor: pointer;" data-toggle="tooltip" data-placement="top" data-expenses_id="<?php echo $expenses['expenses_id']; ?>">
                                                            <i class="fas fa-edit" style="color: #29b6f6;"></i>
                                                        </span>
                                                        &nbsp;&nbsp;
                                                        <span class="edit-row" style="cursor: pointer;" onclick="confirmDelete(<?php echo $expenses['expenses_id']; ?>)" data-toggle="tooltip" data-placement="top">
                                                            <i class="fas fa-trash" style="color: #f05050;"></i>
                                                        </span>
                                                    </td>
                                                <?php } ?>
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
        <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Expenses Information</h5>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <input type="hidden" name="expenses_id" id="expenses_id" value="">

                            <div class="form-group">
                                <label for="item">Item:</label>
                                <textarea type="text" class="form-control" name="item" id="item" placeholder="Enter Item Name"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="amount">Amount:</label>
                                <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter Amount">
                            </div>

                            <button type="submit" name="addItemSubmit" class="btn btn-primary">Submit</button>
                        </form>
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

    <script src="../assets/js/main/expenses.js"></script>

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