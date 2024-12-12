<?php
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Local Authority'])) {
    header("Location: login.php?error=You%20must%20log%20in%20first");
    exit();  
}
?>
