<?php
session_start();
include 'db_conn2.php';

$error = "";
if (isset($_GET['error'])) {
    $error = urldecode($_GET['error']); 
    echo '<p style="color: red; font-weight: bold;">' . $error . '</p>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, password, role, account_status, failed_attempts, last_attempt_date, last_attempt_time FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            $current_date = date('Y-m-d'); 
            $current_time = date('H:i:s'); 

            if ($user['account_status'] == 'Locked') {
                error_log("Account is locked, redirecting to request_unlock.php");
                header("Location: request_unlock.php?email=" . urlencode($email));
                exit();
            } else if ($user['account_status'] == 'Inactive') {
                $error = "Your account is inactive. Please contact the admin for further assistance.";
            } else {
                if (password_verify($password, $user['password'])) {
                    // Reset failed login attempts
                    $resetStmt = $conn->prepare("UPDATE users SET failed_attempts = 0, last_attempt_date = NULL, last_attempt_time = NULL WHERE email = ?");
                    $resetStmt->bind_param("s", $email);
                    $resetStmt->execute();
                    $resetStmt->close();

                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['role'] = $user['role'];

                    // Log the login activity
                    $user_id = $_SESSION['user_id'];  
                    $activity_type = "Logged In";  // Activity type
                    $activity_details = "User logged in successfully.";  // Activity details

                    $logSql = "INSERT INTO activity_logs (user_id, activity_type, activity_details, timestamp) 
                               VALUES ('$user_id', '$activity_type', '$activity_details', NOW())";
                    $conn->query($logSql);  // Log the activity silently (no echo)

                    // Redirect to the appropriate dashboard based on user role
                    if ($user['role'] === 'Admin') {
                        header("Location: admin_dashboard.php");
                    } else if ($user['role'] === 'Local Authority') {
                        header("Location: authority_dashboard.php");
                    }
                    exit();
                } else {
                    $failedAttempts = $user['failed_attempts'] + 1;

                    if ($failedAttempts >= 4) {
                        $updateStmt = $conn->prepare("UPDATE users SET failed_attempts = ?, account_status = 'Locked', last_attempt_date = ?, last_attempt_time = ? WHERE email = ?");
                        $updateStmt->bind_param("isss", $failedAttempts, $current_date, $current_time, $email);
                        $error = "Too many failed attempts. Your account is now locked. <a href='unlock_account.php?email=$email'>Click here</a> to unlock your profile.";
                    } else {
                        $updateStmt = $conn->prepare("UPDATE users SET failed_attempts = ?, last_attempt_date = ?, last_attempt_time = ? WHERE email = ?");
                        $updateStmt->bind_param("isss", $failedAttempts, $current_date, $current_time, $email);
                        $error = "Incorrect credentials. " . (4 - $failedAttempts) . " attempts left before account lock. Please check your info.";
                    }

                    $updateStmt->execute();
                    $updateStmt->close();
                }
            }
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    } else {
        $error = "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./images/Floodpinglogo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Login</title>
</head>

<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Poppins;
}

body {
    height: 100vh;
    width: 100%;
    background-image: url('images/bg.jpg');
    background-size: cover; 
    background-repeat: no-repeat; 
    background-position: center; 
    overflow-x: hidden;
}

.header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color:#F5F5F5;
    border: 2px solid #ccc;
    box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 100;
}

.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1200px;
    height: auto;
    margin: 0 auto;
    padding: 13px 15px;
}

.logo {
    display: inline-block; 
    font-size: 22px !important;
    font-weight: bold;
    margin-right: 0px;
    margin-top: 0px;
}

.FPlogo-image {
    width: 3.5%;
    height: 83%;
    margin: 0px 0px 0px -59px;
    position: absolute;
}

.navbar .links {
    display: flex;
    align-items: center;
    list-style: none;
    gap: 35px;
    margin: 5px 0px 0px 440px !important;
    position: absolute;
}

.navbar .links a {
    font-weight: 400;
    text-decoration: none;
    color: black;
    padding: 10px 0;
    transition: 0.2s ease;
}

.navbar .links a:hover {
    color: #47b2e4;
}

.navbar .buttons a {
    background-color: #02476A; 
    color: #fff; 
    border: 1px solid black;
    cursor: pointer;
    font-size: 14px;
    padding: 6.5px 37px;
    text-decoration: none;
    border-radius: 10px;
    transition: 0.2s ease;
    margin: -16px 0px 0px -80px; 
    position: absolute;
}

.navbar .buttons a:hover {
    color: black;
    background-color: transparent;
    border: 1px solid black;
}

