<?php
session_start();
include('auth_check.php');
include('sidebar.php');
include('db_conn2.php');

$dateRange = isset($_GET['date_range']) ? $_GET['date_range'] : '';

// Fetch false alerts from flood_alerts table
$sql = "SELECT * FROM flood_alerts WHERE alert_status = 'False Alert'";
if (!empty($dateRange)) {
    $dates = explode(' - ', $dateRange);
    $startDate = date('Y-m-d', strtotime($dates[0]));
    $endDate = date('Y-m-d', strtotime($dates[1]));
    $sql .= " AND DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
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
    <title>FALSE ALERT LOGS</title>
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
            width: 95%;
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

        .main-content {
            flex: 3; 
        }

        .cards {
            flex: 1; 
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            font-size: 1em;
            margin: 0 0 5px;
            color: #02476A;
        }

        .card p {
            font-size: 1.5em;
            font-weight: bold;
            margin: 0;
            color: #FF4E42;
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
                <a href="authority_dashboard.php" class="back-button">
                    <span class="material-symbols-rounded">arrow_back</span>
                </a>
                <h2>FALSE ALERT LOGS</h2>
            </div>
            <hr>
            <!-- Filter Section -->
            <div class="filters">
                <form method="GET">
                    <input type="text" name="date_range" id="dateRangePicker" placeholder="Select Date Range" value="<?php echo htmlspecialchars($dateRange); ?>">
                    <button type="submit">Apply</button>
                </form>
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
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['flood_alert_id']) . "</td>
                                    <td>" . htmlspecialchars($row['date']) . "</td>
                                    <td>" . date("g:i A", strtotime($row['time'])) . "</td>
                                    <td>" . htmlspecialchars($row['water_level']) . "</td>
                                    <td>" . htmlspecialchars($row['flow']) . "</td>
                                    <td>" . htmlspecialchars($row['height']) . " m</td>
                                    <td>" . htmlspecialchars($row['sms_status']) . "</td>
                                    <td>" . htmlspecialchars($row['sms_status_reason']) . "</td>
                                    <td><button>VIEW</button></td>
                                </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

    <script>
        $(document).ready(function () {
            $('#FalseAlertLogTable').DataTable({
                searching: true,
                stateSave: true,
                pageLength: 10
            });
            $('#dateRangePicker').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });
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