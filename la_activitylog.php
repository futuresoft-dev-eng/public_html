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
    <link rel="icon" href="/images/Floodpinglogo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
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

        .filters select {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #02476A;
            font-size: 14px;
            background-color: #fff;
            color: #02476A;
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
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="container">
    <div class="header">
    <a href="authority_dashboard.php" class="back-button">
    <span class="material-symbols-rounded">arrow_back</span>
</a>
        <h2>ACTIVITY LOG</h2>
        </div>
        <hr>

   
      

        <!-- Table -->
        <table id="activityLogTable" class="display">
              <!-- Filters -->
        <div class="filters">
            <form method="GET" id="filterForm">
                <select name="filter" id="filter" onchange="document.getElementById('filterForm').submit()">
                    <option value="me" <?php echo ($filterBy == 'me') ? 'selected' : ''; ?>>By Me</option>
                    <option value="all" <?php echo ($filterBy == 'all') ? 'selected' : ''; ?>>By All Local Authorities</option>
                </select>
            </form>
        </div>
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

    <script>
        $(document).ready(function () {
            $('#activityLogTable').DataTable({
                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                },
                stateSave: true,
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>