.login-container {
    background: rgba(245, 251, 255, 0.75); 
    border-radius: 20px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); 
    backdrop-filter: blur(2px); 
    -webkit-backdrop-filter: blur(10px); 
    border: 1px solid rgba(255, 255, 255, 0.2);
    width: 500px;
    height: 500px;
    border-radius: 24px;
    margin: 150px 0px 0px 850px;
}

.login-container h3 {
    color: #FFFFFF;
    font-size: 50px;
    font-weight: 500;
    margin: 100px 0px 0px -690px;
    position: absolute;
}

.login-container #welcome-text {
    color: #FFFFFF;
    font-size: 16px;
    width: 120%;
    text-align: justify;
    margin: 35px 0px 0px -690px;
    position: absolute;
}

.login-container #glad-text {
    color: #02476A;
    font-size: 16px;
    width: 120%;
    text-align: justify;
    margin: 150px 0px 0px 35px;
    font-weight: 500;
}

.login-container input[type="email"] {
    width: 90%;
    padding: 15px;
    margin-top: 20px;
    margin-bottom: -50px;
    margin-left: 23px;
    border: 1px solid #1D4D7B;
    border-radius: 5px;
    font-size: 14px;
    color: #333333;
    padding-left: 45px;
}

.login-container input[type="password"] {
    width: 90%;
    padding: 15px;
    margin: 50px 100px 0px 23px !important;
    border: 1px solid #1D4D7B;
    border-radius: 5px;
    font-size: 14px;
    color: #333333;
    padding-left: 45px;
    
}

.login-container .icon-wrapper {
    position: absolute;
    top: 50.5%;
    left: 30px;
    transform: translateY(-50%);
    width: 35px;
    height: 35px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-container .icon-wrapper i {
    color: white;
    font-size: 1.2rem;
    margin: 110px 0px 0px 0px !important;
    text-shadow: 1px 0 2px #1D4D7B,
                 1px 0 2px #1D4D7B,
                 1px 0 3px #1D4D7B;
}

button {
    width: 90%;
    padding: 10px;
    background-color: #02476A;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    margin: 70px 15px 0px 23px;
    border: 1px solid #02476A;
}

button:hover {
    background-color: #4597C0;
}

.fa-user {
    margin: 0px;
    position: absolute;
    color: white;
    font-size: 1.2rem;
    margin: 35px 0px 0px 40px;
    text-shadow: 1px 0 2px #1D4D7B, 
                 1px 0 2px #1D4D7B,
                 1px 0 3px #1D4D7B;
}

#current-time {
    font-size: 40px;
    font-weight: 500;
    color: #02476A; 
    text-align: center;
    margin: 50px 0px 0px 60px; 
    position: absolute;
}

#current-date {
    font-size: 18px;
    font-weight: 550;
    color: #02476A; 
    margin: 60px 0px 0px 275px;
    position: absolute;
}

#current-day {
    font-size: 18px;
    font-weight: 550;
    text-transform: uppercase;
    color: #02476A; 
    margin: 90px 0px 0px 325px; 
    position: absolute;
}

.footer {
    background-color: #1D4D7B; 
    color: white !important; 
    width: 100%;
    height: 7%;
    padding: 5px 55px;
    font-family: Arial, sans-serif;
    text-align: center;
    margin: 190px 0px 0px 0px;
    position: absolute;
}

.footer-bottom {
    margin: 10px 0px 0px 650px !important;
    position: absolute;
    color: white !important;
}

/* Modal */
.modal {
    display: none; 
    position: fixed; 
    z-index: 1; 
    left: 200px;
    top: 200px;
    width: 100%; 
    height: 100%; 
    background-color: none;
    padding-top: 60px;
  }

  .modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%; 
    height: auto;
    max-width: 500px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  }

  .close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    top: 150px;
    right: 530px;
    cursor: pointer;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }

  .modal h2 {
    color: #e74c3c;
    text-align: center;
  }

  p {
    font-size: 16px;
    color: #555;
    text-align: center;
  }

  .forgot-password {
    text-align: center;
    margin-top: 10px;
    margin-left: 160px;
    position: absolute;
  }

  .forgot-password a {
    color: #02476A;
    text-decoration: none;
    font-size: 14px;
  }

  .forgot-password a:hover {
    text-decoration: underline;
  }
  
  .red-circle {
    display: inline-block;
    width: 8px; 
    height: 8px; 
    background-color: red;
    border-radius: 50%;
    margin-right: 8px; 
    vertical-align: middle; 
}


