<?php
$servername = "localhost";
$username = "u452560305_myfloodping";
$password = "myfloodPING@2024";
$dbname = "u452560305_floodping";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve GET parameters
$height = isset($_GET['height']) ? $_GET['height'] : null;
$rate = isset($_GET['rate']) ? $_GET['rate'] : null;
$level = isset($_GET['level']) ? $_GET['level'] : null;

// Debugging: Display received parameters
var_dump($_GET);

if ($height !== null && $rate !== null && $level !== null) {
    // Map levels to descriptive names
    $level_map = [
        "Overflow" => "Critical Level",
        "Critical" => "Critical Level",
        "Moderate" => "Moderate Level",
        "Low" => "Low Level",
        "Normal" => "Normal Level"
    ];

    // Check if the received level exists in the mapping
    if (array_key_exists($level, $level_map)) {
        $alert_level = $level_map[$level];
    } else {
        $alert_level = "Unknown Level"; // Fallback for unexpected levels
    }

    // Define status based on level
    $status = ($alert_level == "Normal Level") ? "NEW" : "ENTRY";

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO sensor_data (meters, rate, alert_level, status) VALUES (?, ?, ?, ?)");

    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }

    $stmt->bind_param("ddss", $height, $rate, $alert_level, $status);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Data inserted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid input data!";
}

$conn->close();
?>
