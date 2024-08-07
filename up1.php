<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
 

 use PHPMailer\PHPMailer\PHPMailer;
 use PHPMailer\PHPMailer\Exception;
 // Include PHPMailer files
 require './PHPMailer/src/Exception.php';
 require './PHPMailer/src/PHPMailer.php';
 require './PHPMailer/src/SMTP.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user input
    $name1 = htmlspecialchars($_POST["name1"]);
    $email1 = htmlspecialchars($_POST["email1"]);
    $message1 = htmlspecialchars($_POST["message1"]);
   

   

    $mail = new PHPMailer(true);

   

    try {
        // Server settings
        $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Your SMTP server
                    $mail->SMTPAuth = true;
                    $mail->Username = 'eggeye502@gmail.com'; // Your SMTP username
                    $mail->Password = 'elkmzzgckwoufvpp'; // Your SMTP password
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port = 465;
            
                    // Recipients
                    $mail->setFrom('eggeye502@gmail.com', 'Teaching studio');
                    $mail->addAddress('kprabhukumar594@gmail.com');
            
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to Our Gym!';
        $mail->Body = "Name: $name1\nEmail: $email1\n\n$message1";

        // Send email
        $mail->send();
        
        echo '<script>alert("Your message has been sent successfully.");</script>';
    } catch (Exception $e) {
        echo 'Error sending email: ' . $e->getMessage();
    }
}




session_start();
// Database connection parameters
include('kk.php');
// access
$sessionUsername = '';

// Access the variable from the session
if (isset($_SESSION['username'])) {
    $sessionUsername = $_SESSION['username'];
} else {
    $_SESSION['error'] = "Username not set";
    echo "not";
}

$sql = "SELECT name FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

// Check if the query was successful before binding the result
if ($stmt) {
    $stmt->bind_param("s", $sessionUsername);
    $stmt->execute();
    $stmt->bind_result($nameu);
    $stmt->fetch();
    //echo "Name: " . $resultUsername;
    $stmt->close();
} else {
    echo "Error preparing statement";
}
$sessionImagename = '';

// Access the variable from the session
if (isset($_SESSION['username'])) {
    $sessionImagename = $_SESSION['username'];
} else {
    $_SESSION['error'] = "Username not set";
    echo "not";
}

$sql = "SELECT images FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

// Check if the query was successful before binding the result
if ($stmt) {
    $stmt->bind_param("s", $sessionImagename);
    $stmt->execute();
    $stmt->bind_result($resultImagename);
    $stmt->fetch();
    //echo "Name: " . $resultUsername;
    $stmt->close();
} else {
    echo "Error preparing statement";
}
$sql = "SELECT name, phoneno, dob, occupation, age, images FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $sessionUsername);
    $stmt->execute();
    $stmt->bind_result($resultName, $resultPhone, $resultDob, $resultOccupation, $resultAge, $resultImagename);
    $stmt->fetch();
    $stmt->close();
} else {
    echo 'Error preparing statement';
}

// Check if the form is submitted for user details update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_action']) && $_POST['update_action'] === "update_form") {
    // Retrieve and sanitize form data
    $name = isset($_POST['names']) ? $_POST['names'] : null;
    $phone = isset($_POST['phones']) ? $_POST['phones'] : null;
    $dob = isset($_POST['dobs']) ? $_POST['dobs'] : null;
    $occupation = isset($_POST['occups']) ? $_POST['occups'] : null;
    $age = isset($_POST['ages']) ? $_POST['ages'] : null;

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
            if ($img_size > 125000) {
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
                $sql = "UPDATE users SET images = ? WHERE username = ?";
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
                    header("Location: up1.php");
                    exit();
                } else {
                    echo "Error preparing statement for image update: " . $conn->error;
                }
            } else {
                $em = "You can't upload files of this type.";
                header("Location: up1.php?error=$em");
                exit();
            }
        } else {
            $em = "Unknown error occurred!";
            header("Location: up1.php?error=$em");
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
    

    // Update user details in the database
    if (!empty($updateFields)) {
        $updateFieldsStr = implode(", ", $updateFields);

        // Construct the dynamic query for other fields
        $sql = "UPDATE users SET $updateFieldsStr";
        if ($updateImage) {
            $sql .= ", images = ?";
            $updateParams[] = $resultImagname;
        }
        $sql .= " WHERE username = ?";
        $updateParams[] = $sessionUsername;
        $stmt = $conn->prepare($sql);
         $nameu= $name ;
        if ($stmt) {
            $types = str_repeat('s', count($updateParams));
            $stmt->bind_param($types, ...$updateParams);

            $stmt->execute();

         //   if ($stmt->affected_rows > 0) {
          //      echo "User details updated successfully!";
          //  } else {
              //  echo "No rows were affected. Check your SQL query and database.";
           // } 

            $stmt->close();
        } else {
            echo "Error preparing statement";
        }
    }
}

$sql = "SELECT name, phoneno, dob, occupation, age FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $sessionUsername);
    $stmt->execute();
    $stmt->bind_result($resultName, $resultPhone, $resultDob, $resultOccupation, $resultAge);
    $stmt->fetch();
    $stmt->close();
} else {
    echo 'Error preparing statement';
}
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

