<?php
session_start(); 
include('auth_check.php');
include 'db_conn2.php';
include('./sidebar-floodalerts.php'); 

$sql_new_alerts = "SELECT * FROM sensor_data WHERE status = 'NEW' ORDER BY id DESC, timestamp DESC;";
$result_new_alerts = $conn->query($sql_new_alerts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Alerts</title>
    <link rel="stylesheet" href="floodalerts.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>


<div class="newalerts">
<h2>NEW FLOOD ALERTS</h2>
<hr style="color: gray; min-width: 2000px; position: absolute; margin: 10px 0px 0px -100px;">
    <div class ="tablecontainer">
<table id="newalertTable" class="table table-bordered">
<?php
// Fetch new flood alerts
$sql_new_alerts = "SELECT * FROM sensor_data WHERE status = 'NEW' ORDER BY id DESC, timestamp DESC;";
$result_new_alerts = $conn->query($sql_new_alerts);

if ($result_new_alerts->num_rows > 0) {
    echo '<table>';
    echo '<thead>
            <tr>
                <th>Flood Alert ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Height</th>
                <th>Height Rate</th>
                <th>Flow</th>
                <th>Water Level</th>
                <th>Action</th>
            </tr>
          </thead>';
    echo '<tbody>';

    $rows = $result_new_alerts->fetch_all(MYSQLI_ASSOC);
    $defaultMeter = 1.0;

    for ($i = 0; $i < count($rows); $i++) {
        $id = $rows[$i]['id'];
        $timestamp = $rows[$i]['timestamp'];
        $meters = $rows[$i]['meters'];
        $rate = $rows[$i]['rate'];
        $alert_level = $rows[$i]['alert_level'];
        $status = $rows[$i]['status'];
        $date = date("m/d/Y", strtotime($timestamp));
        $time = date("g:i:s A", strtotime($timestamp));

        // Determine waterflow trend
        $previousMeters = $i > 0 ? $rows[$i - 1]['meters'] : $defaultMeter;
        $nextMeters = $rows[$i + 1]['meters'] ?? $defaultMeter;
        $flow = ($meters > $nextMeters)
            ? '<span class="material-symbols-rounded trending-up">trending_up</span>'
            : (($meters < $nextMeters)
                ? '<span class="material-symbols-rounded trending-down">trending_down</span>'
                : '<span class="material-symbols-rounded stable">stable</span>');

        // Map alert levels
        $alertMapping = [
            "NORMAL LEVEL" => "NORMAL",
            "LOW LEVEL" => "LOW",
            "MEDIUM LEVEL" => "MODERATE",
            "CRITICAL LEVEL" => "CRITICAL"
        ];
        $mappedAlertLevel = $alertMapping[$alert_level] ?? $alert_level;

        // Render table row
        echo "<tr>
                <td>{$id}</td>
                <td>{$date}</td>
                <td>{$time}</td>
                <td>{$status}</td>
                <td>{$meters} m</td>
                <td>{$rate} m/min</td>
                <td>{$flow}</td>
                <td>{$mappedAlertLevel}</td>
                <td>
                    <button type=\"button\" class=\"btn btn-primary review-alert\" 
                        style=\"background-color: #59C447; border: none;\" 
                        data-bs-toggle=\"modal\" 
                        data-bs-target=\"#floodAlertModal\" 
                        data-id=\"{$id}\">
                        REVIEW ALERT
                    </button>
                </td>
              </tr>";
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo "<p>No data available.</p>";
}
?>

</table>
</div>
</div>

<div class="recentalerts">
<h2>RECENT VERIFIED FLOOD ALERTS</h2>
<hr style="color: gray; min-width: 2000px; position: absolute; margin: 10px 0px 0px -300px;">
    <div class ="tablecontainer-recent">
<table id="recentalertTable">
<?php
// Fetch verified flood alerts
$sql_verified_alerts = "SELECT * FROM flood_alerts WHERE alert_status = 'Verified'";
$result_verified_alerts = $conn->query($sql_verified_alerts);

echo '<table border="0">';
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

if ($result_verified_alerts->num_rows > 0) {
    while ($row = $result_verified_alerts->fetch_assoc()) {
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
?>
</table>
</div>
</div>

<div class="status" style="margin-left: 400px; margin-top: 100px;">
<img class="status-image" src="images/status.png" alt="Description of the image" style="width: 37%; margin-left: 520px; top">  
</div>

<!-- Modal: Flood Alert Management -->
<div class="modal fade" id="floodAlertModal" tabindex="-1" aria-labelledby="floodAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="floodAlertModalLabel">Flood Alert Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Top  -->
                <div class="top-section">
                    <div class="top-left d-flex">
                        <!-- Livestream  -->
                        <div class="livestream flex-grow-1 me-3">
                            <div style="background-color: #e9ecef; height: 200px; margin-bottom: 10px;"></div>
                            <p>Latest Update As Of: <strong>20 October 2024 | 11:01 AM</strong></p>
                        </div>

                        <!-- Info  -->
                        <div class="info-box d-flex flex-column flex-grow-1">
                            <button class="btn btn-secondary w-100 mb-3">See More Information ></button>
                            <div class="d-flex justify-content-between">      
                                <div>
                                    <h5 class="text-warning">MODERATE</h5>
                                    <span>Water Level</span>
                                </div>
                                <div>
                                    <h5>13 meters</h5>
                                    <span>Height</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <div>
                                    <h5 class="text-danger">⬆</h5>
                                    <span>Flow</span>
                                </div>
                                <div>
                                    <h5>0.04 m/min</h5>
                                    <span>Actual Speed Rate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="top-right">
                        <p>SUMMARY</p>
                        <table style="overflow: auto;">
                            <thead>
                                <tr>
                                    <th >Flood Alert ID</th>
                                    <th>Flood Alert Status</th>
                                    <th>SMS Status</th>
                                    <th>SMS Status Reason</th>
                                </tr>
                            </thead>
                            <tbody id="summaryTableBody">
                                <?php
                                foreach ($rows as $row) {
                                    $id = $row['id'];
                                    echo "<tr id='row_{$id}'>
                                            <td>{$id}</td>
                                            <td id='status_{$id}'></td>
                                            <td id='sms_{$id}'></td>
                                            <td id='sms_reason_{$id}'></td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="modal-footer">
                            <!-- Cancel -->
                            <button type="button" class="btn btn-danger" onclick="window.location.href='flood_alerts.php'">Cancel</button>

                              <!-- Confirm -->
                              <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#sendSmsModal" id="confirmButton" disabled >Confirm</button>
                        </div>
                    </div>
                </div>
                <!-- Bottom  -->
                <div class="bottom mt-3">
                    <p>VERIFY THE FLOOD ALERT(S) BELOW:</p>
                    <table style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th style="width: 150px;">Flood Alert ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Height</th>
                                <th>Height Rate</th>
                                <th>Flow</th>
                                <th>Water Level</th>
                                <th>Mark As:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($rows as $index => $row) {
                                $id = $row['id'];
                                $timestamp = $row['timestamp'];
                                $meters = $row['meters'];
                                $rate = $row['rate'];
                                $alert_level = $row['alert_level'];
                                $status = $row['status'];
                                $date = date("m/d/Y", strtotime($timestamp));
                                $time = date("g:i:s A", strtotime($timestamp));

                                $flow = ($index == 0)
                                    ? (($meters > $defaultMeter) 
                                        ? '<span class="material-symbols-rounded trending-up">trending_up</span>' 
                                        : (($meters < $defaultMeter) 
                                            ? '<span class="material-symbols-rounded trending-down">trending_down</span>' 
                                            : '<span class="material-symbols-rounded stable">stable</span>'))
                                    : (($meters > ($rows[$index + 1]['meters'] ?? $defaultMeter)) 
                                        ? '<span class="material-symbols-rounded trending-up">trending_up</span>' 
                                        : (($meters < ($rows[$index + 1]['meters'] ?? $defaultMeter)) 
                                            ? '<span class="material-symbols-rounded trending-down">trending_down</span>' 
                                            : '<span class="material-symbols-rounded stable">stable</span>'));

                                $alertMapping = [
                                    "NORMAL LEVEL" => "NORMAL",
                                    "LOW LEVEL" => "LOW",
                                    "MEDIUM LEVEL" => "MODERATE",
                                    "CRITICAL LEVEL" => "CRITICAL"
                                ];
                                $mappedAlertLevel = $alertMapping[$alert_level] ?? $alert_level;

                                echo "<tr>
                                        <td>{$id}</td>
                                        <td>{$date}</td>
                                        <td>{$time}</td>
                                        <td>{$status}</td>
                                        <td>{$meters} m</td>
                                        <td>{$rate} m/min</td>
                                        <td>{$flow}</td>
                                        <td>{$mappedAlertLevel}</td>
                                        <td>
                                            <td>
    <button class='buttons' id='falseAlarmBtn_{$id}' onclick='toggleFalseAlarm({$id})'>FALSE ALERT</button>
    <button class='buttons' id='verifyBtn_{$id}' onclick='toggleVerified({$id})'>VERIFIED</button>
</td>

                                        </td>
                                      </tr>";
                            }
                            $mostRecentAlertId = isset($rows[0]) ? $rows[0]['id'] : null;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Send SMS -->
<div class="modal fade" id="sendSmsModal" tabindex="-1" aria-labelledby="sendSmsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendSmsModalLabel">Send SMS Alert</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Send SMS Alert to the registered residents</strong><br>(Magpadala ng mensahe sa mga residente)</p>
                <p>
                    <strong>Flood Alert ID:</strong> 0000062<br>
                    <strong>Flood Alert Status:</strong> Verified<br>
                    <strong>Date (Petsa):</strong> 10/20/2024<br>
                    <strong>Water Level:</strong> Moderate<br>
                    <strong>Height:</strong> 13 meters<br>
                    <strong>Time (Oras):</strong> 11:00:00 AM<br>
                    <strong>Number of Recipients:</strong> 10<br>
                    <strong>Flow:</strong> Rising (Pataas)<br>
                </p>
                <p>
                    <strong>Message Content (Mensahe):</strong><br>
                    FLOODPING (11:00AM, 20Oct24)<br>
                    Darius Creek: Moderate Water Level (13m)<br>
                    Patuloy ang pagtaas ng baha. Nagaganap ang sapilitang pagpapalitas para sa kaligtasan ng lahat. Ang evacuation sites ay sa Bagbag at Goodwill Elem School. Ang lahat ay pinag-iingat.
                </p>
                <button type="button" class="btn btn-success w-100">SEND SMS</button>
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('alertModal');
    const confirmButton = document.getElementById('confirmButton');


    function openModal() {
        modal.style.display = 'flex';
    }

    function closeModal() {
        const buttons = document.querySelectorAll('button');
        buttons.forEach(button => {
            button.classList.remove('clicked');
        });

        const statusCells = document.querySelectorAll('[id^="status_"]');
        const smsStatusCells = document.querySelectorAll('[id^="sms_"]');
        const smsReasonCells = document.querySelectorAll('[id^="sms_reason_"]');

        statusCells.forEach(cell => cell.innerText = '');
        smsStatusCells.forEach(cell => cell.innerText = '');
        smsReasonCells.forEach(cell => cell.innerText = '');

        modal.style.display = 'none';
        confirmButton.disabled = true; 
    }

    function toggleFalseAlarm(id) {
        const falseAlarmButton = document.getElementById('falseAlarmBtn_' + id);
        const verifyButton = document.getElementById('verifyBtn_' + id);  

        if (!falseAlarmButton.classList.contains('clicked')) {
            falseAlarmButton.classList.add('clicked');
            falseAlarmButton.classList.add('active');
            verifyButton.classList.remove('clicked'); 
            verifyButton.classList.remove('active');
            updateStatus(id, 'False Alert', 'No SMS', 'False Alert'); 
        } else {
            falseAlarmButton.classList.remove('clicked');
            falseAlarmButton.classList.remove('active')
            updateStatus(id, '', '', '');
        }

        checkConfirmButtonState(); 
    }

    function toggleVerified(id) {
        const verifyButton = document.getElementById('verifyBtn_' + id);
        const falseAlarmButton = document.getElementById('falseAlarmBtn_' + id);

        if (!verifyButton.classList.contains('clicked')) {
            verifyButton.classList.add('clicked');
        verifyButton.classList.add('active');  // Add active state to Verify button
        falseAlarmButton.classList.remove('clicked');
        falseAlarmButton.classList.remove('active'); 
            updateStatus(id, 'Verified', 'With SMS', 'Verified'); 
        } else {
            verifyButton.classList.remove('clicked');
        verifyButton.classList.remove('active'); 
            updateStatus(id, '', '', '');
        }

        checkConfirmButtonState(); 
    }


    function updateStatus(id, status, smsStatus, smsReason) {
    const statusCell = document.getElementById('status_' + id);
    const smsStatusCell = document.getElementById('sms_' + id);
    const smsReasonCell = document.getElementById('sms_reason_' + id);

    statusCell.innerText = status;
    smsStatusCell.innerText = smsStatus;
    smsReasonCell.innerText = smsReason;

    console.log('Status:', statusCell.innerText);
    console.log('SMS Status:', smsStatusCell.innerText);
    console.log('SMS Reason:', smsReasonCell.innerText);

    checkConfirmButtonState();
}

function checkConfirmButtonState() {
    const statusCells = document.querySelectorAll('[id^="status_"]');
    const smsStatusCells = document.querySelectorAll('[id^="sms_"]');
    const smsReasonCells = document.querySelectorAll('[id^="sms_reason_"]');

    const allFilled = [...statusCells, ...smsStatusCells, ...smsReasonCells].every(cell => cell && cell.innerText.trim() !== '');

    console.log('All Cells Filled:', allFilled); 

    confirmButton.disabled = !allFilled; // Enable/Disable button
}

// Function to check if the first alert is addressed
function checkFirstAlertStatus() {
    // Assuming the first alert's status is stored in an element with id 'status_1'
    const firstAlertStatus = document.querySelector('#status_1'); // Adjust the selector as per your table structure

    // Get all the review alert buttons
    const reviewButtons = document.querySelectorAll('.review-alert');

    // Check if the first alert is addressed (e.g., status not 'NEW')
    if (firstAlertStatus && firstAlertStatus.innerText.trim() !== 'NEW') {
        // Enable all review buttons
        reviewButtons.forEach(button => button.disabled = false);
    } else {
        // Disable all review buttons except the first one if the first alert is not addressed
        reviewButtons.forEach((button, index) => {
            if (index !== 0) {
                button.disabled = true;
            }
        });
    }
}

// Function to handle the verify action
function verifyAlert() {
    // Logic for the VERIFY action (e.g., marking an alert as verified)
    console.log("Alert Verified!");

    // Example of how to update the first alert status after verification
    updateFirstAlertStatus('Verified');
}

// Call the function to check alert status on page load
document.addEventListener('DOMContentLoaded', checkFirstAlertStatus);

// If a review button is clicked, update the first alert status
function updateFirstAlertStatus(status) {
    // Update the status of the first alert
    const firstAlertStatus = document.querySelector('#status_1'); // Ensure the correct selector
    firstAlertStatus.innerText = status;

    // Re-check the status of the first alert and enable/disable buttons
    checkFirstAlertStatus();
}

</script>



</body>
</html>
