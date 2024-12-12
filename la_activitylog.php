<?php
session_start();
include('sidebar.php');
include('db_conn2.php');  

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view the activity logs.");
}

$filterBy = isset($_GET['filter']) ? $_GET['filter'] : 'me'; 

$sql = "SELECT 
            al.activity_id, 
            al.timestamp, 
            al.user_id, 
            al.activity_type, 
            al.activity_details, 
            u.first_name, 
            u.last_name
        FROM activity_logs al
        JOIN users u ON al.user_id = u.user_id";

if ($filterBy == 'me') {
    $user_id = $_SESSION['user_id']; 
    $sql .= " AND al.user_id = '$user_id'"; 
} elseif ($filterBy == 'all') {
    $sql .= " AND u.role = 'Local Authority'"; 
}

// Execute the query
$result = $conn->query($sql);

// Fetch
$logs = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['timestamp'] = date('M d, Y, h:i A', strtotime($row['timestamp'])); 
        $logs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <link rel="icon" href="/images/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
        .filters {
            margin-bottom: 20px;
        }
        .dataTables_paginate .paginate_button {
            font-size: 13px; 
            margin-top: 20px;
        }

        .dataTables_info {
            font-size: 13px; 
            margin-top: 20px;
        }

        .dataTables_length {
            margin-top: -30px; 
            font-size: 13px;
        }

        .dataTables_length select {
            font-size: 13px; 
        }

        .dataTables_filter label {
            margin-right: 5px;
            font-weight: bold;
            font-size: 13px;
        }

        .dataTables_filter {
            position: relative;
            display: flex;
            align-items: center;
            margin-top: -40px !important;
            margin-bottom: 30px;
            top: 10px;
        }

        .dataTables_filter input {
            width: 350px;
            padding: 8px !important;
            padding-left: 25px !important;
            border-radius: 5px;
            border: 1px solid #02476A;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);  
        }

        .dataTables_filter::before {
            content: '\e8b6'; 
            font-family: 'Material Symbols Rounded';
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #02476A;
            pointer-events: none;
        }
    </style>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Activity Log</h2>

        <!-- Filters Section -->
        <div class="filters">
            <form method="GET" id="filterForm">
                <select name="filter" id="filter" onchange="document.getElementById('filterForm').submit()">
                    <option value="me" <?php echo ($filterBy == 'me') ? 'selected' : ''; ?>>By Me</option>
                    <option value="all" <?php echo ($filterBy == 'all') ? 'selected' : ''; ?>>By All Local Authorities</option>
                </select>
            </form>
        </div>

        <!-- Activity Logs Table -->
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
                <?php if (count($logs) > 0): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['activity_id']); ?></td>
                            <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                            <td><?php echo htmlspecialchars($log['first_name']) . " " . htmlspecialchars($log['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($log['activity_type']); ?></td>
                            <td><?php echo htmlspecialchars($log['activity_details']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No activity logs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function () {
            const table = $('#activityLogTable').DataTable({ 
                language: {
                    search: "",
                    searchPlaceholder: "     Search..."
                },
                stateSave: true
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
