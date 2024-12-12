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

$query = "SELECT al.activity_id, al.user_id, al.activity_type, al.activity_details, al.timestamp, u.first_name, u.last_name
          FROM activity_logs al
          JOIN users u ON al.user_id = u.user_id
          WHERE u.role = 'Admin'
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            width: 90%;
            margin: 30px 30px;
            margin-left: 200px;
        }
        h3 {
            text-align: left;
            color: #333;
            font-size: 22px;
            color: #02476A;
            font-weight: bold;
            margin-bottom: 20px;
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
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:hover {
            background-color: #f1f1f1;
        }

        /* DataTables customization */
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

        .dataTables_filter::before {
            content: '\e8b6'; 
            font-family: 'Material Icons';
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #02476A;
            pointer-events: none;
        }

        /* Loading Spinner */
        .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 8px solid #f3f3f3;
            border-top: 8px solid #02476A;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            display: none; /* Hide by default */
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }
            th, td {
                padding: 8px;
            }
            .container {
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Admin Activity Logs</h3>

        <div class="spinner" id="loadingSpinner"></div>
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
                        <td><?php echo htmlspecialchars($row['activity_details']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#loadingSpinner').show();

            $('#activityLogTable').DataTable({
                language: {
                    search: "",
                    searchPlaceholder: "Search activity logs..."
                },
                stateSave: true,
                responsive: true,
                initComplete: function () {
                    $('#loadingSpinner').hide();
                }
            });
        });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>
