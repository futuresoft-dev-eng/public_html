<?php
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $concern = htmlspecialchars($_POST['concern']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'floodping.official@gmail.com'; 
        $mail->Password   = 'vijk olie xyap yhhs';   
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // eender and recipient details
        $mail->setFrom('floodping.official@gmail.com', 'FloodPing Official'); 
        $mail->addAddress('floodping.official@gmail.com', 'FloodPing'); 

        // email content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission: $concern";
        $mail->Body    = "
          <h3>Contact Form Submission</h3>
          <p><strong>Name:</strong> $name</p>
          <p><strong>Email:</strong> $email</p>
          <p><strong>Concern:</strong> $concern</p>
          <p><strong>Message:</strong><br>$message</p>
        ";
        $mail->AltBody = "Name: $name\nEmail: $email\nConcern: $concern\nMessage: $message";

        $mail->send();
        $success = true;
    } catch (Exception $e) {
        $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($error_message)): ?>
  <div id="errorModal" class="modal" style="display: flex;">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Error</h2>
      <p><?php echo htmlspecialchars($error_message); ?></p>
      <button id="errorOkButton">OK</button>
    </div>
  </div>
  <?php endif;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Floodping</title>
    <link rel="stylesheet" href="landingpage.css" />
    <link rel="icon" href="/floodping/images/Floodpinglogo.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=mark_email_read" />
</head>

    <header class="header">
        <nav class="navbar">
       <img class="FPlogo-image" src="./images/Floodpinglogo.png" alt="logo">  
            <h2 class="logo">Floodping</h2>
            <ul class="links">
                <li><a href="index.php">HOME</a></li>
                <li>
                    <a href="livestream.php">
            <span class="red-circle"></span> LIVESTREAM
            </a>
        </li>
                <li><a href="about.php">ABOUT</a></li>
                <li><a href="contact.php"  class="active">CONTACT</a></li>
            </ul>
            <div class="buttons">
                <a href="login.php" class="signin" onclick="window.location.href='login'">LOG IN</a>
            </div>
        </nav>
    </header>
    
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
    background: #F5F5F5;
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
    padding: 6px 15px;
}

.logo {
    display: inline-block; 
    font-size: 22px !important;
    font-weight: bold;
    margin-right: 0px;
    margin-top: 10px;
}

.FPlogo-image {
    width: 4%;
    height: 80%;
    margin: 0px 0px 0px -55px;
    position: absolute;
}

.navbar .links {
    display: flex;
    align-items: center;
    list-style: none;
    gap: 35px;
    margin: 5px 0px 0px 400px !important;
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
    padding: 7px 37px;
    text-decoration: none;
    border-radius: 10px;
    transition: 0.2s ease;
}

.navbar .buttons a:hover {
    color: black;
    background-color: transparent;
    border: 1px solid black;
}

.active {
    color: #1793EB !important;  
}

.landingpage-section {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    height: 105vh;
    padding: 0 15px;
    max-width: 100%;
    margin: 50px auto;
    background-color: #F5F5F5;
}


.contact-us {
    background-color: #F5F5F5;
    width: 100%;
    height: 1000px;
    margin: -150px 0px 0px 0px;
}

.contact-form {
    padding: 20px 40px;
    border-radius: 10px;
    width: 100%;
    max-width: 600px;
    margin: 250px 0px 0px 860px !important;
    position: absolute;
}

.contact-form h1 {
    font-size: 30px;
    color: #333333;
    text-align: center;
    margin-bottom: 20px;
    text-transform: uppercase;
}

.contact-form input[type="text"],
.contact-form input[type="email"],
.contact-form select,
.contact-form textarea {
    width: 100%;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #02476A;
    border-radius: 5px;
    font-size: 14px;
    color: #333333;
}

.contact-form textarea {
    height: 200px !important;
}

.email-input::placeholder {
    padding-left: 25px; 
    color: gray; 
}

.contact-form .icon {
    position: absolute;
    margin: 20px 0px 0px 10px !important;
    transform: translateY(-50%);
    font-size: 18px;
    color: #02476A;
}

.contact-form input::placeholder,
.contact-form textarea::placeholder {
    color: #aaaaaa;
}

.contact-form textarea {
    resize: none;
    height: 100px;
}

.contact-form button {
    background: #02476A;
    color: white;
    font-size: 16px;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
}

.contact-form button:hover {
    background: transparent;
    color: black;
    border: 1px solid black;
}

.contact-map {
    max-width: 80% !important;
    height: 80% !important;
    margin: -750px 0px 0px 150px !important;
    position: absolute;
}

.contact-map h2 {
    font-size: 30px;
    text-transform: uppercase;
    text-align: center;
    margin-top: 20px !important;
}

/* modal contact form */
.modal {
    position: fixed;
    z-index: 100;
    width: 100% !important;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    overflow: auto;
    display: none;
    justify-content: center;
    align-items: center;
    margin-left: 0px !important;
    position: fixed;
}
  
/* modal content box */
.modal-content {
    background-color: #fff;
    border-radius: 8px !important;
    padding: 20px;
    text-align: center;
    width: 485px !important;
    height: 190px;
    position: relative;
    font-size: 12px;
    text-align: justify;
    border: 2px solid #ccc;
}

