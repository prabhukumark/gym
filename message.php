<?php
// Disable caching to ensure up-to-date data
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Start a session to persist user data across requests
session_start();
$resultMessage=""; 

// Database connection parameters
include('kk.php');

// Include the necessary PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables for session-based user information
$sessionUsername = '';
$sessionImagname = '';

// Access the variable from the session
if (isset($_SESSION['username'])) {
    $sessionUsername = $_SESSION['username'];
    $sessionImagname = $_SESSION['username'];
} else {
    $_SESSION['error'] = "Username not set";
    echo "not";
}

// Fetch user's name and image name from the database
$sql = "SELECT name, images FROM sir WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $sessionUsername);
    $stmt->execute();
    $stmt->bind_result($resultUsername, $resultImagname);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Error preparing statement";
}

// Check if the form is submitted for user details update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_action']) && $_POST['update_action'] === "update_form") {
    // Retrieve and sanitize form data
    $name = isset($_POST['uname']) ? $_POST['uname'] : null;
    $phone = isset($_POST['uphone']) ? $_POST['uphone'] : null;
    $dob = isset($_POST['udob']) ? $_POST['udob'] : null;
    $occupation = isset($_POST['uoccup']) ? $_POST['uoccup'] : null;
    $age = isset($_POST['uage']) ? $_POST['uage'] : null;
    $exp = isset($_POST['uexpe']) ? $_POST['uexpe'] : null;

    $updateFields = array();
    $updateParams = array();
    $updateImage = false;

    // Check if the image is being updated
    if (isset($_FILES['input_file']) && $_FILES['input_file']['size'] > 0) {
        $updateImage = true;

        $img_name = $_FILES['input_file']['name'];
        $img_size = $_FILES['input_file']['size'];
        $tmp_name = $_FILES['input_file']['tmp_name'];
        $error = $_FILES['input_file']['error'];

        if ($error === 0) {
            if ($img_size > 12500000000) {
                $em = "File size is too large.";
                header("Location: message.php?error=$em");
                exit();
            }
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $allowed_exs = array("jpg", "jpeg", "png");

            if (in_array($img_ex_lc, $allowed_exs)) {
                $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                $img_upload_path = 'uploads/' . $new_img_name;

                // Update image for the specific user
                $sql = "UPDATE sir SET images = ? WHERE username = ?";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("ss", $new_img_name, $sessionUsername);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        echo "Image updated successfully!";
                    } else {
                        echo "No rows were affected. Check your SQL query and database.";
                    }

                    $stmt->close();

                    move_uploaded_file($tmp_name, $img_upload_path);

                    $_SESSION['new_img_name'] = $new_img_name;
                    header("Location: message.php");
                    exit();
                } else {
                    echo "Error preparing statement for image update: " . $conn->error;
                }
            } else {
                $em = "You can't upload files of this type.";
                header("Location: message.php?error=$em");
                exit();
            }
        } else {
            $em = "Unknown error occurred!";
            header("Location: message.php?error=$em");
            exit();
        }
    }

    // Construct the dynamic query based on other fields
    if ($name !== null) {
        $updateFields[] = "name = ?";
        $updateParams[] = $name;
    }
    if ($phone !== null) {
        $updateFields[] = "phoneno = ?";
        $updateParams[] = $phone;
    }
    if ($dob !== null) {
        $updateFields[] = "dob = ?";
        $updateParams[] = $dob;
    }
    if ($occupation !== null) {
        $updateFields[] = "occupation = ?";
        $updateParams[] = $occupation;
    }
    if ($age !== null) {
        $updateFields[] = "age = ?";
        $updateParams[] = $age;
    }
    if ($exp !== null) {
        $updateFields[] = "experience = ?";
        $updateParams[] = $exp;
    }

    // Update user details in the database
    if (!empty($updateFields)) {
        $updateFieldsStr = implode(", ", $updateFields);

        // Construct the dynamic query for other fields
        $sql = "UPDATE sir SET $updateFieldsStr";
        if ($updateImage) {
            $sql .= ", images = ?";
            $updateParams[] = $resultImagname;
        }
        $sql .= " WHERE username = ?";
        $updateParams[] = $sessionUsername;
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $types = str_repeat('s', count($updateParams));
            $stmt->bind_param($types, ...$updateParams);

            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "User details updated successfully!";
            } else {
                echo "No rows were affected. Check your SQL query and database.";
            }

            $stmt->close();
        } else {
            echo "Error preparing statement";
        }
    }
}

