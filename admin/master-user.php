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
$userList = $dbReference->getData("tbl_master_user");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['UserSubmit'])) {
    // Add User
    if (empty($_POST['userId']) && !$dbReference->getData("tbl_master_user", "username", ["username" => $_POST['username']])) {
      if (!empty($_POST['name']) && !empty($_POST['username']) && !empty($_POST['password'])) {
        $dbReference->insertData("tbl_master_user", ["name" => $_POST['name'], "username" => $_POST['username'], "password" => sha1($_POST['password']), "status" => "1", "acess" => "0"]);
        header('location: master-user.php');
        exit;
      }
    } else {
      // Update User
      $updateStatus = $dbReference->getData("tbl_master_user", "id", ["username" => $_POST['username']]);
      if (!$updateStatus && !empty($_POST['name']) && !empty($_POST['username']) && !empty($_POST['password'])) {
        $dbReference->updateData("tbl_master_user", ["name" => $_POST['name'], "username" => $_POST['username'], "password" => sha1($_POST['password']), "status" => "1", "acess" => "0"], ["id" => $_POST['userId']]);
        header('location: master-user.php');
        exit;
      } else if ($updateStatus[0]['id'] == $_POST['userId']) {
        $dbReference->updateData("tbl_master_user", ["name" => $_POST['name'], "username" => $_POST['username'], "password" => sha1($_POST['password']), "status" => "1", "acess" => "0"], ["id" => $_POST['userId']]);
        header('location: master-user.php');
        exit;
      }
    }
  } else {
    // Status Update
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;
    $dbReference->updateData("tbl_master_user", ["status" => $status ? 0 : 1], ["id" => $id]);
    header('location: master-user.php');
    exit;
  }
}

// Delete User
if ($userAcessStatus == 1) {
  if (isset($_GET['id'])) {
    $dbReference->deleteData("tbl_master_user", ["id" => $_GET['id']]);
    header('location: master-user.php');
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
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h6>Master User</h6>
                <?php if ($userAcessStatus) { ?>
                  <div class="m-t-0 text-right">
                    <span class="btn btn-default waves-effect waves-light" id="addUser" style="background-color: green; color: white;"><i class="fa fa-plus"></i> Add</span>
                  </div>
                <?php } ?>
              </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Username</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <?php if ($userAcessStatus) { ?>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($userList as $user) :
                      if ($userAcessStatus == 1 && $userprofile == $user['username']) {
                        continue;
                      } else if ($userprofile != $user['username'] && $userAcessStatus != 1) {
                        continue;
                      } ?>
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
                            <span class="text-secondary text-xs font-weight-bold"><?php echo $user['username']; ?></span>
                          </div>
                        </td>

                        <td class="align-middle text-center text-sm">
                          <?php
                          $statusBadgeClass = ($user['status'] == 1) ? 'bg-gradient-success' : 'bg-gradient-danger';
                          $onclickAttribute = ($userAcessStatus == 1) ? 'onclick="postData(' . $user['id'] . ', ' . $user['status'] . ')"' : '';
                          ?>
                          <span class="badge badge-sm <?php echo $statusBadgeClass; ?>" style="width: 100px; cursor: pointer;" <?php echo $onclickAttribute; ?>>
                            <?php echo ($user['status'] == 1) ? 'Active' : 'Inactive'; ?>
                          </span>
                        </td>

                        <?php if ($userAcessStatus) { ?>
                          <td class="text-center">
                            <span class="edit-row updateUser" style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Update Students Information" data-userid="<?php echo $user['id']; ?>" data-name="<?php echo $user['name']; ?>" data-username="<?php echo $user['username']; ?>">
                              <i class="fas fa-edit" style="color: #29b6f6;"></i>
                            </span>
                            &nbsp;&nbsp;
                            <span class="edit-row" style="cursor: pointer;" data-toggle="tooltip" data-placement="top" onclick="confirmDelete(<?php echo $user['id']; ?>)">
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

    <!-- User Modal -->
    <div class="modal fade" id="UserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">User Information</h5>
          </div>
          <div class="modal-body">
            <form method="post">
              <!-- Hidden input for user ID -->
              <input type="hidden" name="userId" id="userId" value="">

              <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name">
              </div>
              <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username">
              </div>
              <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password">
              </div>
              <button type="submit" name="UserSubmit" class="btn btn-primary">Submit</button>
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

  <script src="../assets/js/main/master-user.js"></script>

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script>
</body>

</html>