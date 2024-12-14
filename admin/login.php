<?php
require '../database.php';
require '../helper.php';
$dbReference = new Database();
$helper = new Helper();

$accountingYearList = $dbReference->getData("tbl_accounting_year_master", "*");

if (isset($_SESSION['username'])) {
    header('location: dashboard.php');
    exit;
}

$popup = 0;

if (isset($_POST['submitlogin'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']);

    $UserArray = ["username" => $username];
    $result = $dbReference->getData("tbl_master_user", "password, status, acess", $UserArray);

    if ($result && $password === $result[0]['password']) {
        if ($result[0]['status'] == 1) {
            $_SESSION['username'] = $username;
            $_SESSION['acess'] = $result[0]['acess'];
            $_SESSION['accountingYearId'] = $_POST['yearid'];

            header('location: dashboard.php');
            exit;
        } else if ($result[0]['status'] == 0) {
            $popup = 1;
        }
    } else {
        $popup = 2;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Account Login</title>

    <!-- Bootstrap Core CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../assets/css/startmin.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="POST">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Username" name="username" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="yearid">
                                        <?php foreach ($accountingYearList as $key => $value): ?>
                                            <option value="<?= $value['id'] ?>" <?= date('Y') == $value['year'] ? 'selected' : '' ?>><?= $value['year'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" name="submitlogin" class="btn btn-lg btn-success btn-block">Login</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        <?php
        if ($popup == 1) {
        ?>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                    title: "Suspended",
                    text: "Contact With Admin",
                    icon: "error",
                    button: "Okk!",
                });
            });
        <?php
        } else if ($popup == 2) {
        ?>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                    ttitle: "Invalid Details",
                    text: "Invalid Username or Password",
                    icon: "error",
                    button: "Okk!",
                });
            });
        <?php
        }
        ?>
    </script>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- jQuery -->
    <script src="../assets/template/js/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../assets/template/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../assets/template/js/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../assets/template/js/startmin.js"></script>

</body>

</html>