$sql = "SELECT name, phoneno, dob, occupation, age, experience FROM sir WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $sessionUsername);
    $stmt->execute();
    $stmt->bind_result($resultName, $resultPhone, $resultDob, $resultOccupation, $resultAge, $resultExperience);
    $stmt->fetch();
    $stmt->close();
} else {
    echo 'Error preparing statement';
}

// The rest of your code continues...

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email_action']) && $_POST['email_action'] === "send_email") {
    $message = $_POST['message'];

    $sql = "SELECT email FROM users";
    $result = $conn->query($sql);

    $recipients = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $recipients[] = $row['email'];
        }

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'eggeye502@gmail.com';
            $mail->Password = 'elkmzzgckwoufvpp'; // Use the generated App Password here
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('eggeye502@gmail.com', 'Teaching Studio');
            $mail->isHTML(true);
            $mail->Subject = 'Your Subject Here';
            $mail->Body = $message;

            foreach ($recipients as $to) {
                $mail->clearAddresses();
                $mail->addAddress($to);

                try {
                    $mail->send();
                } catch (Exception $e) {
                    echo "Error sending email to $to: {$mail->ErrorInfo}<br>";
                    continue;
                }
            }

           $resultMessage= "Emails sent successfully.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No emails found in the database.";
    }

    $conn->close();
}

    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        input[type="submit"] {
            background-color: rgba(255,111,0);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: black;
        }

        #log {
            background-color: rgba(255,111,0);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #log:hover {
            background-color: black;
        }

        a {
            display: block;
            margin-top: 16px;
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }

        #mo {
            display: block;
            width: 200px;
            background: rgba(255,111,0);
            color: white;
            padding : 8px;
            margin: 5px auto;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
#mo:hover{
    background: black;
    color: white;
}
        #profil {
            width: 100px;
            height: 100px;
            border-radius: 70%;
            justify-content: center;

        }

        .me {
            justify-content: center;
            text-align: center;
            display: none;
        }

        .sub {
            display: block;
            width: 70px;
            background: rgba(255, 111, 0);
            color: white;
            padding: 8px;
            margin: 5px auto;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }

        .sub:hover {
            background: black;
        }

        .container {
            max-width: 500px;
        }
        .container {
            max-width: 500px;
        }

        .container form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        input[type="submit"], #log, .sub {
            background-color: rgba(255, 111, 0);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover, #log:hover, .sub:hover {
            background-color: black;
        }

        #pro {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            border: 2px solid #000;
            object-fit: cover;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            color: #fff;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 3;
        }

        .modal-content {
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            max-width: 800px;
            width: 800%;
            max-height: 80%;
            overflow-y: auto;
            margin: auto;
            padding: 20px;
            color: #fff;
            display: flex;
            flex-direction: column;
            height: fit-content;
        }

        .modal-content img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-top: 20px;
            margin-bottom: 15px;
            object-fit: cover;
        }

        .form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .text {
            margin-bottom: 20px;
            text-align: center;
            color: #fff;
        }

        .text1 {
            font-size: 16px;
            margin-bottom: 5px;
            width: 100%;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .n {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            box-sizing: border-box;
            display: flex;
            flex-direction: horizontal;
        }

        .sub {
            background: rgba(255, 111, 0);
            color: #fff;
            padding: 8px;
            margin: 5px auto;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }

        .sub:hover {
            background: black;
        }
        #userInfo {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            width: 100%;
            margin-top: 20px; /* Add margin for separation */
        }

        #userInfo label {
            display: block;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <img src="<?php echo (!empty($resultImagname)) ? 'uploads/' . $resultImagname : 'teacprof.jpg'; ?>" alt=""
             id="pro">
           

             <input type="hidden" name="update_action" value="update_form">
    <button class="sub" type="button" name="update_submit" id="updateButton" onclick="openUpdateForm()">Update</button>
    <div id="userInfo">
        <label>Name: <?php echo $resultName; ?></label>
        <label>Phone: <?php echo $resultPhone; ?></label>
        <label>Experience: <?php echo $resultExperience; ?></label>
    </div>

    </form>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="logoutForm" method="post">
        <label for="name">Name: <?php echo $resultUsername; ?></label><br>

        <label for="message">Enter your message:</label>
        <textarea id="message" name="message" rows="4" cols="50" required></textarea><br>
        <div id="result-message"><?php echo $resultMessage; ?></div>
        <input type="hidden" name="email_action" value="send_email">
        <input type="submit" value="Send Email">
        <button id="log" type="button">Logout</button>
        <a href="update_details.php">Update Details</a>
    </form>
