<?php
session_start(); // Start the session to access $_SESSION variables
include_once('db_conn2.php');

// Check if the user is logged in and if the session variable is set
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$adminId = $_SESSION['user_id'];

// Check if the admin exists in the 'users' table
$checkAdminQuery = "SELECT * FROM users WHERE user_id = '$adminId'";
$result = mysqli_query($conn, $checkAdminQuery);

if (mysqli_num_rows($result) == 0) {
    die("Error: Admin user does not exist.");
}

// Continue with the rest of the code for deleting residents
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_residents'])) {
    $selectedResidents = json_decode($_POST['selected_residents'], true);

    if (!empty($selectedResidents)) {
        // Escape the resident IDs to prevent SQL injection
        $escapedIds = array_map(function ($id) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $id) . "'";
        }, $selectedResidents);

        // Join the IDs into a single string
        $ids = implode(",", $escapedIds);

        // Delete the selected residents from the 'residents' table
        $query = "DELETE FROM residents WHERE resident_id IN ($ids)";

        if (mysqli_query($conn, $query)) {
            // Record the activity in the activity_logs table
            $activityType = 'Deleted residents';
            $activityDetails = 'Deleted residents with IDs: ' . implode(", ", $selectedResidents);

            // Insert into activity_logs table
            $logQuery = "INSERT INTO activity_logs (user_id, activity_type, activity_details, timestamp)
                         VALUES ('$adminId', '$activityType', '$activityDetails', NOW())";

            if (!mysqli_query($conn, $logQuery)) {
                // If logging fails, log the error
                error_log("Failed to insert activity log: " . mysqli_error($conn));
            }

            // Success response
            $responseMessage = "delete_status=Residents deleted successfully";
        } else {
            // Failure response
            $responseMessage = "delete_status=Failed to delete residents";
        }
    } else {
        // No residents selected
        $responseMessage = "delete_status=No residents selected";
    }

    // Redirect to the previous page with the response message
    $baseUrl = strtok($_SERVER['HTTP_REFERER'], '?'); 
    $redirectUrl = $baseUrl . "?" . $responseMessage; 
    header("Location: $redirectUrl");
    exit;
} else {
    // Redirect if the request method is not POST or no residents selected
    header('Location: residents_list.php');
    exit;
}
?>
