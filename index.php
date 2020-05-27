<!DOCTYPE html>

<?php


include 'includes/dbConfig.php';
session_start();




if ($stmt = $conn->prepare('SELECT id, username, password, email, level FROM accounts WHERE username = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $username, $password, $email, $level);
    $stmt->fetch();
    // Account exists, now we verify the password.
    // Note: remember to use password_hash in your registration file to store the hashed passwords.
    if (password_verify($_POST['password'], $password)) {
        // Verification success! User has loggedin!
        // Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['name'] = $username;
        $_SESSION['id'] = $id;
        $_SESSION['email'] = $email;
        $_SESSION['level'] = $level;
        $referer = $_GET['redirect'];
        if($referer){
            header('Location: https://oinos.simonclay.uk/'.$referer);
    } else {
        header('Location: home.php');
    }

    } else {
        $message = '<div class="alert alert-warning" role="alert">
Incorrect username / password. Please try again. 
</div>';
    }
}


    $stmt->close();
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
        <title>Oinos - Log In</title>
    </head>

    <body>
        
        <div class="container pt-3">
            <div class="row">
                <div class="col-sm-4">
                </div>
                    <div class = "col-sm-4">
                    
                        <form class="form" action="" method="post">
                            
                            <h1 class="text-center pt-2"><i class="fa fa-wine-bottle"></i>  Oinos</h1>
                            <div class="error-message"><?php if (isset($message)) { echo $message; } ?></div>        
                                <div class="form-group mb-3 pt-3">
                                    <input type="text" name="username" id="username" placeholder="Username" class="form-control" required>
                                </div>

                                <div class="input-group mb-3 pt-3">
                                    <input type="password" name="password" id="password" placeholder="Password" class="form-control" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" onclick="showPassword()">
                                            <i class="fa fa-eye fa-lg" style="color:#f0f1f2;" id="password-toggle-icon" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group mb-3 pt-3">
                                    <button type="submit" name="login_btn" class="btn btn-primary btn-block">Sign In</button>
                                </div>

                        </form>
                    </div>
                    
                    </div>
            <div class="col-sm-4">
            </div>
            <div class="row">
                <div class="col-sm-4">
                </div>
                <div class="col-sm-4">
                    <p><a href="/password_reset/enter_email_reset.php">Forgot your password?</a></p>
                </div>
                <div class="col-sm-4">
                </div>
            </div>
        </div>
            
           
        <!--   Toggle show password script ------------------------------------------>
        <script>
            function showPassword() {
                var x = document.getElementById("password");
                var y = document.getElementById("password-toggle-icon");
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