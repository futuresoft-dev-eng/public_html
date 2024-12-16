<?php
include 'db_conn2.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alert_id = $_POST['alert_id'];
    $status = $_POST['status'];
    $sms_status = $_POST['sms_status'];
    $sms_reason = $_POST['sms_reason'];

    $sql = "UPDATE flood_alerts 
            SET alert_status = ?, sms_status = ?, sms_status_reason = ?
            WHERE flood_alert_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $status, $sms_status, $sms_reason, $alert_id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
