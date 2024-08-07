<?php

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
    $stmt->bind_result($resultUsername);
    $stmt->fetch();
    //echo "Name: " . $resultUsername;
    $stmt->close();
} else {
    echo "Error preparing statement";
}


// Check if the form is submitted and a file is uploaded
if (isset($_POST['submit']) && isset($_FILES['input_file'])) {
    $img_name = $_FILES['input_file']['name'];
    $img_size = $_FILES['input_file']['size'];
    $tmp_name = $_FILES['input_file']['tmp_name'];
    $error = $_FILES['input_file']['error'];

    if ($error === 0) {
        if ($img_size > 125000) {
            $em = "File size is too large.";
            header("Location: .php?error=$em");
            exit();
        }

        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);
        $allowed_exs = array("jpg", "jpeg", "png");

        if (in_array($img_ex_lc, $allowed_exs)) {
            $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;

            // Check if the username exists in the database
            $checkUserExists = "SELECT username FROM users WHERE username = ?";
            $stmtCheckUser = $conn->prepare($checkUserExists);

            if ($stmtCheckUser) {
                $stmtCheckUser->bind_param("s", $sessionUsername);
                $stmtCheckUser->execute();
                $stmtCheckUser->store_result();

                if ($stmtCheckUser->num_rows > 0) {
                    // Username exists, update the image
                    $updateImageQuery = "UPDATE users SET image_url = ? WHERE username = ?";
                    $stmtUpdateImage = $conn->prepare($updateImageQuery);

                    if ($stmtUpdateImage) {
                        $stmtUpdateImage->bind_param("ss", $new_img_name, $sessionUsername);
                        $stmtUpdateImage->execute();
                        $stmtUpdateImage->close();

                        // Set session variable for success message
                        $_SESSION['new_img_name'] = $new_img_name;
                        header("Location: up2.php");
                        exit();
                    } else {
                        echo "Error preparing statement for image update";
                    }
                } else {
                    // Username doesn't exist, display an error message
                    $em = "Username does not exist.";
                    header("Location: up2.php?error=$em");
                    exit();
                }

                $stmtCheckUser->close();
            } else {
                echo "Error preparing statement for username check";
            }
        } else {
            $em = "You can't upload files of this type.";
            header("Location: up2.php?error=$em");
            exit();
        }
    } else {
        $em = "Unknown error occurred!";
        header("Location: up2.php?error=$em");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Training Studio</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-group {
    margin: 0;
    padding:0;
}
        body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background: url("innerback.jpeg") no-repeat center; /* Replace with your background image */
    background-size: cover;
    background-position: left;
    height: 100vh;
}

.hero{
    width: 100%;
    height: 100vh;
    background-image: rgba(0,0,0,0.05);
    position: relative;
    padding: 0 5%;
    align-items: center;
    justify-content: center;
    display: flex;
    position: relative;
    z-index: 1;
}
header {
    position: fixed;
    top: 0;
    background-color: rgba(0, 0, 0, 0.5); /* Use rgba with an alpha value for transparency */
    width: 100%;
    height: 75px;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 2;
    transition: background-color 0.3s; /* Add a transition for a smooth color change */
}

header.sticky {
    padding: 5px 100px;
    background: rgba(0,0,0,0); /* Add a background color for sticky state */
}

header ul{
   text-align: center;
    
}

header.sticky ul li {
  list-style: none; 
  display: inline-block;
}
header.sticky ul li a{
 text-decoration: none;
 text-transform: capitalize;
  display: block;
  font-size: 20px;
}

/* Add this to your existing CSS */
/* Add this to your existing CSS */


.logo{
    font-size: 18px;
    font-weight: bold;
    letter-spacing: 0px;
    cursor: pointer;

}
h1 {
    margin: 0;
}
nav li{
    display: inline-block;
    list-style: none;
    font-size: 20px;
}
nav {
    display: flex;
}

nav a {
    
    text-decoration: none;
    padding: 10px;
    margin: 0 10px;
    transition: color 0.3s;
}
#home{
    position: absolute;
    top: 30%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    
    z-index: 1;
    font-size: 200%;
}


.ho{
    
    transition: color 0.3s;
}
.ho:hover{
    color: rgb(255, 111, 0);
}


