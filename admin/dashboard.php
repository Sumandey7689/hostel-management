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
$dbReference->refreshRoomData();

$resetDate = ($dbReference->getData("tbl_config", "reset_date", ["id" => "1"]))[0]['reset_date'];
$currentDate = date('Y-m-d');
$query = "SUM(room_capacity) AS total_room_capacity,SUM(room_filled) AS total_room_filled,(SUM(room_capacity) - SUM(room_filled)) AS available_room_capacity";

if (($currentDate >= $resetDate)) {
  $dbReference->resetData("tbl_payments_months", ["january" => 0, "february" => 0, "march" => 0, " april" => 0, "may" => 0, "june" => 0, "july" => 0, "august" => 0, "september" => 0, "october" => 0, "november" => 0, "december" => 0]);
  $dbReference->resetData("tbl_payments_history", ["payment_color" => NULL]);
  $newResetDate = date('Y-m-d', strtotime($resetDate . ' +1 year'));
  $dbReference->updateData("tbl_config", ["reset_date" => $newResetDate], ["id" => 1]);
}

if (isset($_POST["password1"]) && isset($_POST["password2"])) {
  if ($_POST["password1"] === $_POST["password2"]) {
    $dbReference->updateData("tbl_master_user", ["password" => sha1($_POST["password1"])], ["username" => $userprofile]);
    header('location: login.php');
    exit;
  }
}

if (isset($_POST['late_fine'])) {
  $dbReference->updateData("tbl_config", ["late_fine" => $_POST['late_fine']], ["id" => "1"]);
  header('location: dashboard.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Dashboard</title>
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

<body class="g-sidenav-show bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  <?php include '../includes/navbar.php' ?>
  <main class="main-content position-relative border-radius-lg">
    <?php include '../includes/header.php' ?>
    <div class="container-fluid py-4">
      <?php $statusBadgeClass = ($userAcessStatus) ? 'col-xl-3 col-sm-6 mb-xl-0 mb-4' : 'col-12 col-md-6 mb-4'; ?>
      <div class="row">
        <div class="<?php echo $statusBadgeClass ?>">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-2 text-uppercase font-weight-bold">Boarders No</p>
                    <h5 class="font-weight-bolder">
                      <?php echo ($dbReference->getData("tbl_users", "COUNT(*) AS active_boarders", ["active" => "1"]))[0]['active_boarders']; ?>
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                    <i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="<?php echo $statusBadgeClass ?>">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-2 text-uppercase font-weight-bold">Available Seats</p>
                    <h5 class="font-weight-bolder">
                      <?php echo ($dbReference->getData("tbl_rooms_data", $query))[0]['available_room_capacity']; ?>
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                    <i class="ni ni-building text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php if ($userAcessStatus) { ?>

          <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
              <div class="card-body p-3">
                <div class="row">
                  <div class="col-8">
                    <div class="numbers">
                      <p class="text-sm mb-2 text-uppercase font-weight-bold">Payments</p>
                      <h5 class="font-weight-bolder">
                        ₹<?php echo ($dbReference->getData("tbl_payments_history", "SUM(total_payment_amount) AS total_payment_amount", ["active" => "1"]))[0]['total_payment_amount']; ?>
                      </h5>
                    </div>
                  </div>
                  <div class="col-4 text-end">
                    <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                      <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
              <div class="card-body p-3">
                <div class="row">
                  <div class="col-8">
                    <div class="numbers">
                      <p class="text-sm mb-2 text-uppercase font-weight-bold">Total Expenses</p>
                      <h5 class="font-weight-bolder">
                        ₹<?php echo (($dbReference->getData("tbl_expenses", "SUM(amount) AS amount"))[0]['amount']) ?? 0; ?>
                      </h5>
                    </div>
                  </div>
                  <div class="col-4 text-end">
                    <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                      <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <?php } ?>
      </div>
    </div>
    <div class="row mt-4 p-4">
      <div class="col-lg-7 mb-lg-0 mb-4">
        <div class="card z-index-2 h-100">
          <div class="card-header pb-0 pt-3 bg-transparent">
            <h6 class="text-capitalize">Sales overview</h6>
          </div>
          <div class="card-body p-3">
            <div class="chart">
              <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
            </div>
            <div class="text-center mt-4">
              <button id="toggleChartType" class="btn btn btn-primary">Toggle Chart Type</button>
            </div>
          </div>
        </div>
      </div>


      <div class="col-lg-5">
        <div class="card card-carousel overflow-hidden h-100 p-0">
          <div id="carouselExampleCaptions" class="carousel slide h-100" data-bs-ride="carousel">
            <div class="carousel-inner border-radius-lg h-100">
              <div class="carousel-item h-100 active" style="background-image: url('../assets/img/hostel-room.jpg'); background-size: cover;">
                <div class="carousel-caption d-none d-md-block bottom-0 text-start start-0 ms-5">
                  <div class="icon icon-shape icon-sm bg-white text-center border-radius-md mb-3">
                    <i class="ni ni-building text-dark opacity-10"></i>
                  </div>
                  <h5 class="text-white mb-1">Explore Hostel Rooms</h5>
                  <p>Discover our comfortable and well-equipped hostel rooms designed for your convenience.</p>
                </div>
              </div>
              <div class="carousel-item h-100" style="background-image: url('../assets/img/hostel-payment.jpeg'); background-size: cover;">
                <div class="carousel-caption d-none d-md-block bottom-0 text-start start-0 ms-5">
                  <div class="icon icon-shape icon-sm bg-white text-center border-radius-md mb-3">
                    <i class="ni ni-credit-card text-dark opacity-10"></i>
                  </div>
                  <h5 class="text-white mb-1">Manage Payments Effortlessly</h5>
                  <p>Experience hassle-free payment management with our secure and efficient payment system.</p>
                </div>
              </div>
            </div>
            <button class="carousel-control-prev w-5 me-3" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next w-5 me-3" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
        </div>
      </div>

    </div>
    <?php include '../includes/footer.php' ?>
  </main>

  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/chartjs.min.js"></script>

  <script src="../assets/js/main/dashboard.js"></script>

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script>

</body>

</html>