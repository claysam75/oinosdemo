<!DOCTYPE html>

<?php

    include 'includes/dbConfig.php';
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        header('Location: index.php?redirect=admin.php');
        exit;
    }
    if ($_SESSION['level'] == "U") {
        header('Location: denied.php');
        exit;
    }

    $varietyError = $_GET['varietyError'];
    $varietySuccess = $_GET['varietySuccess'];

    

    if($_POST['submit']) {
        $stmt=$conn->prepare('SELECT * FROM accounts WHERE email = ?');
        $stmt->bind_param('s', $_POST['new-user-password']);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows == 0) {
            $stmt->close();
            $stmt=$conn->prepare('SELECT * FROM accounts WHERE username = ?');
            $stmt->bind_param('s', $_POST['new-user-name']);
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows == 0) {
                $stmt->close();
                if($_POST['new-user-password'] == $_POST['new-user-password-again']) {
                    $newPassword = password_hash($_POST['new-user-password'], PASSWORD_DEFAULT);
                    $stmt=$conn->prepare("INSERT INTO accounts (username, password, email, level) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param('ssss', $_POST['new-user-name'], $newPassword, $_POST['new-user-email'], $_POST['new-user-level']);
                    $stmt->execute();
                    $stmt->close();
                    $message = '<div class="alert alert-success" role="alert">
                    New user added successfully! </div>';
                    
                } else {
                    $message = '<div class="alert alert-warning" role="alert">
                    New passwords do not match! Please try again. </div>';
                }
            } else {
                $message = '<div class="alert alert-warning" role="alert">
                Provided username is already in use by another user. Please use another.</div>';
            }
        } else {
            $message = '<div class="alert alert-warning" role="alert">
            Provided email address is already in use by another user. Please use another.</div>';
        }
    }

    if($_POST['submitNewVariety']) {
        $stmt=$conn->prepare('SELECT * FROM varieties WHERE variety = ?');
        $stmt->bind_param('s', $_POST['newVariety']);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows == 0) {
            $stmt->close();
            $stmt=$conn->prepare("INSERT INTO varieties (variety) VALUES (?)");
            $stmt->bind_param('s', $_POST['newVariety']);
            $stmt->execute();
            $stmt->close();
            header('Location: http://oinos.simonclay.uk/admin.php?varietySuccess=1');
        } else {
            header('Location: http://oinos.simonclay.uk/admin.php?varietyError=1');
        }
    }



    ?>

