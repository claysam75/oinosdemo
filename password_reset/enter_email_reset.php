<!DOCTYPE html>

<?php

include 'includes/dbConfig.php';

    require '../includes/PHPMailer/src/PHPMailer.php';
    require '../includes/PHPMailer/src/SMTP.php';
    

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    
    

    if($_POST['submit']) {
        if(isset($_POST['email'])) {
            $email = $_POST['email'];
            $stmt=$conn->prepare('SELECT email FROM accounts WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows >= 1) {
                $stmt->close();
                $token = bin2hex(random_bytes(50));
                $stmt=$conn->prepare("INSERT INTO passwordResets(email, token) VALUES (?, ?)");
                $stmt->bind_param("ss", $email, $token);
                $stmt->execute();
            

                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host = '** YOUR EMAIL PROVIDER HOST INFO HERE **';
                $mail->Port = 587;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->SMTPAuth = true; 
                $mail->Username   = '** YOUR EMAIL ADDRESS HERE **';                     
                $mail->Password   = '**YOUR EMAIL APP SPECIFIC PASSWORD HERE **';
                
                

                $mail->setFrom('oinosmail@gmail.com', 'Oinos');
                $mail->addAddress($_POST['email']);
                $mail->isHTML(true);
                $mail->Subject = 'Oinos Password Reset';

                $mail->Body    ='Hi.
                                 A password reset was requested for an account assosciated with this email address.
                                 Click the link below to reset your password.
                                 <br>
                                 <a href="https://oinos.simonclay.uk/password_reset/password_reset.php?token='.$token.'">Reset Password</a>';

                $mail->send();
            
                $message = '<div class="alert alert-success" role="alert">
                A reset email has been sent to ' . $_POST['email'] .
                '</div>';
                $stmt->close();
            } else {
                $message = '<div class="alert alert-warning" role="alert">
            No user with that email address. Please try again.</div>';
            }
        } else {
            $message = '<div class="alert alert-warning" role="alert">
            Please enter a valid email address.</div>';
        }
        
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
        <title>Oinos - Enter email</title>
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
                    <h2 class="text-center pt-2">Enter email</h2>
                    <form class="form" action="" method="post">
                        <div class="input-group mb-3 pt-3">
                            <input type="email" name="email" id="email" placeholder="email address" class="form-control" required>
                        </div>
                        <div class="form-group mb-3 pt-3">
                            <button type="submit" name="submit" id="submit" value="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </form>
                </div>
                <div class="col-sm-4">
                </div>
            </div>
        </div>

        <!-- Additional Bootstrap scripts ---------------------------------------------------------------- -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <!-- //Additional Bootstrap scripts -------------------------------------------------------------- -->
        
    </body>
</html>