.modal-content h2 {
    font-size: 18px;
    text-align: center;
    margin: 20px 0px 0px 90px;
}

.modal-content p {
    font-size: 14px;
    margin: 10px 0px 0px 135px;
}

/* email icon */
.email-photo {
    width: 120px;
    height: auto;
    margin: 0px 0px 0px 0px !important;
    position: absolute;
}
 
/* ok button */
button#okButton {
    background-color: #02476A;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin: 30px 0px 0px 170px;
    width: 170px;
    font-size: 12px;
}

button#okButton:hover {
    background-color: #02476A;
}

.modal-content img {
    display: block;
    margin: 0 auto;
}

/* footer */
.footer {
    background-color: #1D4D7B; 
    color: #fff; 
    padding: 5px 15px;
    font-family: Arial, sans-serif;
    text-align: center;
    margin-top: -50px;

}
.footer-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.footer-logo img {
    width: 7%;
    height: auto;
    border-radius: 50%; 
    margin: -55px 0px 0px -90px;
    position: absolute;
}

.footer-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 55px;
    margin: -40px;
}

.footer-links a {
    color: #fff;
    text-decoration: none;
    font-size: 14px;
}

.footer-links a:hover {
    text-decoration: underline;
}

.footer-social p {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
    margin-top: 20px;
}

.footer-social .social-icon {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 50px; 
    height: 50px;
    margin: 20px 10px;
    border-radius: 50%;
    background-color: #fff; 
    color: #1D4D7B; 
    font-size: 18px;
    text-decoration: none;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.footer-bottom {
    margin: -36px 0px -20px 560px;
    position: absolute;
}
.footer-links .faq {
    margin: 30px 0px 0px 0px;
    position: absolute;
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

    </style>
  <body>

  <section class="contact-us" id="contact">
    <div class="contact-form">
      <h1>Contact Us</h1>
      <form action="contact.php" method="POST">
      <input type="text" name="name" required placeholder="Name">
      <input type="email" name="email" required placeholder="Email">
      <select id="concern" name="concern" required>
      <option value="" disabled selected>Type of concern</option>
      <option value="Feedback/Suggestions">Feedback/Suggestions</option>
      <option value="Flood Alert Issues">Flood Alert Issues</option>
      <option value="Account Deactivation">Account Deactivation</option>
      <option value="Emergency Contact Update">Emergency Contact Update</option>
      <option value="System Downtime/Outage">System Downtime/Outage</option>
      <option value="Livestream Issues">Livestream Issues</option>
      <option value="Other">Other</option>
    </select>
    <textarea name="message" required placeholder="Write your message here."></textarea>
    <button type="submit">Submit</button>
  </form>
</div>
</section>


  <!-- map -->
  <div class="contact-map">
    <h2>Our Location</h2>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.283189649812!2d121.0254427967896!3d14.696570800000009!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b12882130001%3A0x343d08778f211842!2sBarangay%20Bagbag!5e0!3m2!1sen!2sph!4v1731839827144!5m2!1sen!2sph" width="680" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
</section>

<!-- modal -->
<?php if ($success): ?>
<div id="successModal" class="modal" style="display: flex;">
    <div class="modal-content">
    <img class="email-photo" src="images/email.png">
        <h2>Email sent!</h2>
        <p>Your message has been sent successfully!</p>
        <button id="okButton">OK</button>
    </div>
</div>
<?php endif; ?>

<!-- footer -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-logo">
            <img src="./images/FloodPingLogo.png" alt="FloodPing Logo">
        </div>
        <div class="footer-links">
            <ul>
                <li><a href="privacy-policy.php">Privacy Policy</a></li>
                <li><a href="terms-conditions.php">Terms & Conditions</a></li>
                <li class="faq"><a href="FAQ.php">FAQ</a></li>
            </ul>
        </div>
        <div class="footer-social">
            <p>For news and updates, follow us on:</p>
            <a href="https://www.facebook.com" target="_blank" class="social-icon">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://www.instagram.com" target="_blank" class="social-icon">
                <i class="fab fa-instagram"></i>
            </a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2024 Quezon City University</p>
    </div>
</footer>


<script>
document.addEventListener("DOMContentLoaded", function () {
    // Handle success modal
    const successModal = document.getElementById("successModal");
    if (successModal) {
        const okButton = document.getElementById("okButton");
        if (okButton) {
            okButton.addEventListener("click", () => {
                successModal.style.display = "none"; // Close the success modal
            });
        }
    }

    // Handle error modal
    const errorModal = document.getElementById("errorModal");
    if (errorModal) {
        const closeIcon = errorModal.querySelector(".close");
        if (closeIcon) {
            closeIcon.addEventListener("click", () => {
                errorModal.style.display = "none"; // Close the error modal
            });
        }

        const errorOkButton = document.getElementById("errorOkButton");
        if (errorOkButton) {
            errorOkButton.addEventListener("click", () => {
                errorModal.style.display = "none"; // Close the error modal
            });
        }
    }
});
</script>


</body>
</html>