// Check if the query was successful before binding the result
if ($stmt) {
    $stmt->bind_param("s", $sessionUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Fetch values from the database
        $height = $row['height'];
        $weight = $row['weight'];
        $bodyfat = $row['bodyfat'];
        $visceralfat = $row['visceralfat'];
        $RMR = $row['RMR'];
        $BMI = $row['BMI'];
        $subcutfat = $row['subcutfat'];
        $skeletmusc = $row['skeletmusc'];
        $BFM = $row['BFM'];
        $sugar = $row['sugar'];
        $BP = $row['BP'];

        // Fetch ideal values from the ideal table
        $sqlIdeal = "SELECT * FROM ideal WHERE height >= ? ORDER BY height DESC LIMIT 1";
        $stmtIdeal = $conn->prepare($sqlIdeal);

        if ($stmtIdeal) {
            $stmtIdeal->bind_param("d", $height);
            $stmtIdeal->execute();
            $resultIdeal = $stmtIdeal->get_result();

            if ($resultIdeal->num_rows > 0) {
                $rowIdeal = $resultIdeal->fetch_assoc();

                // Fetch ideal values
                $idealWeight = $rowIdeal['idealweight'];
                $idealBodyFat = $rowIdeal['idealbodyfat'];
                $idealVisceralFat = $rowIdeal['idealvisceralfat'];
                $idealRMR = $rowIdeal['idealRMR'];
                $idealBMI = $rowIdeal['idealBMI'];
                $idealSubcutaneousFat = $rowIdeal['idealsubcutfat'];
                $idealSkeletalMuscle = $rowIdeal['idealskeletmusc'];
                $idealBFM = $rowIdeal['idealBFM'];

                // Calculate target values
                $targetWeight = $idealWeight - $weight;
                $targetBodyFat = $idealBodyFat - $bodyfat;
                $targetVisceralFat = $idealVisceralFat - $visceralfat;
                $targetRMR = $idealRMR - $RMR;
                $targetBMI = $idealBMI - $BMI;
                $targetSubcutaneousFat = $idealSubcutaneousFat - $subcutfat;
                $targetSkeletalMuscle = $idealSkeletalMuscle - $skeletmusc;
                $targetBFM = $idealBFM - $BFM;

// The rest of your HTML and JavaScript code...
} else {
    // Initialize user details if no data is found in the users table
    $height = $weight = $bodyfat = $visceralfat = $RMR = $BMI = $subcutfat = $skeletmusc = $BFM = $sugar = $BP = 0;

    // Set all ideal and target values to "N/A"
    $idealWeight = $targetWeight = $idealBodyFat = $targetBodyFat = $idealVisceralFat = $idealRMR = $idealBMI = $idealSubcutaneousFat =  $targetSubcutaneousFat =  $idealSkeletalMuscle = $targetSkeletalMuscle = $idealBFM =  $targetBMI = $targetRMR = $targetBFM =  $targetVisceralFat =  "";
    // ... (set other values to "N/A")
}
} else {
// Error preparing the statement
echo "Error preparing statement";
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Training Studio</title>
    <style>
        .form-group {
    margin: 0;
    padding:0;
}
body {
    margin: 0%;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background: url("gymback.jpg") no-repeat center;  
    background-size: cover; /*Use 'contain' instead of 'cover'*/

    background-position: center;
    height: 100vh;
    align-items: center;
    justify-content: center;
    display: block;
    max-width: 100%;
    /* Prevent scrolling in the x-axis direction */
    overflow-x: hidden;
}


header {
    position: fixed;
    top: 0;
    background-color: rgba(0, 0, 0, 0);
    width: 100%;
    height: 10vh;
    margin-top:0;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 2;
    transition: background-color 0.3s;
    margin-left:-10%;
}
header.sticky-scroll {
        background-color: rgba(255,111,0); /* your desired background color when scrolling */;
   
    }

header.sticky {
    padding: 5px 200px;
   
   /* background: rgba(0,0,0,0);  Add a background color for sticky state */
}
header.logo{
    margin-left: 1%;
}
header ul{
   text-align: center;
    
}

header.sticky ul li {
  list-style: none; 
  display: inline-block;
  margin-right: 15px;
}
header.sticky ul li a{
 text-decoration: none;
 text-transform: capitalize;
  display: block;
  font-size: 20px;
}
header.sticky ul li img {
    margin-right: 5px; /* Adjust the margin-right value as needed */
}

/* Add margin-left to the "user" class in the navigation bar */
header.sticky ul li.user {
    margin-left: -20px; /* Adjust the margin-left value as needed */
}
/* Add this to your existing CSS */
/* Add this to your existing CSS */


.logo{
    font-size: 18px;
    font-weight: bold;
    letter-spacing: 0px;
    cursor: pointer;
 margin-top:2%;
}
h1 {
    margin: 0;
}
nav li{
    margin-top: 4%;
    display: inline-block;
    list-style: none;
    font-size: 20px;
    margin: -10px;
}
nav {
    display: flex;
}

nav a {
    color: black;
    text-decoration: none;
    padding: 10px;
    margin: 0 10px;
    transition: color 0.3s;
}
.ho{
    
    transition: color 0.3s;
}
.ho:hover{
    color: white;
}
        .prof {
            width: 30px;
            height: 30px;
            border-radius: 70%;
            margin-top: 20px;
            margin-bottom: 2.5px;
            margin-left: 5px;
            margin-right: -10px;
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
 .form {
    display: flex;
    
}
.modal-content {
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 10px;
    max-width: 800px; /* Adjust the width as needed */
    width: 800%;
    max-height: 80%; /* Set the maximum height for scrolling */
    overflow-y: auto; 
    margin: auto; /* Center the form horizontally */
    padding: 20px;
    color: #fff;
    display: flex;
    flex-direction: column;
    height:fit-content;
        }
 #mo{
        display: block;
        width: 200px;
        background: rgba(255,111,0);
        color: #fff;
        padding : 8px;
        margin: 5px auto;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
    }
    .me{
        justify-content: center;
        text-align: center;
        display: none;
    }
    .modal-content img{
        width: 150px;
        height: 150px;
        border-radius: 50%;
        margin-top: 20px;
        margin-bottom: 15px;
    }

    .form {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .text {
      margin-bottom: 20px;
      text-align: center;
    }

    .text1 {
        font-size: 16px;
      margin-bottom: 5px;
      width: 100%;
      color: white; 
      display: flex; /* Add this to make label and input side by side */
    justify-content: space-between; /* Add this to space them apart */
    align-items: center;
    display: flex;
      flex-direction: horizontal;
    }

    .n{
      width:100%; 
      padding: 8px;
      margin: 5px 0;
      box-sizing: border-box;
      
      display: flex;
      flex-direction: horizontal;
    }
    .sub {
  display: block;
  width: 70px;
  background: rgba(255, 111, 0);
 
  padding: 8px;
  margin: 5px auto;
  border-radius: 5px;
  cursor: pointer;
  border: none; /* Add this line to remove the border */
}
.form input {
    width: 100%; /* Adjust the width value as needed */
    padding: 8px;
    margin: 5px 0;
    box-sizing: border-box;
}

       .modal-content label{
        color: white;
       } 


        #profile {
            width: 100px;
            height: 100px;
            border-radius: 70%;
            justify-content: center;
        }
        .user:hover{
            color: white;
        }
        .user {
                margin-right: -30px;
            }

        @media screen and (max-width: 800px) {
            header.sticky {
                padding: 5px ;
            }

            nav a {
                padding: 5px;
                margin: 0px 5px;
            }}
.modal-overlay form label{
    color: #fff;
}
        

/* Add this CSS to your existing styles */
table {
    width: 80%;
    border-collapse: collapse;
    margin: 20px;
    overflow-x: hidden;
    max-width: 100%;
    border-color: #000; 
}

table, th, td {
    border: 1px solid #000;
    text-align: left;
}

th, td {
    padding: 15px;
}

th {
    background-color: #f2f2f2;
}

.tob {
    margin-top: 120px;
    margin-bottom: 20px;
    overflow-y: auto;
    max-height: 80vh;
    min-width: 50%;
    overflow-x: hidden;
    
    margin-left: 5%;
}
.table-container {
    margin-top: 120px;
    margin-bottom: 20px;
    overflow-y: auto;
    max-height: 80vh;
    min-width: 50%;
    margin-left: 10%;/* Add float: left; to position it on the left */
}
#bb{
 margin-left: 5%;
 padding-right: 5%;
}

li{
    margin-left:3%;
}
/* Add this to your existing styles */
.dropdown {
    position: relative;
    display: inline-block;
}

.dropbtn {
    color: black;
    text-decoration: none;
    padding: 10px;
    margin: 0 10px;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 120px;
    box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.dropdown-content a {
    color: white;
    padding: 10px 14px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: rgba(255,111,0) ;
 
}

.dropdown:hover .dropdown-content {
    display: block;
    color: white;
    background-color: black;
}
#about{
    margin-top: 10%;
}
.cont {
    position: relative;
    z-index: 1; /* Ensure it's above the video */
    top: 50%;
    left: 50%;
    text-align: center;
    transform: translate(-50%, -50%);
}
.row {
   display: flex;
   align-items: center;
   justify-content: space-around;
   flex-wrap: wrap;
}
.row a{
    text-decoration: none;
    margin-bottom: auto;
}
.card{
    flex: 0 0 calc(30% - 20px);
    margin: 10px;
    background: #fff;
box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
padding:20px 20px;
align-self: normal;
display: flex;
flex-direction: column;
align-items: center;
}
.card img{
   max-width: 200px;

}
.card:hover {
    transform: scale(1.1);
    transition: transform 0.3s ease-in-out;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.6);
}
.card h4{
    font-size: 1.3rem;
    text-transform: uppercase;
    margin: 10px 0;
}
.card.btn{
    display: inline-block;
    background: green;
    color:#fff;
text-align: center;
color:#fff;
padding:10px 20px;
border-radius:5px;
-webkit-border-radius:5px;
-moz-border-radius:5px;
-ms-border-radius:5px;
-o-border-radius:5px;
margin-top:auto;
}
.card p{
    margin-bottom: 10px;
}
.container{
    width: 1100px;
    max-width: 100%;
    margin: 0 auto;
    }
    @media screen and (max-width: 1024px) {
            .card {
                flex: 0 0 calc(48% - 20px);
            }
        }

        @media screen and (max-width: 768px) {
            .card {
                flex: 0 0 calc(100% - 20px);
            }
        }
        #schedules {
        background-color: #fff;
        color: rgb(255, 111, 0);
        padding: 50px;
        width: 100%;
        height: 100%;
    }

    .class-details-timetable_title h5 {
        color: #fd7e14;
        margin-bottom: 20px;
    }

    .class-timetable th,
    .class-timetable td {
        border: 1px solid #333;
        padding: 20px;
        text-align: center;
    }

    .class-timetable th {
        background-color: #333;
        color: #fd7e14;
    }

    .class-timetable td.blank-td {
        background-color: #000;
    }

    .class-timetable td.dark-bg {
        background-color: #333;
        color: #fd7e14;
    }

    .class-timetable td.hover-dp:hover {
        background-color: #fd7e14;
        color: #000;
        cursor: pointer;
    }

    .class-time {
        color: #fd7e14;
        font-weight: bold;
    }
    .contai{
        width: 100%;
        max-width: 100%;
    }
    .contain {
        margin: 50px 0;
    }

    /* Section Heading Styles */
    .section-heading {
        text-align: center;
        margin-bottom: 30px;
    }
    .section{
        display: flex;
        padding:20px;
    }

    .section-heading h2 {
        font-size: 36px;
        margin-bottom: 10px;
        color: #000; /* Dark text color */
    }

    .section-heading img {
        margin: 20px auto;
        display: block;
    }

    .section-heading p {
        font-size: 18px;
        color: #000; /* Medium gray text color */
    }

    /* Tabs Styles */
    #tabs {
        margin-top: 30px;
        border-bottom: 1px solid #ddd; /* Light gray border */
        display: flex;
    }

    .ui-tabs-nav {
        background-color: #fff; /* White background color */
        border: none;
        max-width: 100%;
    }

    .ui-tabs-nav li {
        float: left;
        margin: 0;
    }

    .ui-tabs-anchor {
        display: block;
        padding: 15px 20px;
        text-decoration: none;
        color: #000; /* Dark text color */
    }

    .ui-tabs-anchor:hover {
        background-color: #f8f8f8; /* Light gray background color on hover */
        color:black;
    }

    .ui-tabs-active {
        background-color: rgba(255,111,0); /* Orange background color for the active tab */
    }

    .ui-tabs-active .ui-tabs-anchor {
        color: #fff; /* White text color for the active tab */
    }
    .ui-tabs-active .ui-tabs-anchor:hover {
        color: #000; /* White text color for the active tab */
    }
 
    .col-lg-4 {
    display: flex;
    flex-direction: column;
    margin-left: 5%;
    width: 30%;
}