/* Responsive breakpoints */
@media screen and (max-width: 768px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .FPlogo-image {
        width: 40px;
    }

    .navbar .links {
        margin-top: 10px;
    }

    .login-container {
        width: 95%;
    }
}

@media screen and (max-width: 480px) {
    .navbar .links {
        flex-direction: column;
        gap: 10px;
    }

    .login-container h3 {
        font-size: 1.5rem;
    }

    #current-time {
        font-size: 1.2rem;
    }
}
</style>

<header class="header">
        <nav class="navbar">
        <img class="FPlogo-image" src="./images/Floodpinglogo.png" alt="Description of the image">  
            <h2 class="logo">Floodping</h2>
            <ul class="links">
                <li><a href="index.php">HOME</a></li>
               <li>
                    <a href="livestream.php">
            <span class="red-circle"></span> LIVESTREAM
            </a>
        </li>
                <li><a href="about.php">ABOUT</a></li>
                <li><a href="contact.php">CONTACT</a></li>
            </ul>
            <div class="buttons">
                <a href="login.php" class="signin" onclick="window.location.href='login'">LOG IN</a>
            </div>
        </nav>
    </header>

<body>
<div class="login-container">
    <h3>Welcome to FloodPing!</h3>
    <p id="current-time"></p>
    <p id="current-date"></p>
    <p id="current-day"></p>
    <p id="glad-text">Glad to see you again! Login to your account below</p>
    <p id="welcome-text">Stay informed and stay safe as you monitor real-time water levels in your community. With FloodPing, you’re always one step ahead—receive timely alerts, check current flood status, and access essential updates to help protect you and those around you. 
        <br>
        <br>
        Let’s keep watch together. Your safety is our priority!</p>
    <form action="" method="POST">
    <div class="input-container">
        <i class="fas fa-user"></i> <!-- User Icon -->
        <input type="email" name="email" required placeholder="User ID">
    </div>
        
    <div class="icon-wrapper">
        <i class="fas fa-lock"></i> <!-- Lock icon -->
    </div>
    
        <input type="password" name="password" required placeholder="Password"><br>

          <!-- Forgot Password Link -->
    <div class="forgot-password">
        <a href="forgot_password.php">Forgot your password?</a>
    </div>
        <button type="submit">Log in</button>
</div>
    </form>

    <!-- Modal HTML -->
<div class="modal" id="errorModal">
  <div class="modal-content">
    <span class="close" id="closeModal">&times;</span>
    <h2>Login Error</h2>
    <p>
    <?php if ($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    </p>
  </div>
</div>

    
  <!-- footer -->
<footer class="footer">
    <div class="footer-bottom">
        <p style="color: white !important;">© 2024 Quezon City University</p>
    </div>
</footer>

<!-- js for dynamic time -->
<script>
    function updateTime() {
        const timeElement = document.getElementById('current-time');
        const now = new Date();
        const hours = now.getHours() % 12 || 12;
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
        timeElement.textContent = `${hours}:${minutes} ${ampm}`;
    }
    setInterval(updateTime, 1000);
    updateTime(); 

    // Function to update the current time, date, and day
    function updateDateTime() {
        const now = new Date();

        // Get the current time in 12-hour format
        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        const currentTime = `${hours}:${minutes} ${ampm}`;

        // Get the current date in 'Month Day, Year' format
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const currentDate = now.toLocaleDateString('en-US', options);

        // Get the current day in 'long' format
        const currentDay = now.toLocaleDateString('en-US', { weekday: 'long' });

        // Display the current time and date (with only one day)
        document.getElementById('current-time').textContent = `${currentTime}`;
        document.getElementById('current-date').textContent = `${currentDate}`;
        document.getElementById('current-day').textContent = `${currentDay}`;
    }

    // Update the date and time immediately and every minute
    updateDateTime();
    setInterval(updateDateTime, 60000);
</script>
<script>
  // Show the modal if there's an error
  window.onload = function() {
    var errorMessage = '<?php echo $error; ?>';
    if (errorMessage) {
      document.getElementById('errorModal').style.display = 'block';
    }
  }

  // Close the modal when the user clicks on the "x" button
  document.getElementById('closeModal').onclick = function() {
    document.getElementById('errorModal').style.display = 'none';
  }

  // Close the modal if the user clicks outside of the modal
  window.onclick = function(event) {
    var modal = document.getElementById('errorModal');
    if (event.target == modal) {
      modal.style.display = 'none';
    }
  }
</script>
</body>
</html>
