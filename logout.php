<?php
session_start();

// Clear cache control headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Log the user out
    session_destroy();
    // Use JavaScript to redirect after logout
    echo '<script>window.location.href="firstmain.php";</script>';
    exit();
} else {
    // Redirect to the login page if not already logged in
    echo '<script>window.location.href="firstmain.php";</script>';
    exit();
}
?>