.col-lg-4 li {
    flex-grow: 3;
    margin-bottom: 10px;
    height: 100%;
    width: 300px; /* Adjust the margin as needed for the desired gap */
}
.col-lg-8 {
    padding: 20px;
    width: 60%;
}
    /* Tabs Content Styles */
    .tabs-content img {
        max-width: 100%;
        height: auto;
        margin-bottom: 20px;
    }

    .tabs-content h4 {
        font-size: 24px;
        color: #000; /* Dark text color */
        margin-bottom: 10px;
    }

    .tabs-content p {
        font-size: 16px;
        color: #000; /* Medium gray text color */
        line-height: 1.6;
    }
    .tabs-content  {
      max-width: 100%;
    }
    .contactsuu {
        background-color: white;
        padding: 50px ;
        text-align: center;
       
    }

    .contact-heading {
        color: rgb(255, 111, 0);
        font-size: 2em;
        margin-bottom: 20px;
    }

    .contact-info {
        color: #000;
        font-size: 1.2em;
        margin-bottom: 30px;
    }

    .contact-form {
        max-width: 600px;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .form-submit {
        background-color: rgb(255, 111, 0);
        color: #fff;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1.2em;
    }
    .form-input:focus {
        border-color: rgb(255, 111, 0);
        outline: none;
        box-shadow: 0 0 5px rgba(255, 111, 0, 0.5);
    }
    #free{
        width: 100%;
        height: 100%;
       align-content: center;
       
    }
    .close{
        text-align: right;
    }
   
    </style>
