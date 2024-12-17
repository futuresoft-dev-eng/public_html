<?php
// Include the database connection file
include 'db_conn2.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the alerts sent from the frontend
    $alerts = $_POST['alerts'];

    if (empty($alerts)) {
        echo json_encode(['success' => false, 'error' => 'No alerts to process.']);
        exit;
    }

    $response = [];
    foreach ($alerts as $alert) {
        // Extract alert details from POST data
        $id = $alert['id'];
        $status = $alert['status'];
        $smsStatus = $alert['smsStatus'];
        $smsReason = $alert['smsReason'];

        // Retrieve the original alert data from `sensor_data` table
        $query = "SELECT * FROM sensor_data WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $response[] = [
                'id' => $id,
                'success' => false,
                'error' => "No data found for ID {$id} in sensor_data table."
            ];
            continue;
        }

        $row = $result->fetch_assoc();

        // Prepare data for insertion into `flood_alerts`
        $floodAlertId = $row['id'];
        $date = date("Y-m-d"); // Use the current date
        $time = date("H:i:s"); // Use the current time
        $waterLevel = $row['water_level'];
        $flow = $row['height_rate'] > 0 ? 'Rising' : 'Subsiding';
        $height = $row['height'];
        $heightRate = $row['height_rate'];

        // Insert reviewed alert into `flood_alerts` table
        $insertQuery = "INSERT INTO flood_alerts (
            flood_alert_id, date, time, water_level, flow, height, height_rate, alert_status, sms_status, sms_status_reason
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param(
            "ssssssssss",
            $floodAlertId, // flood_alert_id
            $date,         // date
            $time,         // time
            $waterLevel,   // water_level
            $flow,         // flow
            $height,       // height
            $heightRate,   // height_rate
            $status,       // alert_status
            $smsStatus,    // sms_status
            $smsReason     // sms_status_reason
        );

        if ($insertStmt->execute()) {
            // After successful insertion, delete from sensor_data table
            $deleteQuery = "DELETE FROM sensor_data WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $id);
            $deleteStmt->execute();

            $response[] = [
                'id' => $id,
                'success' => true,
                'message' => "Flood alert ID {$id} successfully reviewed, inserted into flood_alerts, and removed from sensor_data."
            ];
        } else {
            $response[] = [
                'id' => $id,
                'success' => false,
                'error' => "Failed to insert ID {$id} into flood_alerts: " . $insertStmt->error
            ];
        }
    }

    // Return a JSON response with the results of the operation
    echo json_encode($response);
    exit;
} else {
    // If the request is not POST, reject it
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}
