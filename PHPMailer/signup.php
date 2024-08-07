<?php
session_start(); // Start the session

$hostname = "localhost";
$username = "root";
$password = "root";
$database = "testing";

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch user input values
    $username = $_POST['userName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Your password and email validation logic here...
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Fetch user input values
        $userName = $_POST['userName'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
    
        // Regular expression for email validation
        $emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/';
    
        // Clear any existing error messages
        clearErrorMessages();
    
        // Check for empty values and display error messages
        if (empty($userName)) {
            displayErrorMessage("userNameError", "Please enter your User Name.");
            return;
        }
    
        if (empty($email)) {
            displayErrorMessage("emailError", "Please enter your Email.");
            return;
        } elseif (!preg_match($emailRegex, $email)) {
            displayErrorMessage("emailError", "Please enter a valid email address.");
            return;
        }
    
        if (empty($password)) {
            displayErrorMessage("passwordError", "Please enter a password.");
            return;
        }
    
        if (empty($confirmPassword)) {
            displayErrorMessage("confirmPasswordError", "Please confirm your password.");
            return;
        }
    
        if ($password !== $confirmPassword) {
            displayErrorMessage("passwordError", "Passwords do not match. Please try again.");
            displayErrorMessage("confirmPasswordError", "Passwords do not match. Please try again.");
            return;
        } elseif (!preg_match($passwordRegex, $password)) {
            displayErrorMessage("passwordError", "The password must contain at least one lowercase letter, one uppercase letter, one number, and one special character. It should be at least 6 characters long.");
            return;
        }
    
        // Assuming the following function sends email
        try {
            sendEmail($email, $userName);
            // Display successful signup message
            echo '<p>Sign up successful! Welcome, ' . $userName . '!</p>';
        } catch (Exception $error) {
            error_log("Error sending email: " . $error->getMessage());
            // Display error message if email sending fails
            echo '<p>Error sending email. Please try again later.</p>';
        }
    }
    
    // Function to display error messages
    function displayErrorMessage($elementId, $message) {
        echo "<div id='$elementId' class='error-message'>$message</div>";
    }
    
    function clearErrorMessages() {
        $errorMessages = ['userNameError', 'emailError', 'passwordError', 'confirmPasswordError'];
        foreach ($errorMessages as $elementId) {
            echo "<div id='$elementId' class='error-message'></div>";
        }
    }
    
    // Check if the username already exists
    $sql = "SELECT * FROM signup WHERE username=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $present = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($present > 0) {
        $_SESSION['username_alert'] = '1';
    } else {
        // Insert the data
        $query = "INSERT INTO signup (userName, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
        
        if (mysqli_stmt_execute($stmt)) {
            // Send email after successful registration
            try {
                sendEmail($email, $username);
                echo "Data inserted successfully! Check your email for further instructions.";
            } catch (Exception $error) {
                echo "Data inserted successfully, but there was an error sending the email. Please try again.";
            }
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}

// Function to send email
function sendEmail($email, $username) {
    $to = $email;
    $subject = "Your Access to Training Studio - Get Started Today!";
    $message = "Dear $username,

We hope this message finds you well. As part of your journey with Training Studio, we're thrilled to provide you with access to our comprehensive training platform.";

    $headers = "From: eggeye502@gmail.com\r\n";
    $headers .= "Reply-To: eggeye502@gmail.com\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

    // Send the email
    if (mail($to, $subject, $message, $headers)) {
        // Email sent successfully
        return true;
    } else {
        // Email sending failed
        throw new Exception("Failed to send email.");
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <script src="https://smtpjs.com/v3/smtp.js"></script>
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

        .marquee {
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
        }
    </style>
</head>
<body>

    <form id="signupForm"  method="post>
        <h2>Sign Up</h2>
        <!-- Input fields for user information -->
        <label for="userName">User Name</label>
        <input type="text" id="userName" name="userName" required>
        <div class="marquee"><marquee behavior="scroll" direction="left"><span class="error-message" id="userNameError"></span></marquee></div>

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
        <button type="button" onclick="submitForm()">Sign Up</button>
    </form>

    <!-- Display area for signup status -->
    <div id="signupStatus" style="margin-top: 10px;"></div>

    

</body>
</html>