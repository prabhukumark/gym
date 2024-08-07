<?php
// Start the session
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "root";
$database = "testing";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error variables
$usernameError = "";
$passwordError = "";

// Function to validate user credentials
function validateCredentials($username, $password, $conn) {
    $sql = "SELECT username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameter and execute the statement
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Bind the result to variables
    $stmt->bind_result($resultUsername, $storedPasswordHash);
    
    // Fetch the result
    $stmt->fetch();

    // Verify the password
    if ($resultUsername !== null && password_verify($password, $storedPasswordHash)) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($password !== "") {
        // Validate credentials against the database
        $isAuthenticated = validateCredentials($username, $password, $conn);

        if (!$isAuthenticated) {
            $usernameError = "Invalid username or password";
            $passwordError = "Invalid username or password";
        } else {
            // Store username in session
            $_SESSION['username'] = $username;

            // Redirect to the next page after successful login
            header('Location: t2.php');
            exit();
        }
    } else {
        // Display appropriate error messages
        if ($password === "") {
            $passwordError = "Please enter a password.";
        }
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
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: rgb(255, 111, 0);
            transition: box-shadow 0.3s ease-in-out;
        }

        .container:hover {
            box-shadow: 0 0 20px black;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            transition: color 0.3s ease-in-out;
            color: white;
            font-weight: bold;
        }

        input {
            padding: 8px;
            margin-bottom: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            transition: border-color 0.3s ease-in-out;
        }

        input:focus {
            border-color: #4caf50;
        }

        .error-message {
            color: black;
            font-weight: bolder;
            margin-bottom: 8px;
        }

        button {
            padding: 10px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: white;
            color: black;
            font-weight: bolder;
        }

        a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin-top: 8px;
            text-align: center;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <form id="loginForm" method="post" action="">
            <h2>Login</h2>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="e.g. 12GYM0001" required>
            <span class="error-message" id="usernameError"><?php echo $usernameError; ?></span>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <span class="error-message" id="passwordError"><?php echo $passwordError; ?></span>

            <button type="submit">Login</button>
        </form>

        <a href="#">Forgot Password?</a>
    </div>

    <script>
        function login() {
            // JavaScript code for handling any client-side actions (if needed)
        }

        document.getElementById("password").addEventListener("keyup", function(event) {
            if (event.key === "Enter") {
                login();
            }
        });
    </script>
</body>
</html>