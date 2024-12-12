<?php
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
    <title>Floodping | ABOUT US</title>
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
                <li><a href="about.php" class="active">ABOUT</a></li>
                <li><a href="contact.php">CONTACT</a></li>
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
    height: 83%;
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

.emergency-section {
    max-width: 100% !important;
    margin: 100px 0px 0px 10px;
    background: #F5F5F5;
    height: 105vh;
    padding: 0 15px;
    max-width: 100%;
}

.emergency-section #spacingTop{
    margin: 0px 0px 0px 550px;
}

#content-about{
    max-width: 100%;
    color: white;
    margin: -200px 0px 0px 0px!important;
}

.card-container {
    display: flex; 
}

.container .card {
    width: 350px; 
    height: 500px;
    margin: 50px 50px 50px 60px; 
    padding: 40px 30px;
    border-radius: 30px;
    background: linear-gradient(196.88deg, #02476A 4.92%, #4597C0 100%);
    border-radius: 40px;
    box-shadow: -6px -6px 20px rgba(255,255,255,1), 6px 6px 20px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column; 
    justify-content: center;
    align-items: center;
}

.container .card:hover
{
    box-shadow: inset -6px -6px 20px rgba(255,255,255,0.5), inset 6px 6px 20px rgba(0,0,0,0.05);
}
.container .card .imgBx
{
    position: relative;
    text-align: center;
}
.container .card .imgBx img
{
    max-width: 120px;
}
.container .card .contentBx
{
    position: relative;
    margin-top: 20px;
    text-align: center;
}
.container .card .contentBx h2
{
    color: white;
    font-weight: 700;
    font-size: 1.1em;
    letter-spacing: 2px;

}
.container .card .contentBx p
{
    color: white;
}
.container .card .contentBx a
{
    display: inline-block;
    padding: 10px 20px;
    margin-top: 15px;
    border-radius: 40px;
    color: #32a3b1;
    font-size: 16px;
    text-decoration: none;
    box-shadow: -4px -4px 15px rgba(255,255,255,1), 4px 4px 15px rgba(0,0,0,0.1);
}

h2 i {
    width: 50px;
    height: 50px;
    top: 1132px;
    left: 750px;
    padding: 6.25px;
    gap: 0px;
    opacity: 0px;
    color: hotpink;
}

/* abt section */
.abt-section {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    height: 95vh;
    padding: 0 15px;
    max-width: 1200px;
    z-index: 10;
    overflow-x: hidden;
}

#content-about {
    margin: 400px;
    position: absolute;
}

.abt-section .about {
    width: 100%;
    color: black;
    z-index: 10;
}

.about h2 {
    font-size: 30px;
    margin: 100px 0px 20px 300px;
    z-index: 10;
}

.about p {
    font-size: 1.0rem;
    margin-bottom: 20px;
    color: black;
    text-align: justify;
    margin-left: 300px !important;
    width: 83%;
}

.abt-section .img image {
    width: 517px;
}

.abt-section .buttons {
    margin-top: 40px;
    margin-left: 50px !important;
}

.abt-section .buttons a {
    text-decoration: none;
    color: black;
    padding: 12px 24px;
    border-radius: 0.375rem;
    font-weight: 600;
    transition: 0.2s ease;
    display: inline-block;
}

.abt-section .buttons a:not(:last-child) {
    margin-right: 15px;
}


#content-about{
    max-width: 50%;
    color: black;
    margin-left: 250px !important;
}

/* floodawareness section */
.floodawareness-section {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    height: 45vh;
    padding: 0px 0px !important;
    max-width: 100% !important;
    background: #F5F5F5;
    margin: 1000px 0px 0px -500px;
    position: absolute;
    z-index: 10;
}

#floodawareness-title {
    width: 100%;
    font-size: 30px;
    margin: -550px 0px 0px 0px !important;
    position: absolute !important;
}

.floodawareness-section p {
    font-size: 1.0rem;
    color: black;
    text-align: justify;
    width: 80% !important; 
    margin: -45px 0px 0px -460px !important;
}

.floodawareness-section .img image {
    width: 17px;
}

#floodawareness-about{
    width: 50%;
    color: black;
    margin-right: 30px !important;
}

.floodawareness-image {
    border-radius: 15px;
    width: 84%;
    margin: -320px 0px 0px 530px;
    position: absolute;
}

.legend-container h2 {
    font-size: 26px;
    margin: 10px 0px 15px -80px;
}

#icon {
    font-size: 17px; 
    margin: 0px 0px 0px 0px;
}

.icon-red {
    color: red;
    font-size: 28px; 
    margin: 0px 0px 0px -20px;
}

.icon-blue {
    color: blue;
    font-size: 28px;
}

#loc-icon {
    font-size: 28px;
    color: red;
}

/* partnership section styles */
.partnership-section {
    margin: 2010px 0px 0px 0px;
    background-color: #F5F5F5;
    width: 100%;
    position: absolute;
}

.brgylogo-image {
    width: 20% !important;
    height: 17%;
    margin: 0px -30px 0px 540px;
}

.qculogo-image {
    width: 17% !important;
    height: 17%;
    margin: 0px 0px 0px 80px;
}

.logo h2 {
    font-size: 25px;
    font-weight: bold;
    margin: 0px 0px 30px 650px;
}

#spacingTop {
    font-weight: bold;
    margin: 100px 100px 0px 650px;
}

#about {
    background-color: #F5F5F5;
    max-width: 100% !important;
    max-height: 1000px !important;
    margin-top: 0px !important;
}

.imagee {
    margin: -315px 0px 0px -200px;
    border-radius: 15px;
    max-width: 41%;
    z-index: 10;
    position: absolute;
}

