<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        h1 {
            color: #333;
        }

        p {
            color: #555;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            margin-bottom: 16px;
        }

        button {
            background-color: rgb(255, 111, 0);
            color: white;
            font-weight: bold;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }
        button:hover{
            background-color: white;
            color: black;
            
            border: 0.5px solid #000000; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: rgb(255, 111, 0);
            color: #fff;
        }

        .container div {
            margin-top: 20px;
        }

        .update-btn {
            background-color:rgb(255, 111, 0);
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
        }

        .update-btn:hover {
            text-decoration: underline;
        }

        .table-container {
            overflow-x: auto;
        }a{
            margin-left:71.3%;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        session_start();
        include('kk.php');

    
        if (isset($_SESSION['username'])) {
            $sessionUsername = $_SESSION['username'];
           // $sessionUsername = '';
            // Check if the username exists in the database
            $userExists = isUsernameExists($sessionUsername, $conn);

            if (!$userExists) {
                echo "<p>Please log in first.</p>";
            } else {
                echo '<h1>User Management</h1>

                <!-- Search Bar -->
                <form action="" method="get">
                    <label for="search">Search by Username:</label>
                    <input type="text" id="search" name="search" required>
                    <button type="submit">Search</button>
                    <!-- Back to Main Page Button -->
                    <a href="message.php">
                        <button type="button">Back to Main Page</button>
                    </a>
                </form>';
                
                // Your existing PHP code for the user management page
                // ...

            }
        } else {
            echo "<p>Please log in first.</p>";
        }

        // Close the database connection
        $conn->close();
        ?>
       
        <?php
        //  session_start();
        $servername = "localhost";
        $username = "root";
        $password = "root";
        $dbname = "testing";

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        if (isset($_SESSION['username'])) {
            $sessionUsername = $_SESSION['username'];
           // $sessionUsername = '';
        }

        function isUsernameExists($sessionUsername, $conn) {
            $sql = "SELECT * FROM sir WHERE username = '$sessionUsername'";
            $result = $conn->query($sql);
        
            if ($result->num_rows > 0) {
                // Username exists in the database
                return true;
            } else {
                // Username does not exist in the database
                return false;
            }
        }

        // Default content
        //echo "<p>Welcome to the User Management page.</p>";

        $searched_username = '';

        // Check if 'search' parameter is present in the URL
        if (isset($_GET['search'])) {
            $searched_username = $_GET['search'];

            $sql = "SELECT * FROM users WHERE username LIKE '$searched_username'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<h2>User Information</h2>";
                echo "<div class='table-container'>";
                echo "<table>";
                echo "<tr><th>Email</th><th>Username</th><th>Height</th><th>Weight</th><th>Body Fat</th><th>Visceral Fat</th><th>RMR</th><th>BMI</th><th>Subcutaneous Fat</th><th>Skeletal Muscle</th><th>Edit User</th></tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['height'] . "</td>";
                    echo "<td>" . $row['weight'] . "</td>";
                    echo "<td>" . $row['bodyfat'] . "</td>";
                    echo "<td>" . $row['visceralfat'] . "</td>";
                    echo "<td>" . $row['RMR'] . "</td>";
                    echo "<td>" . $row['BMI'] . "</td>";
                    echo "<td>" . $row['subcutfat'] . "</td>";
                    echo "<td>" . $row['skeletmusc'] . "</td>";
                    echo "<td><a href='?action=edit&id=" . $row['email'] . "' class='update-btn'>Edit</a></td>";
                    echo "</tr>";
                }

                echo "</table>";
                echo "</div>";
            } else {
                echo "<p>User not found.</p>";
            }
        }
        // Check if 'action' and 'id' parameters are present in the URL
        elseif (isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] === 'edit') {
            $user_id = $_GET['id'];

            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                echo "<div class='container'>";
                echo "<h2>Edit User Information</h2>";
                echo "<form action='?action=update' method='post'>";
                echo "<input type='hidden' name='user_id' value='" . $row['email'] . "'>";
                 echo "<label for='height'>Height:</label>";
                echo "<input type='text' id='height' name='height' value='" . $row['height'] . "' required><br>";
                echo "<label for='weight'>Weight:</label>";
                echo "<input type='text' id='weight' name='weight' value='" . $row['weight'] . "' required><br>";
                echo "<label for='body_fat'>Body Fat:</label>";
                echo "<input type='text' id='body_fat' name='body_fat' value='" . $row['bodyfat'] . "' required><br>";
                echo "<label for='visceral_fat'>Visceral Fat:</label>";
                echo "<input type='text' id='visceral_fat' name='visceral_fat' value='" . $row['visceralfat'] . "' required><br>";
                echo "<label for='rmr'>RMR:</label>";
                echo "<input type='text' id='rmr' name='rmr' value='" . $row['RMR'] . "' required><br>";
                echo "<label for='bmi'>BMI:</label>";
                echo "<input type='text' id='bmi' name='bmi' value='" . $row['BMI'] . "' required><br>";
                echo "<label for='subcutaneous_fat'>Subcutaneous Fat:</label>";
                echo "<input type='text' id='subcutaneous_fat' name='subcutaneous_fat' value='" . $row['subcutfat'] . "' required><br>";
                echo "<label for='skeletal_muscle'>Skeletal Muscle:</label>";
                echo "<input type='text' id='skeletal_muscle' name='skeletal_muscle' value='" . $row['skeletmusc'] . "' required><br>";
                echo "<button type='submit' name='update' class='update-btn'>Update User</button>";
                echo "</form>";
                echo "</div>";
            } else {
                echo "<p>User not found.</p>";
            }

            $stmt->close();
        }
        // Check if 'action' parameter is present in the URL
        elseif (isset($_GET['action']) && $_GET['action'] === 'update') {
            // Handle the form submission for updating user information
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $user_id = $_POST['user_id'];
                $height = $_POST['height'];
                $weight = $_POST['weight'];
                $body_fat = $_POST['body_fat'];
                $visceral_fat = $_POST['visceral_fat'];
                $rmr = $_POST['rmr'];
                $bmi = $_POST['bmi'];
                $subcutaneous_fat = $_POST['subcutaneous_fat'];
                $skeletal_muscle = $_POST['skeletal_muscle'];

                if (isUsernameExists($sessionUsername, $conn)) {
                    $sql = "UPDATE users SET height = ?, weight = ?, bodyfat = ?, visceralfat = ?, RMR = ?, BMI = ?, subcutfat = ?, skeletmusc = ? WHERE email = ?";
                    $stmt = $conn->prepare($sql);

                    if ($stmt) {
                        $stmt->bind_param("sssssssss", $height, $weight, $body_fat, $visceral_fat, $rmr, $bmi, $subcutaneous_fat, $skeletal_muscle, $user_id);
                        $stmt->execute();

                        if ($stmt->affected_rows > 0) {
                            echo "<div class='container'>";
                            echo "<p>User updated successfully.</p>";
                            echo "<a href='?search=" . $searched_username . "'>Back to Search</a>";
                            echo "</div>";
                        } else {
                            echo "<div class='container'>";
                            echo "<p>No rows updated for user ID: $user_id</p>";
                            echo "<a href='?search=" . $searched_username . "'>Back to Search</a>";
                            echo "</div>";
                        }

                        $stmt->close();
                    } else {
                        echo "<div class='container'>";
                        echo "<p>Error preparing statement: " . $conn->error . "</p>";
                        echo "<a href='?search=" . $searched_username . "'>Back to Search</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='container'>";
                    echo "<p>User does not exist.</p>";
                    echo "<a href='?search=" . $searched_username . "'>Back to Search</a>";
                    echo "</div>";
                }
            } else {
                echo "<div class='container'>";
                echo "<p>Invalid request method.</p>";
                echo "<a href='?search=" . $searched_username . "'>Back to Search</a>";
                echo "</div>";
            }
        }
        ?>
    </div>
</body>
</html>