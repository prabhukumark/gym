<?php
session_start();
// Database connection details
include('kk.php');

$sessionUsername = '';

// Access the variable from the session
if (isset($_SESSION['kkk'])) {
    $sessionUsername = $_SESSION['kkk'];
} else {
    $_SESSION['error'] = "Username not set";
    header("Location: error.php"); 
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];
    $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/';

    if (preg_match($passwordRegex, $newPassword)) {
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = "Passwords do not match.";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            // Update the password in the database
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $hashedPassword, $sessionUsername);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Password updated successfully.";
               // sleep(3);
                session_destroy();
                //exit();
               
            } else {
                $_SESSION['error'] = "Error updating password: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        $_SESSION['error'] = "Password should contain at least one lowercase letter, one uppercase letter, one digit, one special character, and be at least 6 characters long.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Update</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            text-align: center;
            font-family: Arial, sans-serif;
            overflow: hidden; /* Prevent scrolling on smaller screens */
            background: linear-gradient(45deg, #87CEEB, black, #87CEEB, #ffffff); /* Sky blue gradient background */
            background-size: 400% 400%; /* Background size for the animation */
            animation: gradientAnimation 15s infinite; /* Animation properties */
            color: white; /* Text color */
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

        .password-form {
            width: 20%;
            min-width: 200px;
            height: 20%;
            margin: auto;
            padding: 1rem;
            background-color: rgb(255, 111, 0);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            box-sizing: border-box;
        }

        input {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            padding: 0.5rem 2rem;
            background-color: black;
            font-weight: bolder;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: white;
            font-weight: bolder;
            color: black;
        }

        /* Media query for adjusting styles on smaller screens */
        @media (max-width: 600px) {
            .password-form {
                width: 80%;
            }
        }

        label {
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="password-form">
        <h2>Password Update</h2>
        <form id="passwordForm" action="u4.php" method="post">
            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="newPassword" required>

            <label for="confirmPassword">Confirm New Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>

            <button type="submit">Submit</button>
            <div id="updateStatus" style="margin-top: 10px;">
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div style="color: black;">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']); // Clear the error message
                } elseif (isset($_SESSION['success'])) {
                    echo '<div style="color: green;">' . $_SESSION['success'] . '</div>';
                    unset($_SESSION['success']); // Clear the success message
                }
                ?>
            </div>
        </form>
    </div>

</body>
</html>