<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
//session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Include PHPMailer files
require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // Retrieve and sanitize user input
   $name1 = isset($_POST["name1"]) ? htmlspecialchars($_POST["name1"]) : "";
$email1 = isset($_POST["email1"]) ? htmlspecialchars($_POST["email1"]) : "";
$message1 = isset($_POST["message1"]) ? htmlspecialchars($_POST["message1"]) : "";


   if (!empty($name1) && !empty($email1) && !empty($message1)) {

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
}
?>
<?php
// Initialize variables to empty values
//$username = $password = "";
$usernameError = $passwordError = "";
$loginError = "";

// Check if there is an error message in the session
//session_start();
if (isset($_SESSION['error'])) {
    $loginError = $_SESSION['error'];
    unset($_SESSION['error']); // Clear the session variable
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user input
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);

    // Perform your validation and authentication logic here
    // Include l3.php to get validation errors
    include('l3.php');

    // Assign validation errors to variables
    $usernameError = isset($usernameError) ? $usernameError : '';
    $passwordError = isset($passwordError) ? $passwordError : '';

    // Check if there are no errors
    if (empty($usernameError) && empty($passwordError)) {
        // Validate credentials against the database
        $isAuthenticated = validateCredentials($username, $password, $conn);

        if (!$isAuthenticated) {
            $loginError = "Invalid username or password";
            scrollToTop(); // JavaScript function to scroll to the top of the page
        } else {
            // Store username in session
            $_SESSION['username'] = $username;

            // Redirect to the next page after successful login
            if (preg_match('/.*[a-z].*/', $username)) {
                header('Location: up1.php');
                exit();
            } else {
                header('Location: test1.php');
                exit();
            }
        }
    } else {
        scrollToTop(); // JavaScript function to scroll to the top of the page
    }
}

// JavaScript function to scroll to the top of the page
function scrollToTop() {
    echo '<script>window.scrollTo({top: 0, behavior: "smooth"});</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Training Studio</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    /* Additional styles for the Contact Us section */
    
.contactsuu {
    background-color: white;
    padding: 50px;
    text-align: center;
}

.contact-heading {
    color: rgb(255, 111, 0);
    font-size: 2em;
    margin-bottom: 20px;
}

.contact-info {
    color: #333;
    font-size: 1.2em;
    margin-bottom: 30px;
}

.contact-form {
    max-width: 600px;
    margin: 0 auto;
}

.form-input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.form-input:focus {
    border-color: rgb(255, 111, 0);
    outline: none;
    box-shadow: 0 0 5px rgba(255, 111, 0, 0.5);
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

/* Add more styling as needed */
</style>
<body>
    <div class="hero">
        <video autoplay loop muted plays-inline class="back">
            <source src="gym.mp4" type="video/mp4">
        </video>
    </div>
    
    <header class="sticky">
        <div class="logo">
            <h1 class="he">Training <span style="color: rgb(255, 111, 0);">Studio</span></h1>
        </div>
        
        <nav id="navbar">
            <ul>
                <li><a href="#hom" class="ho">Home</a></li>
                <li><a href="#about" class="ho">About</a></li>
                <li><a href="#classes"class="ho" onclick="handleClassesClick()">Classes</a></li>
                <li><a href="#schedules"class="ho" onclick="handleSchedulesClick()">Schedules</a></li>
                <li><a href="#contact"class="ho">Contact</a></li>
            </ul>
        </nav>
        <button class="login-button" type="button" onclick="openLoginModal()">Login</button>
    </header>
    
    <section id="hom">
        <h2 id="home">WELCOME TO OUR TRAINING STUDIO</h2>
        <br>
        <h1 id="cap">WORK HARDER, GET STRONGER</h1>
        <button type="button" id="signup" onclick="window.location.href='r.php'">BECOME A MEMBER</button>
    </section>
    <section id="about">
    <div class="abo">
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
                        <p>
                            Strengthen your foundation with our basic muscle course, focusing on fundamental exercises for overall fitness and strength development.</p>
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
         </div>
</section>
    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLoginModal()">&times;</span>
            <form id="loginForm" action="firstmain.php" method="post">
                <h2>Login</h2>
                <br>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="e.g. 12GYM0001" required>
                <span class="error-message" id="usernameError"><?php echo isset($usernameError) ? $usernameError : ''; ?></span>

                <br><br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <span class="error-message" id="passwordError"><?php echo isset($passwordError) ? $passwordError : ''; ?></span>
                <br>
                <span class="error-message" id="loginError"><?php echo $loginError; ?></span>
<br>
                <button type="submit" class="pra">Login</button>
            </form>
            <br>
            <a href="sm2.php" class="fp">Forgot Password?</a><br><br> 
            <a href="foruser.php" class="fp">Forgot Username?</a><br>
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




    </section>
    <!-- Your existing JavaScript code goes here -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('nav a');

            navLinks.forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();

                    const targetSectionId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetSectionId);

                    if (targetSection) {
                        // Use smooth scrolling
                        targetSection.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });

        function openLoginModal() {
            var modal = document.getElementById("loginModal");
            modal.style.display = "block";
        }

        function closeLoginModal() {
            var modal = document.getElementById("loginModal");
            modal.style.display = "none";
        }
        function checkLogin() {
        // Implement your login check logic here
        // For simplicity, let's assume a variable named isLoggedIn is used
        var isLoggedIn = false; // Set this to true if the user is logged in

        return isLoggedIn;
    }

    function handleClassesClick() {
        if (!checkLogin()) {
            alert("Login First");
            // You can redirect the user to the login page or show a login modal
        } else {
            // Implement the logic for handling the "Classes" click
            // For example, navigate to the Classes section
            window.location.href = "#classes";
        }
    }

    function handleSchedulesClick() {
        if (!checkLogin()) {
            alert("Login First");
            // You can redirect the user to the login page or show a login modal
        } else {
            // Implement the logic for handling the "Schedules" click
            // For example, navigate to the Schedules section
            window.location.href = "#schedules";
        }
    }
        window.onclick = function (event) {
            var modal = document.getElementById("loginModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
        
        window.addEventListener("scroll", function(){
            var header = document.querySelector("header");
            header.classList.toggle("sticky", window.scrollY > 0);
        });

        function login() {
            // JavaScript code for handling any client-side actions (if needed)
        }

        document.getElementById("password").addEventListener("keyup", function(event) {
            if (event.key === "Enter") {
                login();
            }
        });
       /*  var inactivityTime = 0;
       var timeout = 1 * 60 * 1000;  // 15 minutes

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

       startLogoutTimer();*/

    </script>
</body>
</html>