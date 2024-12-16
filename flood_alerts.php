<?php
session_start();
include('auth_check.php');
include 'db_conn2.php';
include('./sidebar-floodalerts.php');

// Fetch NEW flood alerts
$sql_new_alerts = "SELECT * FROM sensor_data WHERE status = 'NEW' ORDER BY id DESC, timestamp DESC;";
$result_new_alerts = $conn->query($sql_new_alerts);

// Fetch VERIFIED flood alerts
$sql_verified_alerts = "SELECT * FROM flood_alerts WHERE alert_status = 'Verified'";
$result_verified_alerts = $conn->query($sql_verified_alerts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Alerts</title>
    <link rel="stylesheet" href="floodalerts.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>
<body>


<div class="newalerts">
    <h2>NEW FLOOD ALERTS</h2>
    <hr style="color: gray; min-width: 2000px; position: absolute; margin: 10px 0px 0px -100px;">
    <div class ="tablecontainer">
        <table id="newalertTable" class="table table-bordered">
            <?php
            if ($result_new_alerts->num_rows > 0) {
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
                $defaultheight = 1.0;

                foreach ($rows as $i => $row) {
                    $id = $row['id'];
                    $timestamp = $row['timestamp'];
                    $height = $row['height'];
                    $height_rate = $row['height_rate'];
                    $water_level = $row['water_level'];
                    $status = $row['status'];
                    $date = date("m/d/Y", strtotime($timestamp));
                    $time = date("g:i:s A", strtotime($timestamp));

                    // Determine waterflow trend
                    $previousheight = $i > 0 ? $rows[$i - 1]['height'] : $defaultheight;
                    $nextheight = $rows[$i + 1]['height'] ?? $defaultheight;
                    $flow = ($height > $nextheight)
                        ? '<span class="material-symbols-rounded trending-up">trending_up</span>'
                        : (($height < $nextheight)
                            ? '<span class="material-symbols-rounded trending-down">trending_down</span>'
                            : '<span class="material-symbols-rounded stable">stable</span>');

                    // Map alert levels
                    $alertMapping = [
                        "NORMAL LEVEL" => "NORMAL",
                        "LOW LEVEL" => "LOW",
                        "MEDIUM LEVEL" => "MODERATE",
                        "CRITICAL LEVEL" => "CRITICAL"
                    ];
                    $mappedAlertLevel = $alertMapping[$water_level] ?? $water_level;

                    // Render table row
                    echo "<tr>
                            <td>{$id}</td>
                            <td>{$date}</td>
                            <td>{$time}</td>
                            <td>{$status}</td>
                            <td>{$height} m</td>
                            <td>{$height_rate} m/min</td>
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
            } else {
                echo "<p>No data available.</p>";
            }
            ?>
        </table>
    </div>
</div>

<!-- Recent Alerts Section -->
<div class="recentalerts">
    <h2>DAILY RECEIVED FLOOD ALERTS</h2>
    <hr style="color: gray; min-width: 2000px; position: absolute; margin: 10px 0px 0px -300px;">
    <div class="tablecontainer-recent">
        <!-- Your Table -->
        <table id="recentalertTable" class="display table table-bordered">
            <thead>
                <tr>
                    <th>Flood Alert ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Water Level</th>
                    <th>Flow</th>
                    <th>Height</th>
                    <th>Flood Alert Status</th>
                    <th>SMS Status</th>
                    <th>SMS Status Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_verified_alerts->num_rows > 0) {
                    // Loop through each record
                    while ($row = $result_verified_alerts->fetch_assoc()) {
                        echo '<tr>
                                <td>' . htmlspecialchars($row["flood_alert_id"]) . '</td>
                                <td>' . htmlspecialchars($row["date"]) . '</td>
                                <td>' . htmlspecialchars($row["time"]) . '</td>
                                <td>' . htmlspecialchars($row["water_level"]) . '</td>
                                <td>' . htmlspecialchars($row["flow"]) . '</td>
                                <td>' . htmlspecialchars($row["height"]) . '</td>
                                <td>' . htmlspecialchars($row["alert_status"]) . '</td>
                                <td>' . htmlspecialchars($row["sms_status"]) . '</td>
                                <td>' . htmlspecialchars($row["sms_status_reason"]) . '</td>
                                <td><button>VIEW</button></td>
                              </tr>';
                    }
                } else {
                    echo '<tr><td colspan="10" style="text-align: center;">No records found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>



<!-- STATUS IMAGEEEEEE -->
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
                <!-- Top Section -->
                <div class="top-section">
                    <div class="top-left d-flex">
                        <!-- Livestream -->
                        <div class="livestream flex-grow-1 me-3">
                            <div style="background-color: #e9ecef; height: 200px; margin-bottom: 10px;"></div>
                            <p>Latest Update As Of: <strong>20 October 2024 | 11:01 AM</strong></p>
                        </div>

                        <!-- Info -->
                        <div class="info-box d-flex flex-column flex-grow-1">
                            <button class="btn btn-secondary w-100 mb-3">See More Information ></button>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-warning">MODERATE</h5>
                                    <span>Water Level</span>
                                </div>
                                <div>
                                    <h5>13 height</h5>
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

                    <!-- Summary -->
                    <div class="top-right">
                        <p>SUMMARY</p>
                        <table style="overflow: auto;">
                            <thead>
                                <tr>
                                    <th>Flood Alert ID</th>
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
                            <button type="button" class="btn btn-danger" onclick="window.location.href='flood_alerts.php'">Cancel</button>
                            <button type="button" class="btn btn-success" id="confirmButton" disabled>Confirm</button>
                        </div>
                    </div>
                </div>

  <!-- Bottom Section -->
<div class="bottom mt-3">
    <p>VERIFY THE FLOOD ALERT(S) BELOW:</p>
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 10%;">Flood Alert ID</th>
                <th style="width: 12%;">Date</th>
                <th style="width: 12%;">Time</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Height</th>
                <th style="width: 10%;">Height Rate</th>
                <th style="width: 10%;">Flow</th>
                <th style="width: 10%;">Water Level</th>
                <th style="width: 16%;">Mark As:</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($rows as $index => $row) {
                $id = $row['id'];
                $timestamp = $row['timestamp'];
                $height = $row['height'];
                $height_rate = $row['height_rate'];
                $water_level = $row['water_level'];
                $status = $row['status'];
                $date = date("m/d/Y", strtotime($timestamp));
                $time = date("g:i:s A", strtotime($timestamp));

                // Determine flow class
                $flowClass = ($index == 0)
                    ? (($height > $defaultheight) ? 'trending-up' : (($height < $defaultheight) ? 'trending-down' : 'stable'))
                    : (($height > ($rows[$index + 1]['height'] ?? $defaultheight)) ? 'trending-up' : (($height < ($rows[$index + 1]['height'] ?? $defaultheight)) ? 'trending-down' : 'stable'));

                // Clean flow text
                $flowText = str_replace('-', '_', $flowClass);

                // Map water level
                $alertMapping = [
                    "NORMAL LEVEL" => "NORMAL",
                    "LOW LEVEL" => "LOW",
                    "MEDIUM LEVEL" => "MODERATE",
                    "CRITICAL LEVEL" => "CRITICAL"
                ];
                $mappedAlertLevel = $alertMapping[$water_level] ?? $water_level;

                // Output table row
                echo "<tr>
                        <td>{$id}</td>
                        <td>{$date}</td>
                        <td>{$time}</td>
                        <td>{$status}</td>
                        <td>{$height} m</td>
                        <td>{$height_rate} m/min</td>
                        <td>
                            <span class='material-symbols-rounded {$flowClass}'>{$flowText}</span>
                        </td>
                        <td>{$mappedAlertLevel}</td>
                        <td class='text-center'>
                            <div class='d-flex justify-content-center gap-2 flex-wrap'>
                                <button class='btn btn-sm btn-danger' 
                                    id='falseAlarmBtn_{$id}' 
                                    data-id='{$id}' 
                                    data-flow='{$flowText}' 
                                    data-water-level='{$mappedAlertLevel}'
                                    onclick='toggleFalseAlarm(this)'>FALSE ALERT</button>
                                <button class='btn btn-sm btn-success' 
                                    id='verifyBtn_{$id}' 
                                    data-id='{$id}' 
                                    data-flow='{$flowText}' 
                                    data-water-level='{$mappedAlertLevel}'
                                    onclick='toggleVerified(this)'>VERIFIED</button>
                            </div>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</div>

        </div>
    </div>
</div>



<!-- Initialize DataTable -->
<script>
    $(document).ready(function() {
        $('#newalertTable').DataTable();
    });
    $('#newalertTable').DataTable({
    paging: true,  // Enable pagination
    searching: false, // Enable searching
    ordering: true, // Enable sorting
    pageLength: 10, // Set default number of rows per page

});
$(document).ready(function() {
        $('#recentalertTable').DataTable();
        });

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

</script>


<script>
const modal = document.getElementById("alertModal");
const confirmButton = document.getElementById("confirmButton");

// Function to open the modal
function openModal() {
    modal.style.display = "flex";
}

// Function to close the modal and reset its content
function closeModal() {
    const buttons = document.querySelectorAll("button");
    buttons.forEach((button) => {
        button.classList.remove("clicked");
    });

    const statusCells = document.querySelectorAll("[id^='status_']");
    const smsStatusCells = document.querySelectorAll("[id^='sms_']");
    const smsReasonCells = document.querySelectorAll("[id^='sms_reason_']");

    statusCells.forEach((cell) => (cell.innerText = ""));
    smsStatusCells.forEach((cell) => (cell.innerText = ""));
    smsReasonCells.forEach((cell) => (cell.innerText = ""));

    modal.style.display = "none";
    confirmButton.disabled = true;
}

  // Function to toggle "False Alert" status
  function toggleFalseAlarm(button) {
    const id = button.getAttribute("data-id");
    console.log(`Toggle False Alarm called for ID: ${id}`);

    const falseAlarmButton = document.getElementById("falseAlarmBtn_" + id);
    const verifyButton = document.getElementById("verifyBtn_" + id);

    if (!falseAlarmButton.classList.contains("clicked")) {
        // Mark as clicked and active
        falseAlarmButton.classList.add("clicked", "active");
        verifyButton.classList.remove("clicked", "active");

        updateSummary(id, "False Alert", "No SMS", "False Alert");
    } else {
        // Unmark the button
        falseAlarmButton.classList.remove("clicked", "active");
        updateSummary(id, "", "", "");
    }

    checkConfirmButtonState();
}


// Function to toggle "Verified Alert" status
function toggleVerified(button) {
    // Extract data attributes
    const id = button.getAttribute("data-id");
    const flow = button.getAttribute("data-flow").trim();
    const waterLevel = button.getAttribute("data-water-level").trim();

    console.log(`Toggle Verified called for ID: ${id}`);
    console.log(`Flow: ${flow}, Water Level: ${waterLevel}`);

    const verifyButton = document.getElementById("verifyBtn_" + id);
    const falseAlarmButton = document.getElementById("falseAlarmBtn_" + id);

    if (!verifyButton.classList.contains("clicked")) {
        // Mark the button as clicked and active
        verifyButton.classList.add("clicked", "active");
        falseAlarmButton.classList.remove("clicked", "active");

        // Initialize default values
        let smsStatus = "No SMS";
        let smsReason = "Not Required";

        // Determine SMS Status and Reason based on conditions
        if (flow === "trending_up" && waterLevel === "LOW") {
            smsStatus = "With SMS";
            smsReason = "Required";
        } else if (flow === "trending_up" && waterLevel === "MODERATE") {
            smsStatus = "With SMS";
            smsReason = "Required";
        } else if (flow === "trending_up" && waterLevel === "CRITICAL") {
            smsStatus = "No SMS";
            smsReason = "Not Required";
        } else if (flow === "trending_down" && (waterLevel === "LOW" || waterLevel === "MODERATE" || waterLevel === "NORMAL"))  {
            smsStatus = "No SMS";
            smsReason = "Not Required";
        } else if (flow === "trending_down" && waterLevel === "CRITICAL") {
            smsStatus = "No SMS";
            smsReason = "Not Required";
        }

        // Update the summary table with the final values
        updateSummary(id, "Verified", smsStatus, smsReason);
    } else {
        // If clicked again, unmark and reset the summary table
        verifyButton.classList.remove("clicked", "active");
        updateSummary(id, "", "", "");
    }

    // Check if the confirm button should be enabled
    checkConfirmButtonState();
}




function updateSummary(id, status, smsStatus, smsReason) {
    const statusCell = document.getElementById("status_" + id);
    const smsStatusCell = document.getElementById("sms_" + id);
    const smsReasonCell = document.getElementById("sms_reason_" + id);

    if (statusCell) statusCell.innerText = status || "";
    if (smsStatusCell) smsStatusCell.innerText = smsStatus || "";
    if (smsReasonCell) smsReasonCell.innerText = smsReason || "";

    console.log(`Updated Summary for ID: ${id}`);
    console.log(`Status: ${status}, SMS Status: ${smsStatus}, SMS Reason: ${smsReason}`);

    checkConfirmButtonState();  // Check if the Confirm button should be enabled
}





// Function to check if all rows are marked
function checkConfirmButtonState() {
    const statusCells = document.querySelectorAll("[id^='status_']");
    const allFilled = [...statusCells].every(cell => cell.innerText.trim() !== "");

    const confirmButton = document.getElementById("confirmButton");
    confirmButton.disabled = !allFilled;
}



// Confirm Button Functionality
document.getElementById("confirmButton").addEventListener("click", function () {
    const alertRows = document.querySelectorAll("[id^='row_']");
    const reviewedAlerts = [];

    // Collect reviewed statuses for all alerts
    alertRows.forEach((row) => {
        const id = row.id.split("_")[1]; // Extract ID from row ID
        const status = document.getElementById("status_" + id).innerText.trim();
        const smsStatus = document.getElementById("sms_" + id).innerText.trim();
        const smsReason = document.getElementById("sms_reason_" + id).innerText.trim();

        if (status) {
            reviewedAlerts.push({ id, status, smsStatus, smsReason });
        }
    });

    // Send the reviewed alerts to the backend
    if (reviewedAlerts.length > 0) {
        $.post("process_review.php", { alerts: reviewedAlerts })
            .done(function (response) {
                alert("Flood alerts successfully reviewed and transferred!");
                location.reload();
            })
            .fail(function () {
                alert("Error transferring flood alerts. Please try again.");
            });
    } else {
        alert("No alerts were reviewed.");
    }
});



// Function to handle the verify action
function verifyAlert() {
    console.log("Alert Verified!");
    updateFirstAlertStatus("Verified");
}

// Call the function to check alert status on page load
document.addEventListener('DOMContentLoaded', checkFirstAlertStatus);

// If a review button is clicked, update the first alert status
function updateFirstAlertStatus(status) {
    // Update the status of the first alert
    const firstAlertStatus = document.querySelector('#status_1'); // Ensure the correct selector
    firstAlertStatus.innerText = status;

    // Re-check the status of the first alert and enable/disable buttons
    checkConfirmButtonState();
}

</script>



</body>
</html>