</div>
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-content">
        <?php
        if(isset($_GET['error'])): ?>
        <p> <?php echo $_GET['error'] ?></p>
        <?php endif ?>
        <!-- Update form content -->
        <span class="close" onclick="closeModal()">&times;</span>
        <form class="form" method="post" action="" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="update_action" value="update_form">
            <h3 class="text">Update Your Details</h3>

            <img src="<?php echo (!empty($resultImagname)) ? 'uploads/' . $resultImagname : 'teacprof.jpg'; ?>" alt="" id="profil">
            <label for="input-file" name="input_file" id="mo" >Update Image</label><br>
            <input type="file" id="input-file" name="input_file" class="me" accept="image/jpeg, image/png, image/jpg">

            
                <label for="name" class="text1">Name:</label>
                <input type="text" id="name" class="n" name="uname" value="<?php echo $resultName; ?>" autocomplete="on" required>
            
            <label for="phone" class="text1">Phone:</label>
            <input type="number" class="n" id="phone" name="uphone" value="<?php echo $resultPhone; ?>"autocomplete="on" required>

            <label for="dob" class="text1">Date of Birth:</label>
            <input type="date" class="n" id="dob" name="udob" value="<?php echo $resultDob; ?>"  required>

            <label for="occup" class="text1">Occupation:</label>
            <input type="text" class="n" id="occup" name="uoccup" value="<?php echo $resultOccupation; ?>" autocomplete="on" required>

            <label for="age" class="text1">Age:</label>
            <input type="number" id="age" class="n" name="uage" value="<?php echo $resultAge; ?>" autocomplete="on" required>

 <label for="expe" class="text1">Experience:</label>
            <input type="text" class="n" id="expe" name="uexpe" value="<?php echo $resultExperience; ?>"  autocomplete="on" required>

            <input type="hidden" id="imageFileName" name="imageFileName">
            <button class="sub" type="submit"  value="submit" name="update_submit" onclick="closeModal()">Update</button>
        </form>
    </div>
</div>



<?php if (!isset($_SESSION['username'])): ?>
    <script>
        window.onload = function () {
            // If not logged in, show an alert
            alert("LOGIN FIRST!!");
            // Redirect to the login page or any other appropriate action  
            window.location.href = "firstmain.php";
        };
    </script>
<?php endif; ?>

    <script>
         document.getElementById('updateButton').addEventListener('click', function () {
        openUpdateForm();
    });
        
    function openUpdateForm() {
        document.getElementById("modalOverlay").style.display = "flex";
       
    }

    function closeModal() {
        document.getElementById("modalOverlay").style.display = "none";
        document.getElementById("input-file").style.display = "none";
    }
    document.getElementById('log').addEventListener('click', function() {
        window.location.href = 'logout.php';
    });

    let profilePic = document.getElementById("profil");
    let inputFile = document.getElementById("input-file");

    if (inputFile && profilePic) {
        inputFile.addEventListener("change", function () {
            document.getElementById('imageFileName').value = inputFile.files[0].name;

            if (inputFile.files.length > 0) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    profilePic.src = e.target.result;
                };

                reader.readAsDataURL(inputFile.files[0]);
            } else {
                console.error("No file selected");
            }
        });
    } else {
        console.error("Element with ID 'input-file' or 'profil' not found");
    }
    document.addEventListener("DOMContentLoaded", function() {
        var inactivityTime = 0;
        var timeout = 15 * 60 * 1000;  // 1 minute

        function resetTimer() {
            inactivityTime = 0;
        }

        document.addEventListener("mousemove", resetTimer);
        document.addEventListener("keypress", resetTimer);

        function startLogoutTimer() {
            return setInterval(checkInactivity, 1000);  // Check every second
        }

        function checkInactivity() {
            inactivityTime += 1000;

            if (inactivityTime >= timeout) {
                // Redirect to the sess.php or any other appropriate action
                window.location.href = 'sess.php';
            }
        }

        var logoutTimer = startLogoutTimer();

        // Stop the timer if the user is active after page load
        document.addEventListener("mousemove", function() {
            clearInterval(logoutTimer);
            logoutTimer = startLogoutTimer();
        });

        document.addEventListener("keypress", function() {
            clearInterval(logoutTimer);
            logoutTimer = startLogoutTimer();
        });
    });

    
</script>
</body>
</html>