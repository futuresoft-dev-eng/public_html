<?php
session_start();
include('auth_check.php');
include('./sidebar-LAfloodalertlogs.php');
include('db_conn2.php');


$smsStatusFilter = isset($_GET['sms_status']) ? $_GET['sms_status'] : '';

$dateRange = isset($_GET['date_range']) ? $_GET['date_range'] : '';

$sql = "SELECT * FROM flood_alerts";

if (!empty($smsStatusFilter)) {
    $sql .= " WHERE sms_status = '" . mysqli_real_escape_string($conn, $smsStatusFilter) . "'";
}

if (!empty($dateRange)) {
    $dates = explode(' - ', $dateRange);
    $startDate = date('Y-m-d', strtotime($dates[0]));
    $endDate = date('Y-m-d', strtotime($dates[1]));

    if (!empty($smsStatusFilter)) {
        $sql .= " AND DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
    } else {
        $sql .= " WHERE DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
    }
}

$sql .= " ORDER BY created_at DESC";



$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error fetching data: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLOOD ALERT LOGS</title>
    <link rel="icon" href="./images/Floodpinglogo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex; 
            gap: 20px;
            width: 100%;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .filters select,.filters input {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #02476A;
            font-size: 14px;
            background-color: #fff;
            color: #02476A;
        }
        .filters button {
            padding: 10px 20px;
            background-color: #02476A;
            color: #fff;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .filters select:focus {
            outline: none;
            border-color: #02476A;
            box-shadow: 0 0 3px rgba(2, 71, 106, 0.5);
        }
        .nav-tabs .nav-link {
            color: #02476A;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
            color: #02476A;
            border-bottom: 5px solid #02476A;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: #02476A;
            gap: 15px;
        }

        .back-button {
            background-color: #0073AC;
            color: white;
            padding: 8px 20px;
            border-radius: 15%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        table.dataTable {
            width: 100%;
            border-collapse: collapse;
        }

        table.dataTable th,
        table.dataTable td {
            text-align: center;
            padding: 12px 10px;
        }

        table.dataTable th {
            background-color: #02476A;
            color: #fff;
            font-weight: bold;
        }

        table.dataTable tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table.dataTable tbody tr:hover {
            background-color: #d9edf7;
        }

        .no-logs-message {
            text-align: center;
            font-size: 16px;
            color: #555;
            margin-top: 20px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header">
                <h2>VERIFIED FLOOD ALERT LOGS</h2>

                <div class="d-flex justify-content-end mb-3">
    <!-- 3-dot Dropdown Button -->
    <div class="dropdown me-2">
        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 6px 12px;">
            <i class="fa fa-ellipsis-v" style="font-size: 1.2rem;"></i> <!-- 3 dots icon -->
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="falsealert_logs.php">View False Alert Logs</a></li>
            <li><a class="dropdown-item" href="unverified_alert_logs.php">View Unverified Alert Logs</a></li>
        </ul>
        </div>
        </div>
                <div class="d-flex justify-content-end mb-3">
                <a href="floodheight_rate_logs.php" class="btn btn-primary" style="background-color: #0073AC; border: none;">
                    <span style="margin-right: 5px;">👁</span> VIEW HEIGHT RATE LOGS
                </a>
            </div>

            </div>
            <hr>
            <div class="mb-3">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?php echo empty($smsStatusFilter) ? 'active' : ''; ?>" href="?sms_status=">ALL</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($smsStatusFilter === 'With SMS') ? 'active' : ''; ?>" href="?sms_status=With SMS">With SMS</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($smsStatusFilter === 'No SMS') ? 'active' : ''; ?>" href="?sms_status=No SMS">NO SMS</a>
        </li>
    </ul>
</div>
    
    <!-- Filter Section -->
    <div class="filters">
                <form method="GET">
                    <input type="text" name="date_range" id="dateRangePicker" placeholder="Select Date Range" value="<?php echo htmlspecialchars($dateRange); ?>">
                    <button type="submit">Apply</button>
                </form>
            </div>

            <!-- Dropdown Filter for SMS Status and SMS Status Reason -->
<div class="mb-3 d-flex justify-content-start gap-2">
    <!-- SMS Status Filter -->
    <select id="smsStatusFilter" class="form-select" style="width: 200px;">
        <option value="">Select SMS Status</option>
        <option value="All">All</option>
        <option value="With SMS: Required">With SMS: Required</option>
        <option value="No SMS: Not Required">No SMS: Not Required</option>
        <option value="No SMS: Overtaken">No SMS: Overtaken</option>
        <option value="No SMS: Insufficient">No SMS: Insufficient</option>
    </select>
</div>



            <div class="table-responsive">
                 <table id="FalseAlertLogTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Flood Alert ID</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Water Level</th>
                        <th>Flow</th>
                        <th>Height</th>
                        <th>SMS Status</th>
                        <th>SMS Status Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>
                                <td>' . htmlspecialchars($row["flood_alert_id"]) . '</td>
                                <td>' . htmlspecialchars($row["date"]) . '</td>
                                <td>' . htmlspecialchars($row["time"]) . '</td>
                                <td>' . htmlspecialchars($row["water_level"]) . '</td>
                                <td>' . htmlspecialchars($row["flow"]) . '</td>
                                <td>' . htmlspecialchars($row["height"]) . '</td>
                                <td>' . htmlspecialchars($row["sms_status"]) . '</td>
                                <td>' . htmlspecialchars($row["sms_status_reason"]) . '</td>
                                <td><button>VIEW</button></td>
                              </tr>';
                    }
                } 
                ?>
                </tbody>
            </table>
        </div>

    <script>
        $(document).ready(function () {
    // Initialize the DataTable only once
    var table = $('#FalseAlertLogTable').DataTable({
        searching: true,
        stateSave: true,
        pageLength: 10
    });

    // Event Listener for SMS Status Dropdown
    $('#smsStatusFilter').on('change', function () {
        var selectedValue = $(this).val();

        if (selectedValue === "" || selectedValue === "All") {
            // Reset filter to show all rows
            table.column(6).search("").draw(); // Column 6 = SMS Status
            table.column(7).search("").draw(); // Column 7 = SMS Status Reason
        } else {
            // Search specific SMS Status
            table.column(6).search(selectedValue).draw();
        }
    });

    // Date Range Picker Initialization
    $('#dateRangePicker').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        }
    });

    // Apply Date Range Picker filter
    $('#dateRangePicker').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('#dateRangePicker').on('cancel.daterangepicker', function () {
        $(this).val('');
    });
});

    </script>
</body>
</html>

<?php
$conn->close();
?>