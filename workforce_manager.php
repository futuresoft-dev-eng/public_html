<?php
session_start();
include 'db_conn2.php';
include('auth_check.php');
include 'adminsidebar-shift.php';

$userData = [];
if (isset($_GET['edit_user_id'])) {
    $userId = $_GET['edit_user_id'];
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_schedule'])) {
    echo "Form submitted!<br>";  // Debugging line
    
    $userId = $_POST['user_id'];
    $schedule = [];
    $shift = [];

    foreach (["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"] as $day) {
        if (isset($_POST[$day])) {
            $schedule[] = strtoupper(substr($day, 0, 3));  // Convert day name to 3-letter format
            $time = $_POST[$day . "_time"];  // Get the selected shift time

            switch ($time) {
                case "08:00 AM - 5:00 PM":
                    if (!in_array("Morning Shift", $shift)) $shift[] = "Morning Shift";
                    break;
                case "2:00 PM - 11:00 PM":
                    if (!in_array("Mid Shift", $shift)) $shift[] = "Mid Shift";
                    break;
                case "11:00 PM - 8:00 AM":
                    if (!in_array("Night Shift", $shift)) $shift[] = "Night Shift";
                    break;
            }
        }
    }

    // Convert array to string
    $scheduleStr = implode(", ", $schedule);
    $shiftStr = implode(", ", $shift);

    echo "Schedule: $scheduleStr <br>";  // Debugging line
    echo "Shift: $shiftStr <br>";  // Debugging line
    
    // Prepare the update SQL query
    $sql = "UPDATE users SET schedule = ?, shift = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    
    // Check for errors in the statement preparation
    if (!$stmt) {
        die("Error in query preparation: " . $conn->error);
    }

    // Bind parameters and execute the query
    $stmt->bind_param("ssi", $scheduleStr, $shiftStr, $userId);

    if ($stmt->execute()) {
        // Successfully updated
        echo "<script>alert('Schedule and shift updated successfully!'); window.location.href = 'workforce_manager-user.php';</script>";
    } else {
        // Error during execution
        echo "<script>alert('Error updating schedule and shift!');</script>";
        echo "Error: " . $stmt->error;  // Debugging line
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Rounded" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Workforce Management</title>

    <style>
        body {
            width: 1000px;
            background-color: white;
        }

        .main-content {
            max-width: 1000px;
            margin-left: 0px; 
            padding: 20px;
            background-color: white;
            overflow: hidden;
        }

        .container {
            width: 100%;
        }

        .title h3 {
            font-size: 25px;
            font-weight: bold;
            color: #02476A;
            margin: 20px 0px 0px 240px !important;
            width: 1000px;
        }

        .profile-container {
            border-radius: 8px;
            display: flex;
            flex-direction: row-reverse;
            gap: 30px;
        }

        .profile-info {
            width: 100%;
            margin: -20px 0px 0px -150px !important;
            position: absolute;
        }

        .info-group {
            width: 40%;
            background-color: white;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #ccc;
            border-radius: 8px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-left: 930px;
        }
       
        .info-item-id  {
            margin: -10px 0px 0px 210px;
            position: absolute;
            font-size: 14px;
        }

        .info-item-id input  {
            width: 123px;
            height: 35px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin: 180px 0px 0px 220px;
            font-size: 14px;
        }

        #profilePhotoPreview {
            text-align: center; 
            cursor: pointer;
            width: 125px; 
            height: 125px; 
            border: 2px solid #02476A; 
            overflow: hidden; 
            display: inline-block; 
            margin: 80px 0px 0px 1230px;
            position: absolute;
            z-index: 100 !important;
        }

        .profile-info, .profile-photo {
            flex: 1;
            color: #02476A;
            font-size: 17px;
        }

        #personal-info-title {
            margin-top: -0px;
            margin-bottom: 5px;
        }

        #job-info-title {
            margin-bottom: 7px;
        }

        #group-label {
            display: block;
            font-size: 12px;
            color: #02476A;
            margin-bottom: 6px;
            margin-left: 10px;
        }

        .info-item input,
        .info-item select {
            width: 55%;
            padding: 6px 5px;
            font-size: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            margin: 0px 0px -30px 0px;
            display: block;
            flex-direction: column;
            
        }

        .info-item input[readonly] {
            background-color: #e9e9e9;
        }

        #assign-btn {
            background-color: #02476A;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            width: 10%;
            margin-left: 940px;
            padding: 6px;
            margin-top: -22px;
            z-index: 100;
            position: absolute;
        }

        #assign-btn [type="submit"]:hover {
            background-color: #033F5E;
        }


        .shift-section {
            margin: -470px 0px 0px 400px;
            text-transform: uppercase;
            font-size: 15px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center; 
            gap: 20px; 
            margin-bottom: 20px; 
        }       

        .checkbox-group input[type="checkbox"] {
            width: 20px; 
            height: 20px;
            margin: 0; 
            cursor: pointer;
        }

        .checkbox-group select {
            display: flex;
            font-size: 15px !important; 
            padding: 6px 10px;
            height: 36px; 
            border-radius: 5px;
            border: none;
            color: #BABABA;
            text-transform: uppercase;
            font-size: 18px;
            width: auto; 
        }

        .checkbox-group {
            margin-top: 20px;
            width: 100%; 
        }

        .checkbox-group div {
            display: flex;
            justify-content: space-between; 
            width: 100%; 
        }

        .checkbox-group select option {
            font-size: 14px; 
        }

        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-left: 197px;
            margin-top: 575px;
            position: absolute;
            width: 80.9%;
        }

        .table th {
            color: #02476A;
            background-color: #E8F3F8;
            font-weight: bold;
            font-size: 13px;
            width: 680px;
        }

        .table td {
            font-size: 13px;
        }

        .table tbody td:last-child {
            text-align: center;
            display: flex;
            justify-content: center;
        }

        #residentTable {
            width: 100%;
            margin-top: 20px;
        }

        .buttons-container {
            display: flex;
            margin: 20px 0;
            position: absolute;
            top: 610px;
            left: 269px;
            border-radius: none !important;
        }

        .navigation-btn {
            min-width: 615px;
            height: 40px;
            background-color: #FFFFFF;
            color: #02476A;
            border: 1px solid #ccc;
            border-radius: none !important;
            border-top-left-radius: 0px;
            border-bottom-left-radius: 0px;
            border-top-right-radius: 0px;
            border-bottom-right-radius: 0px;
            font-size: 14px;
            text-transform: uppercase;
            cursor: pointer;
            text-transform: uppercase;
            transition: background-color 0.3s ease;
            z-index: 100!important;
        }

        .navigation-btn.active {
            background-color: #4597C0 !important; 
            color: white !important;
        }

        #userAccountsBtn {
            border-top-left-radius: 10px;
        
        }

        #residentsBtn {
            border-top-right-radius: 10px; 
            background-color: #FFFFFF;
            color: #02476A;
            border: 1px solid #ccc;
        }

        #edit-btn-123 {
            background-color: #4597C0;
            padding: 5px 15px;
            text-transform: uppercase;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            
        }

        #searchBar {
            width: 30%;
            border-radius: 5px;
            border: 1px solid #02476A;
            padding: 5px 10px;
            font-size: 14px;
            margin-left: 860px;
            position: absolute;
        }

        #searchBar + i {
            position: absolute;
            margin-left: 860px; 
            margin-top: 15px; 
            transform: translateY(-50%); 
            color: #aaa; 
            font-size: 16px; 
        }

        #roleFilter {
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #02476A;
            cursor: pointer;
            transition: border-color 0.3s ease;
            width: 20%;
        }

        #roleFilter option {
            padding: 10px;
        }

       #assign-btn:disabled {
            background-color: #ddd; 
            cursor: not-allowed; 
}
    </style>
