<?php
session_start();
include('auth_check.php');
include 'db_conn2.php';
include('./adminsidebar-activitylog.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$filterRole = isset($_GET['filter_role']) ? $_GET['filter_role'] : 'Admin';
$dateRange = isset($_GET['date_range']) ? $_GET['date_range'] : '';

$query = "SELECT al.activity_id, al.user_id, al.activity_type, al.activity_details, al.timestamp, u.first_name, u.last_name
          FROM activity_logs al
          JOIN users u ON al.user_id = u.user_id
          WHERE u.role = '$filterRole'";

if (!empty($dateRange)) {
    $dates = explode(' - ', $dateRange);
    $startDate = date('Y-m-d', strtotime($dates[0]));
    $endDate = date('Y-m-d', strtotime($dates[1]));
    $query .= " AND DATE(al.timestamp) BETWEEN '$startDate' AND '$endDate'";
}

$query .= " ORDER BY al.timestamp DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching activity logs: " . mysqli_error($conn));
}

$logs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $logs[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, initial-scale=1, maximum-scale=1">
    <title>Admin Activity Logs</title>
    <link rel="icon" href="/images/Floodpinglogo.png" type="image/png">
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
            width: 100%;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            padding: 20px 30px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            background-color: white;
            color: white;
            padding: 10px;
            border-radius: 8px;
            gap: 15px;
        }

        .back-button {
            background-color: #0073AC;
            color: white;
            padding: 8px  20px;
            border-radius: 15%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filters form {
            display: flex;
            gap: 10px;
        }

        .filters select,.filters input  {
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

        table.dataTable {
            width: 100%;
            border-collapse: collapse;
        }

        table.dataTable th,
        table.dataTable td {
            text-align: left;
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

        .dataTables_filter {
            text-align: right;
            margin-bottom: 20px;
        }

        .dataTables_filter input {
            width: 300px;
            padding: 8px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .dataTables_info {
            font-size: 14px;
        }

        .dataTables_paginate {
            margin-top: 10px;
            text-align: right;
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
        <div class="header">
            <a href="admin_dashboard.php" class="back-button">
                <span class="material-symbols-rounded">arrow_back</span>
            </a>
            <h2>ACTIVITY LOG</h2>
        </div>
        <hr>
        <!-- Filter Section -->
        <div class="filters">
            <form method="GET" id="filterForm">
                <label for="filter_role">Filter by Role:</label>
                <select name="filter_role" id="filter_role" onchange="document.getElementById('filterForm').submit()">
                    <option value="Admin" <?php echo ($filterRole === 'Admin') ? 'selected' : ''; ?>>Admin Activity Logs</option>
                    <option value="Local Authority" <?php echo ($filterRole === 'Local Authority') ? 'selected' : ''; ?>>Local Authorities Activity Logs</option>
                </select>
                <input type="text" name="date_range" id="dateRangePicker" value="<?php echo htmlspecialchars($dateRange); ?>" placeholder="Select Date Range">
                <button type="submit">Apply</button>
            </form>
        </div>

        <?php if (count($logs) > 0): ?>
            <table id="activityLogTable" class="display">
                <thead>
                    <tr>
                        <th>Activity ID</th>
                        <th>Date and Time</th>
                        <th>Performed By</th>
                        <th>Activity</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['activity_id']); ?></td>
                            <td><?php echo date('M d, Y, h:i A', strtotime($log['timestamp'])); ?></td>
                            <td><?php echo htmlspecialchars($log['first_name']) . " " . htmlspecialchars($log['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($log['activity_type']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($log['activity_details'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-logs-message">No activity logs found.</div>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function () {
            <?php if (count($logs) > 0): ?>
            $('#activityLogTable').DataTable({
                language: {
                    search: "",
                    searchPlaceholder: "Search activity logs..."
                },
                responsive: true,
                autoWidth: false,
            });
            <?php endif; ?>

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