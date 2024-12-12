<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <link rel="icon" href="/images/logo.png" type="image/png">
    <style>
        /* Reset some basic styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        /* Main content area */
        .container {
            margin-left: 360px; /* Adjust this to sidebar width */
            padding: 20px;
        }

        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            font-size: 24px;
            border-radius: 5px;
            text-align: center;
        }

        /* Table for Activity Log */
        .activity-log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .activity-log-table th, .activity-log-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .activity-log-table th {
            background-color: #3498db;
            color: white;
        }

        .activity-log-table tr:hover {
            background-color: #f1f1f1;
        }

        .activity-log-table td {
            background-color: #ffffff;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
                padding-left: 15px;
            }

            .container {
                margin-left: 220px;
            }

            .header {
                font-size: 20px;
            }

            .activity-log-table th, .activity-log-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php
    session_start();
    include('./adminsidebar.php');
    ?>

    <div class="container">
        <!-- Page Header -->
        <div class="header">
            Activity Log
        </div>

        <!-- Activity Log Table -->
        <table class="activity-log-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>User</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2024-12-01</td>
                    <td>Login</td>
                    <td>John Doe</td>
                    <td>Successful login from IP 192.168.1.1</td>
                </tr>
                <tr>
                    <td>2024-12-02</td>
                    <td>Account Update</td>
                    <td>Jane Smith</td>
                    <td>Updated profile information</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>