<html lang=en>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" >
        <meta name="application-name" content="Oinos">
        <meta name="mobile-web-app-capable" content="yes"> 
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://kit.fontawesome.com/d194762fcf.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <link rel="apple-touch-icon" sizes="150x150" href="/assets/img/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="80x80" href="/assets/img/favicon40x40.png">
        <link rel="icon" type="image/png" sizes="40x40" href="/assets/img/favicon20x20.png">
        <link rel="shortcut-icon" href="/assets/img/favicon40x40.png">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
        <title>Oinos - Admin</title>

        <script>
        $(document).ready( function () {
            
            var table = $('#users-table').DataTable({
            paging: true,
            searching: true,
            orderCellsTop: true,
            order: [[0, 'asc']],
            "oSearch": {"sSearch": <?php $searchVal = $_GET['search']; echo '"'.$searchVal.'"'; ?>}
            });
        });
        </script>
    </head>

    <body>
        <!-- Top navbar -------------------------------------------------------------- -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand" href="inventory.php">
                    <i class="fa fa-wine-bottle"></i>
                    Admin
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                        <li>
                            <a class="nav-link" href="home.php"><i class="fas fa-home"></i>  Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inventory.php"><i class="fas fa-th"></i>  Inventory</a>
                        </li>
                        <li class="nav-item-dropdown">
                            <a class="nav-link dropdown-toggle"href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-camera"></i>  Scan
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="query.php"><i class="fas fa-question"></i>  Query</a>
                                <a class="dropdown-item" href="bookin.php"><i class="fas fa-arrow-down"></i>  Book In</a>
                                <a class="dropdown-item" href="drink.php"><i class="fas fa-glass-cheers"></i>  Drink</a>
                            </div>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a href="javascript:void(Tawk_API.toggle())" class="nav-link"><i class="fas fa-comment-alt" style="color:#ffbf00"></i> Help</a>
                        </li>
                        <li class="nav-item-dropdown">
                            <a class="nav-link dropdown-toggle"href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user"></i>  <?php echo $_SESSION['name']; ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="account.php"><i class="fas fa-user-circle"></i>  Account</a>
                                <?php if($_SESSION['level'] == "S" or "A") {echo '<a class="dropdown-item" href="admin.php"><i class="fas fa-user-shield"></i>  Admin</a>';} ?>
                                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i>  Log Out</a>
                            </div>
                        </li>
                    </ul>
            </div>
        </nav>
        <!-- //Top navbar ------------------------------------------------------------ -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card mt-3">
                        <div class="card-body">
                            <h4 class="card-title">Add User</h4>
                            <hr>
                            <div class="error-message"><?php if (isset($message)) { echo $message; } ?></div>
                            <form action="" method="post">
                                <div class="input-group mb-3 pt-3">
                                    <input type="text" class="form-control" id="new-user-name" name="new-user-name" placeholder="Name" required>
                                </div>
                                <div class="input-group mb-3 pt-3">
                                    <input type="email" class="form-control" id="new-user-email" name="new-user-email" placeholder="Email address" required>
                                </div>
                                <div class="input-group mb-3 pt-3">
                                    <input type="password" class="form-control" id="new-user-password" name="new-user-password" placeholder="Password" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" onclick="showNewPassword()">
                                            <i class="fa fa-eye fa-lg" style="color:#f0f1f2;" id="newPass-password-toggle-icon" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="input-group mb-3 pt-3">
                                    <input type="password" class="form-control" id="new-user-password-again" name="new-user-password-again" placeholder="Confirm Password" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" onclick="showNewAgainPassword()">
                                            <i class="fa fa-eye fa-lg" style="color:#f0f1f2;" id="newPassAgain-password-toggle-icon" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new-user-level">Access Level</label>
                                    <select id="new-user-level" name="new-user-level" class="form-control">
                                        <?php
                                            if($_SESSION['level'] == "A") {
                                                echo '<option value="U" selected="selected">User</option>
                                                    <option value="A">Admin</option>';
                                            } else if ($_SESSION['level'] == "S") {
                                                echo '<option value="U" selected="selected">User</option>
                                                <option value="A">Admin</option>
                                                <option value="S">SuperAdmin</option>';
                                            }

                                        ?>

                                        <
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 mb-3 pt-3">
                                        <input type="submit" name="submit" id="submit" value="Submit" class="btn btn-block btn-primary form-group"></input>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            
            <br>
            
                <div class="col-sm-6">
                    <div class="card mt-3">
                        <div class="card-body">
                        <h4 class="card-title">Add Variety</h4>
                            <hr>
                            <div class="error-message"><?php if (isset($varietyError)) { echo '<div class="alert alert-warning" role="alert"> Variety already exists!</div>'; } ?></div>
                            <div class="success-message"><?php if (isset($varietySuccess)) { echo '<div class="alert alert-success" role="alert"> Variety added successfully!</div>'; } ?></div>

                            <h6>Current Varieties</h6>
                            <?php
                                $stmt=$conn->prepare('SELECT * FROM varieties ORDER BY variety');
                                $stmt->execute();
                                $result=$stmt->get_result();
                                $numRows = $result->num_rows;
                                if($numRows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $variety = $row['variety'];
                                        echo '<div class="row"><div class="col-sm-6">'.$variety.'</div></div>';
                                    }
                                    $stmt->close();
                                }
                            ?>
                            <hr>
                            <container>
                            <form action="" method="post">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="newVariety" name="newVariety" placeholder="New Variety" required>
                                </div>
                                <div class="row">
                                <div class="col-sm-12">
                                    <button type="submit" name="submitNewVariety" id="submitNewVariety" value="Submit"  class="btn btn-primary form-control col-sm-12"><i class="fas fa-save"></i> Save</button>
                                </div>
                                </div>
                            </form>
                            </container>
                        </div>
                    </div>
                </div>
                </div>
            
        </div>
        
        <br>

        <script>

            function showNewPassword() {
                var x = document.getElementById("new-user-password");
                var y = document.getElementById("newPass-password-toggle-icon");
                if (x.type==="password") {
                    x.type = "text";
                    y.classList.remove("fa-eye");
                    y.classList.add("fa-eye-slash");
                } else {
                    x.type = "password";
                    y.classList.remove("fa-eye-slash");
                    y.classList.add("fa-eye");
                }
            }
            </script>

            <script>
            function showNewAgainPassword() {
                var x = document.getElementById("new-user-password-again");
                var y = document.getElementById("newPassAgain-password-toggle-icon");
                if (x.type==="password") {
                    x.type = "text";
                    y.classList.remove("fa-eye");
                    y.classList.add("fa-eye-slash");
                } else {
                    x.type = "password";
                    y.classList.remove("fa-eye-slash");
                    y.classList.add("fa-eye");
                }
            }
        </script>  

        <!-- Additional Bootstrap scripts ---------------------------------------------------------------- -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <!-- //Additional Bootstrap scripts -------------------------------------------------------------- -->
        
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5e6f918e8d24fc226587deae/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>
        <!--End of Tawk.to Script-->


        </body>
        </html>