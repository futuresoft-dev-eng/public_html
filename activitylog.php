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

$query = "SELECT al.activity_id, al.user_id, al.activity_type, al.activity_details, al.timestamp, u.first_name, u.last_name
          FROM activity_logs al
          JOIN users u ON al.user_id = u.user_id
          WHERE u.role = '$filterRole'
          ORDER BY al.timestamp DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching activity logs: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, initial-scale=1, maximum-scale=1"> <!-- Ensures proper zoom scaling -->
    <title>Admin Activity Logs</title>
    <link rel="icon" href="/images/Floodpinglogo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px; 
            width: 95%; 
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box; 
        }

        h3 {
            text-align: left;
            color: #02476A;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .filters {
            margin-bottom: 20px;
        }

        .filters select {
            padding: 10px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #02476A;
        }

        table {
            width: 100%; 
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            word-wrap: break-word; 
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .dataTables_wrapper {
            overflow-x: auto; 
        }

        .dataTables_paginate .paginate_button {
            font-size: 14px;
        }

        .dataTables_length {
            margin-top: 10px;
        }

        .dataTables_filter {
            margin-top: 10px;
        }

        .dataTables_filter input {
            padding-left: 25px;
            border-radius: 5px;
            border: 1px solid #02476A;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3> Activity Logs</h3>

        <!-- Filter Section -->
        <div class="filters">
            <form method="GET" id="filterForm">
                <label for="filter_role">Filter by Role:</label>
                <select name="filter_role" id="filter_role" onchange="document.getElementById('filterForm').submit()">
                    <option value="Admin" <?php echo ($filterRole === 'Admin') ? 'selected' : ''; ?>>Admin Activity Logs</option>
                    <option value="Local Authority" <?php echo ($filterRole === 'Local Authority') ? 'selected' : ''; ?>>Local Authorities Activity Logs</option>
                </select>
            </form>
        </div>

        <table id="activityLogTable" class="display responsive nowrap">
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
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['activity_id']); ?></td>
                        <td><?php echo date('M d, Y, h:i A', strtotime($row['timestamp'])); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['activity_details'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#activityLogTable').DataTable({
                language: {
                    search: "",
                    searchPlaceholder: "Search activity logs..."
                },
                stateSave: true,
                responsive: true, 
                autoWidth: false,
            });
        });
    </script>
</body>
</html>
