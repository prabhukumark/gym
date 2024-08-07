<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Sender</title>
   
    <style>
       
       body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(45deg, #001F3F, #CCCCCC, #FF6F61, #FFFFFF);
            background-size: 400% 400%;
            animation: gradientAnimation 15s infinite, colorAnimation 10s infinite;
            margin-top: 20%;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes colorAnimation {
            0% {
                filter: hue-rotate(0deg);
            }
            100% {
                filter: hue-rotate(360deg);
            }
        }

        h4 {
            margin-top: 2rem;
            font-size: 2rem;
            color: #333;
        }

        .form-contact {
            width: 300px;
            margin: auto;
            padding: 2rem;
            background-color: rgb(255, 111, 0);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
            border-radius: 16px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        input {
            width: 100%;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease-in-out;
        }

        input[type="submit"] {
            width: fit-content;
            padding: 1rem 2rem;
            cursor: pointer;
            background-color: black;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease-in-out;
        }

        input[type="submit"]:hover {
            background-color: white;
            color: black;
            font-weight: bold;
        }

        #otpSection {
            display: none;
        }

        #otpInput {
            margin-top: 1rem;
            margin-left:auto;
            margin-right:auto;
            width: 88%;
        }

        #submitOTP {
            background-color: black;
            font-weight: bold;
            color: #fff;
            margin-top: 6%;
           margin-left: 24%;
        }

        #submitOTP:hover {
            background-color: white;
            color: black;
            font-weight: bold;
        }
    </style>
</head>
<body>
    
    <form action="#sm2.php" method="post" class="form-contact" id="userVerificationForm">
        <h4>Forgot password</h4>
        <input id="username" type="text" name="username" placeholder="Enter username" required>
        <input type="email" name="email" placeholder="Enter email" required>
        <input type="submit" name="send" value="Verify">
        <div class="error-message" id="kk"></div>
    </form>
    <div id="otpSection" class="form-contact">
        <form action="#" method="post" onsubmit="return checkOTP()">
            <input id="otpInput" type="text" name="otp" placeholder="Enter OTP" required>
            <input id="submitOTP" type="submit" name="submitOTP" value="Submit OTP">
            <div class="error-message" id="k"></div>
        </form>
    </div>

    <?php
    session_start();

    include('kk.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require './PHPMailer/src/Exception.php';
    require './PHPMailer/src/PHPMailer.php';
    require './PHPMailer/src/SMTP.php';

    if (isset($_POST['send'])) {
        $recipient_email = $_POST['email'];
        $username = $_POST['username'];
        $_SESSION['kkkk'] =  $_POST['username'];
        
        $sql = "SELECT * FROM users WHERE username = ? AND email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $recipient_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $otp = rand(100000, 999999);

            $mail = new PHPMailer(true);

            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'eggeye502@gmail.com'; // Your SMTP username
                $mail->Password = 'elkmzzgckwoufvpp'; // Your SMTP password
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('eggeye502@gmail.com', 'Teaching studio');
                $mail->addAddress($recipient_email);

                $mail->isHTML(true);
                $mail->Subject = 'OTP for Verification';
                $mail->Body = 'Your OTP is: ' . $otp;

                $mail->send();

                $_SESSION['otp'] = $otp;

                echo '<script>
                        document.getElementById("userVerificationForm").style.display = "none";
                        document.getElementById("otpSection").style.display = "flex";
                      </script>';
                      exit();
            } catch (Exception $e) {
                echo 'Error sending email: ', $mail->ErrorInfo;
            }
        } else {
            echo '<script>
                    document.getElementById("kk").innerHTML="invalid username or email";
                  </script>';
        }
    }

    if (isset($_POST['submitOTP'])) {
        if (isset($_SESSION['kkkk'])) {
            $g = $_SESSION['kkkk'];

            $enteredOTP = $_POST['otp'];
            $generatedOTP = $_SESSION['otp'];

            echo '<script>
                    document.getElementById("userVerificationForm").style.display = "none";
                    document.getElementById("otpSection").style.display = "flex";
                    window.location.href = "u4.php";
                  </script>';
                
            if ($enteredOTP == $generatedOTP) {
                $_SESSION['kkk'] = $g;
                exit(); // Exit after sending JavaScript, preventing further execution
            } else {
                echo '<script>
                        document.getElementById("k").innerHTML="incorrect otp";
                      </script>';
            }
        } else {
            // Handle the case when $_SESSION['kkkk'] is not set
            echo '<script>
                    document.getElementById("kk").innerHTML="Session variable not set";
                  </script>';
        }
    }

    $conn->close();
    ?>

</body>
</html>