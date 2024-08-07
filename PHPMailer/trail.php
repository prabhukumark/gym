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

$username = $_POST['userName'];
$email = $_POST['email'];
$pass = $_POST['password'];

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
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $pass);
    if (mysqli_stmt_execute($stmt)) {
        echo "Data inserted successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}
?>
