<?php 
$yearMaster = $dbReference->getData("tbl_accounting_year_master", "*", ["id" => $accountingYearId])[0];
?>

<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="../admin/dashboard.php" target="_blank">
            <img src="../assets/img/fav_logo.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">Dashboard - (<?= $yearMaster['year'] ?>)</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="../admin/dashboard.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../admin/boarders.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Boarders</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../admin/payments.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-credit-card navbar-brand-img text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Rent Payments</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../admin/payments-history.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-history navbar-brand-img text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Payments History</span>
                </a>
            </li>
            <!-- Student Wise Collection Report -->
            <li class="nav-item">
                <a class="nav-link" href="../admin/student-collection.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-history navbar-brand-img text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Student Wise Collection</span>
                </a>
            </li>
            <!-- Student Wise Collection Report -->
            <li class="nav-item">
                <a class="nav-link" href="../admin/rooms.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-bed navbar-brand-img text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Rooms</span>
                </a>
            </li>

            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account admin</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../admin/master-user.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-circle-08 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Master User</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../admin/deleted-boarders.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-user-run text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Left Boarders</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../admin/expenses.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-money-coins text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Expenses</span>
                </a>
            </li>

            <?php if ($userAcessStatus) { ?>
                <li class="nav-item">
                    <span class="nav-link" id="config" style="cursor: pointer;">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-settings text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Configure</span>
                    </span>
                </li>
            <?php } ?>
        </ul>
    </div>

    <div class="modal fade" id="configModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configure Settings</h5>
                </div>
                <div class="modal-body">
                    <form method="post" action="dashboard.php" id="configForm" data-parsley-validate novalidate>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="late_fine">Late Fine</label>
                                        <input type="text" class="form-control" name="late_fine" id="late_fine" placeholder="Enter Late Fine Amount" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="late_fine">Reset Date</label>
                                        <input type="date" class="form-control" id="Resetdate" placeholder="Enter Late Fine Amount" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#config").click(function() {
                $.ajax({
                    type: "POST",
                    url: "../api/fetch_config.php",
                    success: function(data) {
                        var config = JSON.parse(data);

                        $("#late_fine").val(config[0].late_fine);
                        $("#Resetdate").val(config[0].reset_date);
                        $("#configModal").modal("show");
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    },
                });
            });
        });
    </script>

</aside>