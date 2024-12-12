<?php
include 'db_conn2.php';
$sql_false_alerts = "SELECT * FROM flood_alerts WHERE alert_status = 'False Alert'";
$result_false_alerts = $conn->query($sql_false_alerts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>False Alert Logs</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
   
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .trending-up {
            color: #EA3323;
        }
        .trending-down {
            color: #0BA4D7;
        }
        .stable {
            color: #808080;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<p>FALSE ALERT LOGS</p>
<?php 
echo '<table border="1">';
echo '<thead>
        <tr>
            <th>Flood Alert ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Water Level</th>
            <th>Flow</th>
            <th>Height</th>
            <th>Height Rate</th>
            <th>SMS Status</th>
            <th>SMS Status Reason</th>
            <th>Action</th>
        </tr>
      </thead>';
echo '<tbody>';

if ($result_false_alerts->num_rows > 0) {
    while ($row = $result_false_alerts->fetch_assoc()) {
        echo '<tr>
                <td>' . htmlspecialchars($row["flood_alert_id"]) . '</td>
                <td>' . htmlspecialchars($row["date"]) . '</td>
                <td>' . htmlspecialchars($row["time"]) . '</td>
                <td>' . htmlspecialchars($row["water_level"]) . '</td>
                <td>' . htmlspecialchars($row["flow"]) . '</td>
                <td>' . htmlspecialchars($row["height"]) . '</td>
                <td>' . htmlspecialchars($row["height_rate"]) . '</td>
                <td>' . htmlspecialchars($row["sms_status"]) . '</td>
                <td>' . htmlspecialchars($row["sms_status_reason"]) . '</td>
                <td><button>VIEW</button></td>
              </tr>';
    }
} else {
    echo '<tr><td colspan="10" style="text-align: center;">No records found</td></tr>';
}

echo '</tbody>';
echo '</table>';
$conn->close();
?>
</body>
</html>
