<?php
include('./db_conn2.php');

// Fetch the total number of flood alerts
$result_new_alerts = $conn->query("SELECT COUNT(*) AS total FROM newalerts WHERE status = 'NEW'");
$totalFloodAlerts = ($result_new_alerts) ? $result_new_alerts->fetch_assoc()['total'] : 0;

// Fetch other sensor data if necessary
// Example: Replace with actual queries to fetch sensor data
$sensorData = [
    'alert_level' => 'MODERATE',
    'water_height' => 10.6,
    'actual_speed' => 0.02,
    'average_speed' => 0.01
];

// Return data as JSON
echo json_encode([
    'totalFloodAlerts' => $totalFloodAlerts,
    'sensorData' => $sensorData
]);
?>
