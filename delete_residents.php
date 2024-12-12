<?php
session_start(); 
include_once('db_conn2.php');

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$adminId = $_SESSION['user_id'];

$checkAdminQuery = "SELECT * FROM users WHERE user_id = '$adminId'";
$result = mysqli_query($conn, $checkAdminQuery);

if (mysqli_num_rows($result) == 0) {
    die("Error: Admin user does not exist.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_residents'])) {
    $selectedResidents = json_decode($_POST['selected_residents'], true);

    if (!empty($selectedResidents)) {
        $escapedIds = array_map(function ($id) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $id) . "'";
        }, $selectedResidents);

        $ids = implode(",", $escapedIds);

        $query = "DELETE FROM residents WHERE resident_id IN ($ids)";

        if (mysqli_query($conn, $query)) {
            $activityType = 'Deleted residents';
            $activityDetails = 'Deleted residents with IDs: ' . implode(", ", $selectedResidents);

            $logQuery = "INSERT INTO activity_logs (user_id, activity_type, activity_details, timestamp)
                         VALUES ('$adminId', '$activityType', '$activityDetails', NOW())";

            if (!mysqli_query($conn, $logQuery)) {
                error_log("Failed to insert activity log: " . mysqli_error($conn));
            }

            $responseMessage = "delete_status=Residents deleted successfully";
        } else {
            $responseMessage = "delete_status=Failed to delete residents";
        }
    } else {
        $responseMessage = "delete_status=No residents selected";
    }

    $baseUrl = strtok($_SERVER['HTTP_REFERER'], '?'); 
    $redirectUrl = $baseUrl . "?" . $responseMessage; 
    header("Location: $redirectUrl");
    exit;
} else {
    header('Location: residents_list.php');
    exit;
}
?>
