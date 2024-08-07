<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Session Timeout</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    #timeout-message {
      text-align: center;
      padding: 20px;
      background-color: #f8d7da;
      border: 1px solid #f5c6cb;
      color: #721c24;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div id="timeout-message" style="display: none;">
    <h1>Session Timeout</h1>
    <p>Your session has been logged out due to inactivity.</p>
    <p>Please log in again to continue.</p>
    <button id="login-button">Login</button>
  </div>

  <script>
    // Set the timeout duration in milliseconds (15 minutes)
    const timeoutDuration = 0 * 60 * 1000;

    // Function to display the timeout message with a delay
    function displayTimeoutMessage() {
      const timeoutMessage = document.getElementById('timeout-message');

      // Display the message after a 2-second delay
      setTimeout(() => {
        timeoutMessage.style.display = 'block';
      }, 1000);
    }

    // Function to log out the user (you can customize this part)
    function logoutUser() {
      // Display timeout message
      displayTimeoutMessage();

      // Optionally, you can add more customization or animations here

      // After user interaction (e.g., clicking the "Login" button), redirect to 'firstmain.php'
      const loginButton = document.getElementById('login-button');
      loginButton.addEventListener('click', () => {
        window.location.href = 'firstmain.php';
      });
    }

    // Set up the timeout
    let timeout;

    function startTimeout() {
      timeout = setTimeout(() => {
        logoutUser();
      }, timeoutDuration);
    }

    // Reset the timeout on user activity
    function resetTimeout() {
      clearTimeout(timeout);
      startTimeout();
    }

    // Initial setup
    document.addEventListener('DOMContentLoaded', () => {
      startTimeout();

      // Reset the timeout on user activity (e.g., mouse movement or key press)
      document.addEventListener('mousemove', resetTimeout);
      document.addEventListener('keypress', resetTimeout);
    });
  </script>
   <?php
    // Clear cache control headers
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
  ?>
</body>
</html>