</head>
<body>
    <header class="sticky">
        <div class="logo">
            <h1 class="he">Training <span style="color: white;">Studio</span></h1>
        </div>
        <div id="bb">     
            <nav id="navbar">
            <ul>
                <li><a href="#hom" class="ho">Home</a></li>
                <li><a href="#about" class="ho">About</a></li>
                <li><a href="#classes" class="ho">Classes</a></li>
                <li><a href="#schedules" class="ho">Schedules</a></li>
                <li><a href="#contact" class="ho">Contact</a></li>
                <li><img src="<?php echo (!empty($resultImagename)) ? 'uploads/' . $resultImagename : 'profile.jpg'; ?>" alt="" class="prof" id="pro"></li>
                <li class="user"><div class="dropdown">
        <a href="#" class="dropbtn"><?php echo $nameu ; ?></a>
        <div class="dropdown-content">
            <a href="#" onclick="openUpdateForm()">Update</a>
            <a href="logout.php">Logout</a>
        </div>
    </div></li>
                
            </ul>
        </nav>
    </div>
   </header>
   <section id="hom">
   <div class="tob">
   <?php
    if ($result->num_rows > 0) {
        // User data is available, display the table
    ?>
        <h2>User Details:</h2>
        <table>
            <tr>
                <td colspan="1">Username</td>
                <td><?php echo $sessionUsername; ?></td>
            </tr>
            <tr>
                <th>Property</th>
                <th>Your Info</th>
                <th>Ideal Values</th>
                <th>Target Value</th>
            </tr>
            <tr>
                <td colspan="1">Height</td>
                <td><?php echo $height; ?> cm</td>
            </tr>
            <tr>
                <td>Weight</td>
                <td><?php echo $weight; ?> kg</td>
                <td><?php echo $idealWeight; ?> kg</td>
                <td><?php echo $targetWeight; ?> kg</td>
            </tr>
            <tr>
                <td>Body Fat</td>
                <td><?php echo $bodyfat; ?> %</td>
                <td><?php echo $idealBodyFat; ?> %</td>
                <td><?php echo $targetBodyFat; ?> %</td>
            </tr>
            <tr>
                <td>Visceral Fat level</td>
                <td><?php echo $visceralfat; ?> kg</td>
                <td><?php echo $idealVisceralFat; ?> kg</td>
                <td><?php echo $targetVisceralFat; ?> kg</td>
            </tr>
            <tr>
                <td>Resting Metabolic Rate(RMR)</td>
                <td><?php echo $RMR; ?> Calts</td>
                <td><?php echo $idealRMR; ?> Calts</td>
                <td><?php echo $targetRMR; ?> Calts</td>
            </tr>
            <tr>
                <td>BMI</td>
                <td><?php echo $BMI; ?> kg</td>
                <td><?php echo $idealBMI; ?> kg</td>
                <td><?php echo $targetBMI; ?> kg</td>
            </tr>
            <tr>
                <td>Subcutaneous Fat</td>
                <td><?php echo $subcutfat; ?> kg</td>
                <td><?php echo $idealSubcutaneousFat; ?> kg</td>
                <td><?php echo $targetSubcutaneousFat; ?> kg</td>
            </tr>
            <tr>
                <td>Skeletal Muscle</td>
                <td><?php echo $skeletmusc; ?> kg</td>
                <td><?php echo $idealSkeletalMuscle; ?> kg</td>
                <td><?php echo $targetSkeletalMuscle; ?> kg</td>
            </tr>
            <tr>
                <td>Body Fat Mass</td>
                <td><?php echo $BFM; ?> kg</td>
                <td><?php echo $idealBFM; ?> kg</td>
                <td><?php echo $targetBFM; ?> kg</td>
            </tr>
            <tr>
                <td colspan="1">Sugar</td>
                <td><?php echo $sugar; ?></td>
            </tr>
            <tr>
                <td colspan="1">BP</td>
                <td><?php echo $BP; ?></td>
            </tr>
        </table>
        </table>
        <?php
            }
        }
    }