.back{
    object-fit: cover; /* or use 'contain' based on your preference */
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 1;
}
.backimg {
            object-fit: cover;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
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
            color: rgb(255, 111, 0);
        }
        .user {
                margin-right: -30px;
            }

        @media screen and (max-width: 768px) {
            header.sticky {
                padding: 5px 10px;
            }

            nav a {
                padding: 5px;
                margin: 0px 5px;
            }
.form label{
    color: #fff;
}

            
        }

    </style>
</head>
<body>

    
    <header class="sticky">
        <div class="logo">
            <h1 class="he">Training <span style="color: rgb(255, 111, 0);">Studio</span></h1>
        </div>
        <div>     <nav id="navbar">
            <ul>
                <li><a href="#hom" class="ho">Home</a></li>
                <li><a href="#about" class="ho">About</a></li>
                <li><a href="#classes"class="ho">Classes</a></li>
                <li><a href="#schedules"class="ho">Schedules</a></li>
                <li><a href="#contact"class="ho">Contact</a></li>
                <li><img src="profile.jpg" alt="" class="prof" id="pro"></li>
                <li class="user"><a href="#" onclick="openUpdateForm()" class="user"><?php echo $resultUsername; ?></a></li>
                       </ul>
        </nav>
    </div>
   </header>
   <div class="hero">
    <img src="innerback.jpeg" alt="" class="backimg">
</div>
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-content">
        <?php
        if(isset($_GET['error'])): ?>
        <p> <?php echo $_GET['error'] ?></p>
        <?php endif ?>
        <!-- Update form content -->
        <form class="form" method="post" action="" enctype="multipart/form-data" autocomplete="off">
            <h3 class="text">Update Your Details</h3>

            <img src="profile.jpg" id="profile">
            <label for="input-file" name="input_file" id="mo" style="color: white;">Update Image</label><br>
            <input type="file" id="input-file" name="input_file" class="me" accept="image/jpeg, image/png, image/jpg" required>

            
                <label for="name" class="text1">UserName:</label>
                <input type="text" id="name" class="n" name="names" autocomplete="on" required>
            
            <label for="phone" class="text1">Phone:</label>
            <input type="number" class="n" id="phone" name="phones" autocomplete="on" required>

            <label for="dob" class="text1">Date of Birth:</label>
            <input type="date" class="n" id="dob" name="dobs" autocomplete="on" required>

            <label for="occup" class="text1">Occupation:</label>
            <input type="text" class="n" id="occup" name="occups" autocomplete="on" required>

            <label for="age" class="text1">Age:</label>
            <input type="number" id="age" class="n" name="ages" autocomplete="on" required>

            <label for="height" class="text1">Height:</label>
            <input type="number" id="height" class="n" name="heights" autocomplete="on" required>

            <label for="weight" class="text1">Weight:</label>
            <input type="number" class="n" id="weight" name="weights" autocomplete="on" required>
           
            <input type="hidden" id="imageFileName" name="imageFileName">
            <button class="sub" type="submit" name="submit" value="submit" onclick="closeModal()">Update</button>
        </form>
    </div>
</div>

<script>
  window.onload = function(){
        // Start the session to access session variables
        console.log("Script is running");

        // Check if the session variable is set
        <?php if (isset($_SESSION['new_img_name'])): ?>
            // Retrieve the new image name from the session
            console.log("Session variable new_img_name:", "<?php echo $_SESSION['new_img_name']; ?>");

            // Dynamically update the image source
            let ProfilePic = document.getElementById("pro");
            ProfilePic.src = "uploads/<?php echo $_SESSION['new_img_name']; ?>";
            ProfilePic.style.width = "30px"; // Set the desired width
            ProfilePic.style.height = "30px"; // Set the desired height

            // Unset the session variable after using it
           
        <?php endif; ?>
        };
    function openUpdateForm() {
        document.getElementById("modalOverlay").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("modalOverlay").style.display = "none";
    }
    let profilePic = document.getElementById("profile");
    let inputFile = document.getElementById("input-file");

    inputFile.onchange = function () {
        profilePic.src = URL.createObjectURL(inputFile.files[0]);
        // Set the file name in the hidden input
    }
 
   

</script>


</div> 
   
</body>
</html>