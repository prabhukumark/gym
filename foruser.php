<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
$mail = new PHPMailer(true);
// Database connection details
include('kk.php');

// Function to check if email exists
function isEmailExists($email, $conn) {
    $email = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return true; // Email exists
    } else {
        return false; // Email does not exist
    }
}

$email = "";
$resultMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    $sql = "SELECT username FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($resultUsername);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Error preparing statement";
    }

    // Check if the form is submitted
    $emailToCheck = $_POST["email"];

    if (isEmailExists($emailToCheck, $conn)) {
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
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'About username';
            $mail->Body = 'Your username is: ' . $resultUsername;

            $mail->send();

            $resultMessage = "Username sent to your email.";
        } catch (Exception $e) {
            $resultMessage = 'Error sending email: ' . $mail->ErrorInfo;
        }
    } else {
        $resultMessage = "Email does not exist enter correct email id";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Checker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #3f51b5; /* Change this color to your desired background color */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            animation: gradientBackground 10s infinite alternate;
        }

        @keyframes gradientBackground {
            0% {
                background: linear-gradient(45deg, skyblue, pink);
            }
            100% {
                background: linear-gradient(45deg, lightgreen, yellow);
            }
        }

        form {
            background-color: rgb(255, 111, 0);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: black;
            color: white;
            font-weight: bold;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: white;
            color: black;
        }

        #result-message {
            margin-top: 10px;
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <form action="foruser.php" method="post">
        <label for="email">Enter Email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Check Email</button>
        <div id="result-message"><?php echo $resultMessage; ?></div>
    </form>
</body>
</html>