?>
 </div>  
</section> 
<section id="about">
            <div class="cont">
                <h2 id="about0"><span style="color: rgb(255, 111, 0);">About</span>  <span id="about1.1" style="color: #0c0224";>Us</h2>
                <h3 id="about1"><span id="about1.1" style="color: #0c0224";>OUR</span>  <span id="about1.2" style="color: rgb(255, 111, 0);">PROGRAMS</span></h3>
                <p>Empower your fitness journey at our state-of-the-art gym, where passion meets performance. Achieve your wellness goals with personalized training and cutting-edge facilities.</p>
            </div>
            
            <div class="container">
                <div class="row">
                    <div class="card">
                        <img src="https://themewagon.github.io/training-studio/assets/images/features-first-icon.png" alt="">
                        <h4>Basic Fitness</h4>
                        <p>Basic fitness encompasses fundamental exercises and activities aimed at 
                            enhancing overall health and well-being. 
                            It lays the foundation for strength, flexibility,
                            and cardiovascular endurance, fostering a solid
                            starting point for individuals on their fitness
                            journey.</p>
                        <a href="#" class="btn" ><span style="color: rgb(255, 111, 0);">Read More</span></a>
                    </div>
                    
                    <div class="card">
                        <img src="https://themewagon.github.io/training-studio/assets/images/features-first-icon.png" alt="">
                        <h4>Advanced Muscle Course</h4>
                        <p>
                            Advance muscle training in the gym involves progressive resistance exercises targeting specific muscle groups, optimizing hypertrophy, strength, and overall athletic performance for advanced fitness enthusiasts.</p>
                        <a href="#" class="btn"><span style="color: rgb(255, 111, 0);">Read More</span></a>
                    </div>
                    
                    <div class="card">
                        <img src="https://themewagon.github.io/training-studio/assets/images/features-first-icon.png" alt="">
                        <h4>New Gym Training</h4>
                        <p>
                            New gym training incorporates cutting-edge fitness techniques and innovative equipment, providing a dynamic and holistic approach to workouts for optimal results and a fresh fitness experience.</p>
                        <a href="#" class="btn"><span style="color: rgb(255, 111, 0);">Read More</span></a>
                    </div>
                </div>
                <br><br>
                
                <div class="row">
                    <div class="card">
                        <img src="https://themewagon.github.io/training-studio/assets/images/features-first-icon.png" alt="">
                        <h4>Yoga Training</h4>
                        <p>Explore the serenity of yoga within the dynamic environment of our gym, where expert instructors guide you through transformative sessions, combining strength-building and mindfulness for a holistic fitness experience.</p>
                        <a href="#" class="btn"><span style="color: rgb(255, 111, 0);">Read More</span></a>
                    </div>
                    
                    <div class="card">
                        <img src="https://themewagon.github.io/training-studio/assets/images/features-first-icon.png" alt="">
                        <h4>Basic Muscle Course</h4>
                        <p>Strengthen your foundation with our basic muscle course, focusing on fundamental exercises for overall fitness and strength development.</p>
                        <a href="#" class="btn"><span style="color: rgb(255, 111, 0);">Read More</span></a>
                    </div>
                    
                    <div class="card">
                        <img src="https://themewagon.github.io/training-studio/assets/images/features-first-icon.png" alt="">
                        <h4>Body Building Course</h4>
                        <p>Our bodybuilding course at the gym focuses on comprehensive strength training, personalized workout regimens, and expert guidance to sculpt and develop muscle mass, fostering a robust foundation for a powerful physique.</p>
                        <a href="#" class="btn" ><span style="color: rgb(255, 111, 0);">Read More</span></a>
                    </div>
                </div>
            </div>
        </section>
        <section class="section" id="classes">
        <div class="contain">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="section-heading">
                        <h2>Our <em>Classes</em></h2>
                        <p> classes are instructor-led fitness sessions, often in a group setting, focusing on various exercises such as cardio, strength training, and flexibility, tailored to different fitness levels and goals.</p>
                    </div>
                </div>
            </div>
            <div class="row ui-tabs ui-widget ui-widget-content ui-corner-all" id="tabs">
              <div class="col-lg-4">
                <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
                  <li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="-1" aria-controls="tabs-1" aria-labelledby="ui-id-1" aria-selected="true" aria-expanded="true"><a href="#tabs-1" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1"><img src="https://themewagon.github.io/training-studio/assets/images/tabs-first-icon.png" alt="">First Training Class</a></li><br>
                  <br><br>
                  <li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="-1" aria-controls="tabs-2" aria-labelledby="ui-id-2" aria-selected="false" aria-expanded="false"><a href="#tabs-2" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2"><img src="https://themewagon.github.io/training-studio/assets/images/tabs-first-icon.png" alt="">Second Training Class</a></li><br>
                  <br><br>
                  <li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="-1" aria-controls="tabs-3" aria-labelledby="ui-id-3" aria-selected="false" aria-expanded="false"><a href="#tabs-3" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-3"><img src="https://themewagon.github.io/training-studio/assets/images/tabs-first-icon.png" alt="">Third Training Class</a></li><br>
                  <br><br>
                  <li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="-1" aria-controls="tabs-4" aria-labelledby="ui-id-4" aria-selected="false" aria-expanded="false"><a href="#tabs-4" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-4"><img src="https://themewagon.github.io/training-studio/assets/images/tabs-first-icon.png" alt="">Fourth Training Class</a></li><br>
                  <br><br>
                </ul>
              </div>
              <div class="col-lg-8">
                <section class="tabs-content">
                  <article id="tabs-1" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="false" style="display: block;">
                    <img src="https://themewagon.github.io/training-studio/assets/images/training-image-01.jpg" alt="First Class">
                    <h4>First Training Class</h4>
                    <p>Weight lifting, or resistance training, involves lifting weights to build strength, muscle endurance, and enhance overall fitness. It targets various muscle groups through exercises like squats, deadlifts, and bench presses. Regular weight lifting contributes to improved metabolism, bone density, and functional strength.</p>
                   
                  </article>
                  <article id="tabs-2" aria-labelledby="ui-id-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="true" style="display: none;">
                    <img src="https://themewagon.github.io/training-studio/assets/images/training-image-02.jpg" alt="Second Training">
                    <h4>Second Training Class</h4>
                    <p>Plank Jacks are a dynamic bodyweight exercise performed from a plank position. It involves simultaneously jumping legs outward and inward, engaging multiple muscle groups for improved cardiovascular fitness and core strength. This exercise helps enhance agility and endurance while targeting the abdominal muscles.</p>
                
                  </article>
                  <article id="tabs-3" aria-labelledby="ui-id-3" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="true" style="display: none;">
                    <img src="https://themewagon.github.io/training-studio/assets/images/training-image-03.jpg" alt="Third Class">
                    <h4>Third Training Class</h4>
                    <p>Muscle building, or hypertrophy, is the process of increasing muscle size through resistance training and proper nutrition. This involves subjecting muscles to progressively heavier loads, causing microscopic damage that the body repairs and overcompensates, leading to muscle growth.</p>
                   
                  </article>
                  <article id="tabs-4" aria-labelledby="ui-id-4" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="true" style="display: none;">
                    <img src="https://themewagon.github.io/training-studio/assets/images/training-image-04.jpg" alt="Fourth Training">
                    <h4>Fourth Training Class</h4>
                    <p>Backside boost exercises, like glute bridges and hip thrusts, target the glute muscles, enhancing strength and definition. These movements contribute to better posture, improved athletic performance, and reduced risk of lower back pain by engaging and strengthening the muscles in the posterior chain.</p>
                   
                  </article>
                </section>
              </div>
            </div>
        </div>
    </section>
        <section id="schedules"class="class-timetable-section class-details-timetable spad">
        <div class="contai">
            <div class="rows">
                <div class="col-lg-12">
                    <div class="class-details-timetable_title">
                        <h2>Classes timetable</h2>
                    </div>
                </div>
            </div>
            <div class="rows">
                <div class="col-lg-12">
                    <div class="class-timetable details-timetable">
                        <table>
                            <thead>
                                <tr>
                                    <th>Week</th>
                                    <th>Monday</th>
                                    <th>Tuesday</th>
                                    <th>Wednesday</th>
                                    <th>Thursday</th>
                                    <th>Friday</th>
                                    <th>Saturday</th>
                                    <th>Sunday</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="class-time">6.00am - 8.00am</td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="workout">
                                        <h5>WEIGHT LOOSE</h5>
                                        
                                    </td>
                                    <td class="hover-dp ts-meta" data-tsmeta="fitness">
                                        <h5>Cardio</h5>
                                       
                                    </td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="workout">
                                        <h5>Yoga</h5>
                                       
                                    </td>
                                    <td class="hover-dp ts-meta" data-tsmeta="fitness">
                                        <h5>Fitness</h5>
                                       
                                    </td>
                                    <td class="dark-bg blank-td">
                                    <h5>No Class</h5>
                                    </td>
                                    <td class="hover-dp ts-meta" data-tsmeta="motivation">
                                        <h5>Boxing</h5>
                                       
                                    </td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="workout">
                                        <h5>Body Building</h5>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td class="class-time">10.00am - 12.00am</td>
                                    <td class="blank-td"><h5>No Class</h5></td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="fitness">
                                        <h5>Fitness</h5>
                                       
                                    </td>
                                    <td class="hover-dp ts-meta" data-tsmeta="workout">
                                        <h5>WEIGHT LOOSE</h5>
                                       
                                    </td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="motivation">
                                        <h5>Cardio</h5>
                                       
                                    </td>
                                    <td class="hover-dp ts-meta" data-tsmeta="workout">
                                        <h5>Body Building</h5>
                                       
                                    </td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="motivation">
                                        <h5>Karate</h5>
                                       
                                    </td>
                                    <td class="blank-td"><h5>No Class</h5></td>
                                </tr>
                                <tr>
                                    <td class="class-time">5.00pm - 7.00pm</td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="fitness">
                                        <h5>Boxing</h5>
                                    
                                    </td>
                                    <td class="hover-dp ts-meta" data-tsmeta="motivation">
                                        <h5>Karate</h5>
                                      
                                    </td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="workout">
                                        <h5>Body Building</h5>
                                    
                                    </td>
                                    <td class="blank-td"><h5>No Class</h5></td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="workout">
                                        <h5>Yoga</h5>
                                        
                                    </td>
                                    <td class="hover-dp ts-meta" data-tsmeta="motivation">
                                        <h5>Cardio</h5>
                                        
                                    </td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="fitness">
                                        <h5>Fitness</h5>
                                      
                                    </td>
                                </tr>
                                <tr>
                                    <td class="class-time">7.00pm - 9.00pm</td>
                                    <td class="hover-dp ts-meta" data-tsmeta="motivation">
                                        <h5>Cardio</h5>
                                        
                                    </td>
                                    <td class="dark-bg blank-td"><h5>No Class</h5></td>
                                    <td class="hover-dp ts-meta" data-tsmeta="fitness">
                                        <h5>Boxing</h5>
                                       
                                    </td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="workout">
                                        <h5>Yoga</h5>
                                       
                                    </td>
                                    <td class="hover-dp ts-meta" data-tsmeta="motivation">
                                        <h5>Karate</h5>
                                       
                                    </td>
                                    <td class="dark-bg hover-dp ts-meta" data-tsmeta="fitness">
                                        <h5>Boxing</h5>
                                       
                                    </td>
                                    <td class="hover-dp ts-meta" data-tsmeta="workout">
                                        <h5>WEIGHT LOOSE</h5>
                                        
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <section class="contactsuu" id="contact">
        <div id="free">
        <h2 class="contact-heading">Contact Us</h2>
        <p class="contact-info">Feel free to reach out for any inquiries or assistance. We're here to help!</p>

        <div class="contact-form">
        <form action="#" method="post">
    <input type="text" name="name1" class="form-input" placeholder="Your Name" required>
    <input type="email" name="email1" class="form-input" placeholder="Your Email" required>
    <textarea name="message1" class="form-input" placeholder="Your Message" rows="5" required></textarea>
    <button type="submit" class="form-submit">Send Message</button>
