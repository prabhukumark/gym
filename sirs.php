<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }

        form {
            width: 350px;
            margin: 0 auto;
            border: 1px solid black;
            background-color: rgb(255, 111, 0);
            box-sizing: border-box;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            padding: 10px;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 10px;
            margin-left: 18px;
            margin-bottom: 5px;
            text-align: left;
            color: rgb(255, 255, 255);
            font-weight: bold;
        }

        input {
            width: calc(100% - 16px);
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 2px solid black;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: black;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: white;
            color: black;
            font-weight: bold;
            border: 2px solid black;
        }

        h2 {
            font-size: 17px;
            margin-top: 1px;
        }

        .error-message {
            color: black;
            text-align: left;
            margin-left: 18px;
            margin-top: -5px;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-top: 10px;
        }

        .marquee {
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
        }
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <form id="signupForm" action="sirs.php" method="post">
        <h2>Sign Up</h2>
        <!-- Input fields for user information -->
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        <div class="marquee"><marquee behavior="scroll" direction="left"><span class="error-message" id="usernameError"></span></marquee></div>

        <label for="lastName">Name</label>
        <input type="text" id="lastName" name="lastName" required>
        <div class="marquee"><marquee behavior="scroll" direction="left"><span class="error-message" id="lastNameError"></span></marquee></div>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <div class="marquee"><marquee behavior="scroll" direction="left"><span class="error-message" id="emailError"></span></marquee></div>

        <label for="password">Create Password</label>
        <input type="password" id="password" name="password" required>
        <div class="marquee"><marquee behavior="scroll" direction="left"><span class="error-message" id="passwordError"></span></marquee></div>

        <label for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>
        <div class="marquee"><marquee behavior="scroll" direction="left"><span class="error-message" id="confirmPasswordError"></span></marquee></div>

        <!-- Button to trigger form submission -->
        <button type="submit">Sign Up</button>
    </form>

    <!-- Display area for signup status -->
    <div id="signupStatus" style="margin-top: 10px;"></div>
    <?php if (isset($errorMessage)) { ?>
        <div class="error-message"><?php echo $errorMessage; ?></div>
    <?php } elseif (isset($successMessage)) { ?>
        <div class="success-message"><?php echo $successMessage; ?></div>
    <?php } ?>

</body>
</html>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
include('kk.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch data from the form
    $inputUsername = $_POST['username'];
    $inputLastName = $_POST['lastName'];
    $inputEmail = $_POST['email'];
    $inputPassword = $_POST['password'];
    $inputConfirmPassword = $_POST['confirmPassword'];

    // Additional validation and processing can be added here
    // For simplicity, let's check if the username already exists
    $checkUsernameQuery = "SELECT * FROM sir WHERE username = '$inputUsername' or email='$inputEmail'";
    $result = $conn->query($checkUsernameQuery);
   
    $checkUsernameQuery2 = "SELECT * FROM sir WHERE email='$inputEmail'";
        
    $result2 = $conn->query($checkUsernameQuery2);
    $checkUsernameQuery3 = "SELECT * FROM sir WHERE username = '$inputUsername' and email='$inputEmail'";
    $result3 = $conn->query($checkUsernameQuery3);
    $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/';
    $pattern = '/.*[a-z].*/';
    if ($result->num_rows > 0) {

        if($result3->num_rows > 0){
            $errorMessage = "!!!**Username and email  already exists. Please choose a different username and email**!!!";
        }
        else if($result2->num_rows > 0){
            $errorMessage = "!!!**email  already exists. Please choose a different email**!!!";
        }else{
            $errorMessage = "!!!**Username already exists. Please choose a different username**!!!";
        // Username already exists, set an error message
        }
    } elseif(preg_match($pattern,$inputUsername)){

        $errorMessage = "!!!**please enter username only numbers**!!!";


    }
    
    elseif (preg_match($passwordRegex, $inputPassword)) {
        if ($inputPassword !== $inputConfirmPassword) {
            // Passwords do not match, set an error message
            $errorMessage = "Passwords do not match. Please try again.";
        } else {
            // Username is unique, and passwords match, proceed with the registration
            // You can add further validation and database operations here 

            // Hash the password for security
            $hashedPassword = password_hash($inputPassword, PASSWORD_DEFAULT);

            // Insert the new user into the 'users' table
            $insertQuery = "INSERT INTO sir (username, name, email, password) VALUES ('$inputUsername', '$inputLastName', '$inputEmail', '$hashedPassword')";

            if ($conn->query($insertQuery) === TRUE) {
                // Registration successful
                $successMessage = "Registration successful!";
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Your SMTP server
                    $mail->SMTPAuth = true;
                    $mail->Username = 'eggeye502@gmail.com'; // Your SMTP username
                    $mail->Password = 'elkmzzgckwoufvpp'; // Your SMTP password
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port = 465;
            
                    // Recipients
                    $mail->setFrom('eggeye502@gmail.com', 'Teaching studio');
                    $mail->addAddress($inputEmail);
            
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to Our Gym!';
                    $mail->Body = "Hi ,$inputUsername!\n ,Welcome to our gym! We're thrilled to have you on board. Whether you're a fitness pro or just starting, we're here to support your journey. If you have any questions, feel free to ask.!\nCheers to your fitness goals!!";
            
                    // Send email
                    $mail->send();
            
                    // Now you can store the OTP in the database or session for verification
                    // For example, you can use $_SESSION['otp'] = $otp;
            
                   // echo "<script> alert('OTP sent successfully.');</script>";
                } catch (Exception $e) {
                    echo 'Error sending email: ', $mail->ErrorInfo;
                }
            
                        } else {
                            // Registration failed, provide a detailed error message
                            $errorMessage = "Error: " . $insertQuery . "<br>" . $conn->error;
                        }
                    }
    } else {
        $errorMessage = "The Password Must Contain at least one lowercase, one uppercase, one digit, one special character, and be at least 6 characters long.";
    }
}

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php if (isset($errorMessage)) { ?>
        <div class="error-message"><?php echo $errorMessage; ?></div>
    <?php } elseif (isset($successMessage)) { ?>
        <div class="success-message"><?php echo $successMessage; ?></div>
    <?php } ?>
</body>
</html>