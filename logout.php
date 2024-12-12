<?php
// Start the session
session_start();

// Destroy the session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();  // Ensure no further code is executed after the redirect
?>
