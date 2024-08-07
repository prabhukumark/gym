<?php
// login.php

// Start the session
session_start();

// Database connection parameters
include('kk.php');

// Function to validate user credentials
function validateCredentials($username, $password, $conn) {
    $resultUsername = null;
    $storedPasswordHash = ''; // Initialize the variable with an empty string

    $sql = "SELECT username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameter and execute the statement
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Bind the result to variables
    $stmt->bind_result($resultUsername, $storedPasswordHash);

    // Fetch the result
    $stmt->fetch();
    $stmt->close();

    // Verify the password
    if ($resultUsername !== null && password_verify($password, $storedPasswordHash)) {
        return true;
    }

    // If the username is not found in the first query, check the second query
    $sql = "SELECT username, password FROM sir WHERE username = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameter and execute the statement
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Bind the result to variables
    $stmt->bind_result($resultUsername, $storedPasswordHash);

    // Fetch the result
    $stmt->fetch();
    $stmt->close();

    // Verify the password
    if ($resultUsername !== null && password_verify($password, $storedPasswordHash)) {
        return true;
    }

    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($password !== "") {
        // Validate credentials against the database
        $isAuthenticated = validateCredentials($username, $password, $conn);

        if (!$isAuthenticated) {
            // Set error messages
            $_SESSION['error'] = "Invalid username or password";
            header('Location: firstmain.php');
            exit();
        } else {
            // Store username in session
            $_SESSION['username'] = $username;

            // Redirect to the next page after successful login
            if (preg_match('/.*[a-z].*/', $username)) {
                header('Location: up1.php');
            } else {
                $_SESSION['username'] = $username;
                header('Location: message.php');
            }
            exit();
        }
    } else {
        // Display appropriate error messages
        $_SESSION['error'] = "Please enter a password.";
        header('Location: firstmain.php');
        exit();
    }
}

// Close the database connection
$conn->close();
?>
