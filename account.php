<!DOCTYPE html>

<?php

    include 'includes/dbConfig.php';
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        header('Location: index.php?redirect=account.php');
        exit;
    }
    
    if($_POST['submit']) {
        $oldPass = password_hash($_POST['edit-user-oldPass'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare('SELECT password FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $_SESSION['id']);
        $stmt->execute();
        $stmt->bind_result($passHash);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($_POST['edit-user-oldPass'], $passHash)) {
            if($_POST['edit-user-newPass'] == $_POST['edit-user-newPassAgain']) {

                $stmt = $conn->prepare("UPDATE accounts SET password=? WHERE id = ?");
                $updatedPassword = password_hash($_POST['edit-user-newPass'], PASSWORD_DEFAULT);
                $stmt->bind_param('si', $updatedPassword, $_SESSION['id']);
                $stmt->execute();

                $message = '<div class="alert alert-success" role="alert">
            Password updated successfully!
            </div>';
            $stmt->close();
            } else {
                $message = '<div class="alert alert-warning" role="alert">
            New passwords do not match! Please try again. 
            </div>';
            }
            
        } else {
            $message = '<div class="alert alert-warning" role="alert">
            Old password incorrect! Please try again. 
            </div>';
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
        <title>Oinos - Account</title>
    </head>

    <body>
        <!-- Top navbar -------------------------------------------------------------- -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand" href="inventory.php">
                    <i class="fa fa-wine-bottle"></i>
                    Account
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
        <br>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card mb-2">
                        <div class="card-body">
                            <h4 class="card-title">Details</h4>
                            <hr>
                            <div class="row mt-2">
                                <div class="col-sm-3">
                                    <h6>Username: </h6>
                                </div>
                                <div class="col-sm-3">
                                    <?php echo $_SESSION['name']?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-3">
                                    <h6>Email: </h6>
                                </div>
                                <div class="col-sm-3">
                                    <?php echo $_SESSION['email']?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-3">
                                    <h6>Access Level: </h6>
                                </div>
                                <div class="col-sm-3">
                                    <?php 
                                        if($_SESSION['level'] == "S") {
                                            echo "SuperAdmin";
                                        } else if ($_SESSION['level'] == "A") {
                                            echo "Admin";
                                        } else if ($_SESSION['level'] == "U") {
                                            echo "User";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Change Password</h4>
                            <hr>
                            <div class="error-message"><?php if (isset($message)) { echo $message; } ?></div>
                            <form action="" method="post">
                                <div class="input-group mb-3 pt-3">
                                    <input type="password" class="form-control" id="edit-user-oldPass" name="edit-user-oldPass" placeholder="Old password" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" onclick="showOldPassword()">
                                            <i class="fa fa-eye fa-lg" style="color:#f0f1f2;" id="oldPass-password-toggle-icon" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="input-group mb-3 pt-3">
                                    <input type="password" class="form-control" id="edit-user-newPass" name="edit-user-newPass" placeholder="New password" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" onclick="showNewPassword()">
                                            <i class="fa fa-eye fa-lg" style="color:#f0f1f2;" id="newPass-password-toggle-icon" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="input-group mb-3 pt-3">
                                    <input type="password" class="form-control" id="edit-user-newPassAgain" name="edit-user-newPassAgain" placeholder="Repeat new password" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" onclick="showNewAgainPassword()">
                                            <i class="fa fa-eye fa-lg" style="color:#f0f1f2;" id="newPassAgain-password-toggle-icon" aria-hidden="true"></i>
                                        </button>
                                    </div>
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
            </div>
        </div>

        <!--   Toggle show password script ------------------------------------------>
        <script>
            function showOldPassword() {
                var x = document.getElementById("edit-user-oldPass");
                var y = document.getElementById("oldPass-password-toggle-icon");
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

            function showNewPassword() {
                var x = document.getElementById("edit-user-newPass");
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
                var x = document.getElementById("edit-user-newPassAgain");
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
        <!--   //Toggle show password script --------------------------------------->  


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