</head>
<body>

<main class="main-content">
    <div class="title">
        <h3>WORKFORCE MANAGEMENT</h3>
    </div>
    <hr style="color: gray; width: 90%; position: absolute; margin: 10px 0px 0px 50px;">

    <div class="buttons-container">
    <button class="navigation-btn active" id="userAccountsBtn" onclick="activateButton('userAccountsBtn', 'workforce_manager.php')">Unassigned Accounts</button>
    <button class="navigation-btn" id="residentsBtn" onclick="activateButton('residentsBtn', 'workforce_manager-user.php')">User Accounts</button>
</div>

    <div class="container">
        
    <img id="profilePhotoPreview" src="<?= $userData['profile_photo'] ?? '#' ?>" alt="Profile Photo" width="100"><br>
    
        <!-- Personal Information -->
        <div class="profile-info">
            <br>
            <div class="info-group">
            <div class="info-item-id">   
                    <input type="text" value="<?= isset($userData['user_id']) ? htmlspecialchars($userData['user_id']) : '' ?>" readonly class="readonly-field"><br>
            </div>

                <div class="info-item"  style="margin: 10px 0px 0px 0px; position: relative; width: 100%;">
                    <p id="personal-info-title">Personal Information</p>
                    <label id="group-label">First Name
                        <input type="text" name="first_name" value="<?= $userData['first_name'] ?? '' ?>" readonly>
                    </label><br>
                </div>

                <div class="info-item" style="margin: 40px 0px 0px -140px; position: relative; width:150%">
                    <label id="group-label">Middle Name 
                        <input type="text" name="middle_name" value="<?= $userData['middle_name'] ?? '' ?>" readonly>
                    </label><br>
                </div>

                <div class="info-item">
                    <label id="group-label">Last Name 
                        <input type="text" name="last_name" value="<?= $userData['last_name'] ?? '' ?>" readonly>
                    </label><br>
                </div>

                <div class="info-item" style="margin: 0px 0px 0px -140px; position: relative; width:150%">
                    <label id="group-label">Suffix 
                        <input type="text" name="suffix" value="<?= $userData['suffix'] ?? '' ?>" readonly>
                    </label><br>
                </div>

                <div class="info-item" id="personal">
                    <label id="group-label">Sex
                        <input type="text" name="sex" value="<?= $userData['sex'] ?? '' ?>" readonly>
                    </label><br>
                </div>

                <div class="info-item" id="personal" style="margin: 0px 0px 0px -140px; position: relative; width:150%;">
                    <label id="group-label">Mobile Number 
                        <input type="text" name="contact_no" value="<?= $userData['contact_no'] ?? '' ?>" readonly>
                    </label><br>
                </div>

                <div class="info-item" id="personal" style="margin: 0px 0px 0px 0px; width:280%;">
                    <label id="group-label">Email 
                        <input type="text" name="email" value="<?= $userData['email'] ?? '' ?>" readonly>
                    </label><br>
                </div>

                <div class="info-item" id="personal" style="margin: 70px 0px 0px -350px; width:210%;">
                    <p id="job-info-title">Job Description</p>
                    <label id="group-label">Role
                        <input type="text" name="role" value="<?= $userData['role'] ?? '' ?>" readonly>
                    </label><br>
                </div>

                <div class="info-item" id="personal" style="margin: -70px 0px 0px 290px; width:128%;">
                    <label id="group-label">Position
                        <input type="text" name="position" value="<?= $userData['position'] ?? '' ?>" readonly>
                    </label><br>

                    <button type="submit" name="assign_schedule" style="margin: 20px 0px 0px 200px; position: absolute; display: none;" disabled>ASSIGN</button><br><br>
                </div>
            </div>

    <!-- Work Day Shift -->
    <div class="shift-section">
            <h4>Work Day Shift</h4>
            <form method="POST" action="">
                <input type="hidden" name="user_id" value="<?= $userData['user_id'] ?? '' ?>">

                <?php
                    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                    $existingSchedule = explode(", ", $userData['schedule'] ?? '');
                    $existingShifts = json_decode($userData['shift'] ?? '{}', true);

                    $dayMargins = [
                    "Monday" => "20px",
                    "Tuesday" => "20px",
                    "Wednesday" => "0px",
                    "Thursday" => "15px",
                    "Friday" => "40px",
                    "Saturday" => "15px",
                    "Sunday" => "30px"
                ];

            foreach ($days as $day) {
                    $dayShort = strtoupper(substr($day, 0, 3)); 
                    $checked = in_array($dayShort, $existingSchedule) ? "checked" : "";
                    $selectedShift = $existingShifts[$day] ?? "";
                    $marginLeft = $dayMargins[$day] ?? "0px"; 
    
            echo "<div class='checkbox-group'>
                    <label>
                        <input type='checkbox' name='$day' $checked> $day
                        <select name='${day}_time' style='margin-left: $marginLeft;'>
                            <option value='08:00 AM - 5:00 PM' " . ($selectedShift == "08:00 AM - 5:00 PM" ? "selected" : "") . ">Morning Shift - 08:00 AM - 5:00 PM</option>
                            <option value='2:00 PM - 11:00 PM' " . ($selectedShift == "2:00 PM - 11:00 PM" ? "selected" : "") . ">Mid Shift - 2:00 PM - 11:00 PM</option>
                            <option value='11:00 PM - 8:00 AM' " . ($selectedShift == "11:00 PM - 8:00 AM" ? "selected" : "") . ">Night Shift - 11:00 PM - 8:00 AM</option>
                </select>
            </label>
          </div>";
}
?>
                <button id="assign-btn" type="submit" name="assign_schedule" disabled>ASSIGN</button>
            </form>          
        </div>
    </div>

    <!-- Table Container (for showing users) -->
    <div class="table-container">
    <div class="filters" style="margin-bottom: 15px;">
                <!-- Search Bar -->
                <div class="search-container" style="position: relative; width: 100%; max-width: 300px;">
                    <input type="text" id="searchBar" placeholder="Search..." onkeyup="filterTable()" style="padding-left: 30px; width: 100%; padding-right: 20px;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);"></i>
                </div>

                <!-- Role Filter -->
                <select id="roleFilter" onchange="filterTable()" style="margin-left: 20px; padding: 8px 12px; width: auto;">
                    <option value="">Filter by Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Local Authority">Local Authority</option>
                </select>
            </div>

        <table id="residentTable" class="table table-bordered">
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Suffix</th>
                <th>Day Schedule</th>
                <th>Time Schedule</th>
                <th>Role</th>
                <th>Account Status</th>
                <th>Action</th>
            </tr>

            <tbody id="userTableBody">
                <?php
                // Fetch users from the database
                $result = $conn->query("SELECT user_id, first_name, middle_name, last_name, suffix, schedule, shift, role, account_status FROM users WHERE schedule IS NULL AND shift IS NULL");
                while ($row = $result->fetch_assoc()) {
                    $schedule = $row['schedule'] ?: "Unassigned";
                    $shift = $row['shift'] ?: "Unassigned";

                    echo "<tr data-role='". htmlspecialchars($row['role']) ."'>
                        <td>{$row['user_id']}</td>
                        <td>{$row['first_name']}</td>
                        <td>{$row['middle_name']}</td>
                        <td>{$row['last_name']}</td>
                        <td>{$row['suffix']}</td>
                        <td>{$schedule}</td>
                        <td>{$shift}</td>
                        <td>{$row['role']}</td>
                        <td>{$row['account_status']}</td>
                        <td><a href='?edit_user_id={$row['user_id']}' id='edit-btn-123'><i class='fas fa-pen'></i> Edit</a></td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>     
</main>

<script>
    function activateButton(buttonId, redirectUrl) {
        const buttons = document.querySelectorAll('.navigation-btn');
        buttons.forEach((btn) => {
            btn.classList.remove('active');
        });

        const activeButton = document.getElementById(buttonId);
        activeButton.classList.add('active');

        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    }

    let originalRows = [];

    window.onload = function() {
    
    // Store the rows when the page loads
    originalRows = Array.from(document.querySelectorAll("#userTableBody tr"));
    };

    function filterTable() {
        let searchValue = document.getElementById('searchBar').value.toLowerCase();
        let roleFilter = document.getElementById('roleFilter').value.toLowerCase();

    // Filter the rows based on search and role filter
    let filteredRows = originalRows.filter(row => {
        let userId = row.children[0].textContent.toLowerCase();
        let firstName = row.children[1].textContent.toLowerCase();
        let lastName = row.children[3].textContent.toLowerCase();
        let role = row.getAttribute('data-role').toLowerCase(); // Use the data-role attribute for role filtering
        let schedule = row.children[5].textContent.toLowerCase();
        let shift = row.children[6].textContent.toLowerCase();

        // Return true if any of the fields match the searchValue and the role filter matches
        return (userId.includes(searchValue) || firstName.includes(searchValue) || lastName.includes(searchValue)) &&
               (roleFilter === '' || role.includes(roleFilter));
    });

    let tableBody = document.getElementById('userTableBody');
    tableBody.innerHTML = '';  // Clear the current table body

    // If no rows match, show 'No matching records found'
    if (filteredRows.length === 0) {
        tableBody.innerHTML = "<tr><td colspan='10' style='text-align:center;'>No matching records found</td></tr>";
    } else {
        // Append the filtered rows if any are found
        filteredRows.forEach(row => tableBody.appendChild(row));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.checkbox-group input[type="checkbox"]');
    const assignButton = document.getElementById('assign-btn');

    // Save the initial state of the checkboxes
    const initialState = Array.from(checkboxes).map(checkbox => checkbox.checked);

    // Function to check if any checkbox is selected or changed
    function updateAssignButtonState() {
        let isAnyChecked = false;

        // Loop through all checkboxes and check if any are checked
        checkboxes.forEach(function(checkbox, index) {
            if (checkbox.checked !== initialState[index]) {
                isAnyChecked = true;  // Mark as changed
            }
        });

        // Enable the assign button if at least one checkbox is checked or any checkbox is changed
        if (isAnyChecked) {
            assignButton.disabled = false;
            assignButton.classList.remove('disabled');  // Remove any 'disabled' class if present
        } else {
            assignButton.disabled = true;
            assignButton.classList.add('disabled');  // Add a 'disabled' class to style if needed
        }
    }

    // Add event listeners to each checkbox
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateAssignButtonState);
    });

    // Initial check to set the correct state of the button when the page loads
    updateAssignButtonState();
});

</script>

</body>
</html>