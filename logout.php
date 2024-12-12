<?php
session_start();
include 'db_conn2.php'; 

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    $activity_type = "Logged Out";
    $activity_details = "User logged out successfully.";
    
    $log_stmt = $conn->prepare("
        INSERT INTO activity_logs (user_id, activity_type, activity_details, timestamp) 
        VALUES (?, ?, ?, NOW())
    ");
    $log_stmt->bind_param("sss", $user_id, $activity_type, $activity_details);

    if ($log_stmt->execute()) {
        session_unset();
        session_destroy();
        header("Location: login.php"); 
        exit();
    } else {
        echo "Error inserting activity log: " . $log_stmt->error;
    }

    $log_stmt->close();
} else {
    header("Location: login.php");
    exit();
}

?>