</form>

        </div>
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

            <img src="<?php echo (!empty($resultImagename)) ? 'uploads/' . $resultImagename : 'profile.jpg'; ?>" alt="" id="profile">
            <label for="input-file" name="input_file" id="mo" style="color: white;">Update Image</label><br>
            <input type="file" id="input-file" name="input_file" class="me" accept="image/jpeg, image/png, image/jpg">

            
                <label for="name" class="text1">Name:</label>
                <input type="text" id="name" class="n" name="names" value="<?php echo $resultName; ?>" autocomplete="on" required>
            
            <label for="phone" class="text1">Phone:</label>
            <input type="number" class="n" id="phone" name="phones" value="<?php echo $resultPhone; ?>" autocomplete="on" required>

            <label for="dob" class="text1">Date of Birth:</label>
            <input type="date" class="n" id="dob" name="dobs" value="<?php echo $resultDob; ?>" required>

            <label for="occup" class="text1">Occupation:</label>
            <input type="text" class="n" id="occup" name="occups" value="<?php echo $resultOccupation; ?>" autocomplete="on" required>

            <label for="age" class="text1">Age:</label>
            <input type="number" id="age" class="n" name="ages" value="<?php echo $resultAge; ?>" autocomplete="on" required>

            <input type="hidden" id="imageFileName" name="imageFileName">
            <button class="sub" type="submit" name="submit" value="submit" onclick="closeModal()">Update</button>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    
        history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });

  /* $(document).ready(function() {
            // Add click event for each training class link
            $('.ui-tabs-anchor').on('click', function(e) {
                e.preventDefault();

                // Get the target tab ID from the href attribute
                var targetTab = $(this).attr('href');

                // Hide all tabs
                $('.ui-tabs-panel').hide();

                // Show the selected tab
                $(targetTab).show();
            });
        });
*/
    function openUpdateForm() {
        document.getElementById("modalOverlay").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("modalOverlay").style.display = "none";
    }

    let profilePic = document.getElementById("profile");
    let inputFile = document.getElementById("input-file");

    if (inputFile) {
        inputFile.onchange = function () {
            if (profilePic) {
                profilePic.src = URL.createObjectURL(inputFile.files[0]);
                // Set the file name in the hidden input We want to update students form also
            } else {
                console.error("Element with ID 'profile' not found");
            }
        }
    } else {
        console.error("Element with ID 'input-file' not found");
    }
    var inactivityTime = 0;
       var timeout = 15 * 60 * 1000;  // 15 minutes

       function resetTimer() {
           inactivityTime = 0;
       }

       document.addEventListener("mousemove", resetTimer);
       document.addEventListener("keypress", resetTimer);

       function startLogoutTimer() {
           setInterval(checkInactivity, 1000);  // Check every second 
       }

       function checkInactivity() {
           inactivityTime += 1000;

           if (inactivityTime >= timeout) {
               // Redirect to the logout page or perform any other logout actions
               window.location.href = 'sess.php';
           }
       }

       startLogoutTimer();
       window.onload = function () {
        
        console.log("Script is running");
        <?php if (!isset($_SESSION['username'])): ?>
                // If not logged in, show an alert
                alert("LOGIN FIRST!!");
                // Redirect to the login page or any other appropriate action
                window.location.href = "firstmain.php";
                <?php endif; ?>
        // Check if the session variable is set
        <?php if (isset($_SESSION['new_img_name'])): ?>
            // Retrieve the new image name from the session
            let newImageName = "<?php echo $_SESSION['new_img_name']; ?>";
            console.log("Session variable new_img_name:", newImageName);

            // Dynamically update the image source
            let profilePic = document.getElementById("pro");
            if (profilePic) {
                profilePic.src = "uploads/" + newImageName;
                
                profilePic.style.width = "30px"; // Set the desired width
                profilePic.style.height = "30px"; // Set the desired height
            } else {
                console.error("Element with ID 'pro' not found");
            }

            // Unset the session variable after using it
            <?php unset($_SESSION['new_img_name']); ?>
        <?php endif; ?>
    };
    document.addEventListener("DOMContentLoaded", function () {
            var header = document.querySelector("header");

            window.addEventListener("scroll", function () {
                if (window.scrollY > 0) {
                    header.classList.add("sticky-scroll");
                } else {
                    header.classList.remove("sticky-scroll");
                }
            });
        });
     
</script>




   
</body>
</html>