#map {
    width: 100%;
    height: 300%;
    background-color: #F5F5F5 !important;
    margin: 1650px 0px 0px -0px !important;
    position: absolute;
}

.map-image {
    width: 60%;
    margin: 1570px 0px 100px 130px !important;
    border: 1px solid #000;
    border-radius: 3%;
    position: absolute;
}

.legend-container {
    width: 300px;              
    padding: 20px;             
    background-color: #FFFF; 
    border: 1px solid #000;    
    border-radius: 8px;       
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
    margin: 20px auto;         
    text-align: center;  
    margin: 1750px 0px 0px 1100px;   
    position: absolute; 
    z-index: 10;;
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
    padding: 10px 15px;
    font-family: Arial, sans-serif;
    text-align: center;
    margin-top: 1650px;
    margin-left: 0px;
    position: absolute;

}
.footer-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    min-width: 1490px;
    height: 120px !important;
    margin: 0px auto;
    padding: 0 20px;
}

.footer-logo img {
    width: 7%;
    height: auto;
    border-radius: 50%; 
    margin: -55px 0px 0px 140px;
    position: absolute;
}

.footer-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 55px;
    margin: -40px;
    margin-left: -280px;
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
    margin-top: -45px !important;
    margin-left: -450px;
    position: absolute;
}

.footer-social .social-icon {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 50px; 
    height: 50px;
    margin: 0px 10px 0px -300px;
    position: absolute;
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
    <!-- emergency section -->
    <section class="emergency-section" id="emergency">
        <h2 id='spacingTop'><i class="fas fa-phone-alt"></i>EMERGENCY HOTLINES</h2>
        <div class="container">
            <div class="card-container">
                <div class="card">
                    <div class="imgBx">
                        <img src="https://image.flaticon.com/icons/svg/2092/2092063.svg" alt="">
                    </div>
                    <div class="contentBx">
                      <p>QC EMERGENCY HOTLINE</p>
                      <h2>112</h2>

                      <p>EMERGENCY OPERATIONS CENTER:</p>
                      <h2>0977 031 2892 (GLOBE)</h2>
                      <h2>0947 885 9929 (SMART)</h2>
                      <h2>8988 4242 loc. 7245</h2>

                      <p>EMERGENCY MEDICAL SERVICES / SEARCH AND RESCUE:</p>
                      <h2>0947 884 7498 (SMART)</h2>
                      <h2>8928 4396</h2>
                    </div>
                </div>
                <div class="card">
                    <div class="imgBx">
                        <img src="https://image.flaticon.com/icons/svg/1197/1197460.svg" alt="">
                    </div>
                    <div class="contentBx">
                        <h2></h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                    </div>
                </div>
                <div class="card">
                    <div class="imgBx">
                        <img src="https://image.flaticon.com/icons/svg/1067/1067256.svg" alt="">
                    </div>
                    <div class="contentBx">
                        <h2></h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                    </div>
                </div>
            </div>
        </div>   
    </section>

    
    <!-- about section -->
  <section class="abt-section" id="about">
    <div class = "photo">
      <img class="imagee" src="./images/baha.jpg">  
    </div>
    
    <div class="about" id="content-about" style="top: 980px;">
      <h2 id='about-title'>ABOUT FLOODPING</h2>
      <p>
        Welcome to FloodPing – your dependable partner in flood preparedness and safety. FloodPing combines cutting-edge sensor technology and real-time water level monitoring to keep communities informed and prepared. When rising water levels reach critical points, our system automatically sends instant alerts to authorized users, empowering local authorities to act fast and minimize risks.
        <br>
        <br>But that’s not all – with FloodPing, local authorities can seamlessly broadcast SMS alerts to residents, reaching them right where they are, no matter the time or place. This ensures everyone stays updated and has the information they need to stay safe. </br>
        <br>
        FloodPing is more than just a tool; it's a commitment to proactive, life-saving action. Join us in building a safer, more resilient community where information flows as swiftly as the alerts we send. Prepare, protect, and trust in FloodPing – where flood management is designed with your safety in mind.
        <br>
        </p>
      </div>
      <br>

<!-- flood awareness section -->
    <div class="floodawareness-section" id="floodawareness-about">
      <div class = "photo">
      <img class="floodawareness-image" src="./images/floodawareness.png" alt="Flood Awareness Image">
      </div>

      <h2 id='floodawareness-title'>FLOOD AWARENESS</h2>
      <br>
      <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
      <br>
      <br>
        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.
      </br>
      <br>
        FloodPing is more than just a tool; it's a commitment to proactive, life-saving action. Join us in building a safer, more resilient community where information flows as swiftly as the alerts we send. Prepare, protect, and trust in FloodPing – where flood management is designed with your safety in mind.
      </p>
    </div>
  </div>
<br>

<!-- map section -->
    <div class="map-section" id="map"> 
        <div class = "photo">
          <img class="map-image" src="images/map.jpg" alt="photo">   
    </div> 

    <div class="legend-container">
      <h2>Legends</h2>
      <h2 id='icon'><i class="fas fa-circle icon-red"></i>Fire Prone Area</h2>
      <h2 id='icon'><i class="fas fa-circle icon-blue"></i>Flood Prone Area</h2>
      <h2 id='icon'><i class="fas fa-map-marker-alt" id="loc-icon"></i>Evacuation Area</h2>
  </div>
</div>

<!-- partnership section -->
  <div class="partnership-section" id="partnership">
    <div class = "logo">
      <h2>LINKAGES AND PARTNERSHIP WITH:</h2>
      <img class="brgylogo-image" src="images/brgy-logo.png" alt="Description of the image">   
      <img class="qculogo-image" src="images/QCU-Logo.png" alt="Description of the image">
    </div>
  </div>
  <br>
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
                <li><a href="terms">Terms & Conditions</a></li>
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


</body>
</html>