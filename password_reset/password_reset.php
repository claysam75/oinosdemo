<!DOCTYPE html>

<?php

include 'includes/dbConfig.php';

$token = $_GET['token']; //Get token from url 
$stmt=$conn->prepare('SELECT email FROM passwordResets WHERE token = ?'); //See if any records exist with that token
$stmt->bind_param('s', $token);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

if($email) { //if rows with the url token have been returned (it exists)
    
    
    if($_POST['submit']) { //wait for the submit button to be pressed
        if($_POST['reset_pass'] == $_POST['reset_pass_again']) { //then check if the submitted new passwords match
            $passHash = password_hash($_POST['reset_pass_again'], PASSWORD_DEFAULT);
            $stmt=$conn->prepare("UPDATE accounts SET password = ? WHERE email = ?");
            $stmt->bind_param('ss', $passHash, $email);
            $stmt->execute();
            $stmt->close();
            $message = '<div class="alert alert-success" role="alert">
                Password reset successfully! <a href="../index.php">Go to login page.</a>
                </div>';
            $stmt=$conn->prepare("DELETE FROM passwordResets WHERE token = ?");
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $stmt->close(); 
             
        } else {
            $message = '<div class="alert alert-danger" role="alert">
            New passwords do not match! Please try again. 
            </div>';
            }
    }
} else {
    $message = '<div class="alert alert-danger" role="alert">
    Invalid reset token.  
    </div>';
    } 



 

?>

<html lang=en>
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="application-name" content="Oinos">
        <meta name="mobile-web-app-capable" content="yes"> 
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://kit.fontawesome.com/d194762fcf.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <link rel="apple-touch-icon" sizes="150x150" href="/assets/img/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="80x80" href="/assets/img/favicon40x40.png">
        <link rel="icon" type="image/png" sizes="40x40" href="/assets/img/favicon20x20.png">
        <link rel="shortcut-icon" href="/assets/img/favicon40x40.png">
        <title>Oinos - Password Reset</title>
    </head>

    <body>

        <div class="container-fluid pt-3">
            <div class="row">
                <div class="col-sm-4">
                </div>
                <div class="col-sm-4">
                    <h1 class="text-center pt-2"><i class="fa fa-wine-bottle"></i>  Oinos</h1>
                    <div class="error-message"><?php if (isset($message)) { echo $message; } ?></div>
                    <hr>
                    <h2 class="text-center pt-2">Reset password</h2>
                    <form class="form" action="" method="post">
                        <div class="input-group mb-3 pt-3">
                            <input type="password" name="reset_pass" id="reset_pass" placeholder="New password" class="form-control" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" onclick="showNewPassword()">
                                    <i class="fa fa-eye fa-lg" style="color:#f0f1f2;" id="newPass-password-toggle-icon" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="input-group mb-3 pt-3">
                            <input type="password" name="reset_pass_again" id="reset_pass_again" placeholder="Confirm new password" class="form-control" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" onclick="showNewAgainPassword()">
                                    <i class="fa fa-eye fa-lg" style="color:#f0f1f2;" id="newPassAgain-password-toggle-icon" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3 pt-3">
                            <button type="submit" name="submit" id="submit" value="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </form>
                </div>
                <div class="col-sm-4">
                </div>
            </div>
        </div>

        <!--   Toggle show password script ------------------------------------------>
        <script>

            function showNewPassword() {
                var x = document.getElementById("reset_pass");
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
                var x = document.getElementById("reset_pass_again");
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
        
    </body>
</html>