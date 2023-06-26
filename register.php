
<?php
    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    session_start();
    if (isset($_SESSION['SESSION_EMAIL'])) {
        header("Location: welcome.php");
        die();
    }

    //Load Composer's autoloader
    require 'vendor/autoload.php';

    include 'config.php';
    $msg = "";

    if (isset($_POST['submit'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm-password']);
        $code = mysqli_real_escape_string($conn, md5(rand()));
        $uppercase = preg_match('@[A-Z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE email='{$email}'")) > 0) {
            $msg = "<div class='alert alert-danger'>{$email} - This email address has been already exists.</div>";
        } else {
            $password1 = mysqli_real_escape_string($conn, md5($_POST['password']));
            if ($uppercase && $lowercase && $number && $specialChars && $password === $confirm_password) {
                $sql = "INSERT INTO users (name, email, password, code) VALUES ('{$name}', '{$email}', '{$password1}', '{$code}')";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    echo "<div style='display: none;'>";
                    //Create an instance; passing `true` enables exceptions
                    $mail = new PHPMailer(true);
                    try {
                        //Server settings
                        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                        $mail->isSMTP();                                            //Send using SMTP
                        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                        $mail->Username   = 'bishalgautam@ismt.edu.np';                     //SMTP username
                        $mail->Password   = '07*Ush/Ish';                               //SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                        //Recipients
                        $mail->setFrom('bishalgautam@istm.edu.np');
                        $mail->addAddress($email);

                        //Content
                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = 'no reply';
                        $mail->Body    = 'Here is the verification link <b><a href="http://localhost/loginbishal/?verification='.$code.'">http://localhost/loginbishal/?verification='.$code.'</a></b>';

                        $mail->send();
                        echo 'Message has been sent';
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                    echo "</div>";
                    $msg = "<div class='alert alert-info'>We've send a verification link on your email address.</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Something wrong went.</div>";
                }
            } else {
                $msg = "<div class='alert alert-danger'>Requirement for password doesn't match.</div>";
            }
            if($password !== $confirm_password){
                $msg = "<div class='alert alert-danger'>Password and Confirm password do not match</div>";
            }
        }


        if (isset($_POST['g-recaptcha-response'])) {
            $recaptcha = $_POST['g-recaptcha-response'];
      
            if (!$recaptcha) {
              echo '<script>alert("Please make sure to check recaptcha.")</script>';
            ?>
              <script>
                location.replace("index.php");
              </script>
            <?php
              exit;
            } else {
              $secret = "6LfdsmQlAAAAANHtYmlz5uz9oYPveHpq9sbA1VgP";
              $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $recaptcha;
              $response = file_get_contents(($url));
      
              $responseKeys = json_decode($response, true);
      
              //just checking if recapcha works well
              if ($responseKeys['success']) {
                // echo '<script>alert("data submitted!!!")</script>';
              } else {
                echo '<script>alert("Something went wrong. Try again!")</script>';
              }
            }
          }
    }
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Login Form - Brave Coder</title>
    <!-- Meta tag Keywords -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords"
        content="Login Form" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

  <!-- Bootstrap CSS-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
  <!--  Iconscout CSS  -->
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <!-- //Meta tag Keywords -->

    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!--/Style-CSS -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />

    <script src="https://kit.fontawesome.com/af562a2a63.js" crossorigin="anonymous"></script>
    
    <!--recaptcha-->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>


</head>

<body>

    <!-- form section start -->
    <div class="form signup scroll">
    <section class="w3l-mockup-form">
            <!-- /form -->
         <div class="workinghny-form-grid">
             <div class="container">
                <div class="main-mockup">
                    
                    <div class="w3l_form align-self">
                        <div class="left_grid_info">
                            <img src="images/image5.jpg" alt="">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h2>Register Now</h2>
                        <?php echo $msg; ?>
                <form action="" method="post">
                            <input type="text" class="name" name="name" placeholder="Enter Your Name" value="<?php if (isset($_POST['submit'])) { echo $name; } ?>" required />
                            <div class="input-field">
                            <input type="email" class="email" name="email" placeholder="Enter Your Email" value="<?php if (isset($_POST['submit'])) { echo $email; } ?>" required />
                            </div>
                            <div class="input-field">
                               <input name="password" type="password" class="password" id="passField"  placeholder="Enter Your Password" minlength="8" required  />

                            </div>
                            <!--password strength-->
                    <div class="pw-strength">
                    <span>Weak</span>
                    <span></span>
                    </div>
        
                    <div id="password-policies" class="hide">
                    <ul>
                        <li id="length" class="invalid">
                        <i class="fa fa-times" aria-hidden="true"></i> At least
                        <strong>8 characters</strong> in length.
                        </li>
                        <li id="number" class="invalid">
                        <i class="fa fa-times" aria-hidden="true"></i> Contains at
                        least a <strong>number(0-9)</strong>.
                        </li>
                        <li id="upperCase" class="invalid">
                        <i class="fa fa-times" aria-hidden="true"></i> At least an
                        <strong>uppercase(A-Z) letter</strong>.
                        </li>
                        <li id="specialCharacter" class="invalid">
                        <i class="fa fa-times" aria-hidden="true"></i> Contains a
                        <strong>special character(*,$...)</strong>.
                        </li>
                    </ul>
                    </div>
                    <div class="input-field">
                            <input type="password" class="confirm-password" name="confirm-password" placeholder="Enter Your Confirm Password" required>
                    </div>
                    
                    <div class="g-recaptcha" data-type="image" data-sitekey="6LfdsmQlAAAAAPTDka4yQ25TNNcQbMPcZP133FcQ"></div>

                            <button name="submit" class="btn"  type="submit">Register</button>
                </form>
                        <div class="social-icons">
                            <p>Have an account! <a href="index.php">Login</a>.</p>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            
            <!-- //form -->
        </div>
    </section>
    <!-- //form section start -->
    
 
    <script src="./js/app.js">

</script>
</body>

</html>