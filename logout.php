<?php
session_start();
// Destroy session 
session_unset();
session_destroy();
header("Location: login.php");
exit(); 
?>
