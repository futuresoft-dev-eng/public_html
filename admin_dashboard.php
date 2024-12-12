<?php
    session_start(); 
    include('auth_check.php');
    include('./adminsidebar-dashboard.php'); 
    include_once('db_conn2.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN Dashboard</title>
    <link rel="icon" href="./images/Floodpinglogo.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Symbols+Rounded">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Poppins;
            background-color: #f9f9f9;
            display: flex;
        }

        .container {
            width: 100%;
            padding: 20px;
            
        }

        .header {
            background-image: url('./images/bgwaves.jpg');
            background-size: cover;
            border-radius: 8px;
            padding: 20px;
            color: white;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: 100px;
            margin-left: 50px;
        }

        .header-title {
            font-size: 24px;
        }

        .header-date {
            font-size: 18px;
        }

        .main-content {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            margin-left: 50px;
        }

        .left-section {
            flex: 3;
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-left: 0px;
        }

        .right-section {
            flex: 2;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 0px;
        }

        .titlee {
            font-size: 18px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
            border-radius: 8px;
            background-color: #E8F3F8;
            color: #02476A;
            text-align: center;
        }

        .dashboard-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1;
            text-align: center;
        }

        .card h3 {
            margin: 0;
            font-size: 16px;
        }

        .card-value {
            font-size: 30px;
            font-weight: bold;
            margin: 10px 0;
            color: #4597C0;
        }

        .highlight-moderate {
            color: orange;
            font-weight: bold;
        }
        .station-info h3 {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}
        .station-info {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        .station-info img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .info-buttons button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .info-buttons .watch-live {
            background-color: #59C447;
            color: white;
            font-size: 14px;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .info-buttons .watch-live:hover {
            background-color: white;
            border: solid 2px #59C447;
            color: #59C447;
        }  
    </style>
</head>
<body>
    <?php 
        date_default_timezone_set('Asia/Manila'); 

        $totalResidents = 0;
        $activeAccounts = $lockedAccounts = $deactivatedAccounts = 0;

        $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM residents");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $totalResidents = $row['total'];
        }

        $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE account_status = 'Active'");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $activeAccounts = $row['total'];
        }

        $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE account_status = 'Locked'");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $lockedAccounts = $row['total'];
        }

        $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE account_status = 'Inactive'");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $deactivatedAccounts = $row['total'];
        }

        $residentData = [];
        $result = mysqli_query($conn, "
            SELECT 
                MONTHNAME(registered_at) AS month, 
                COUNT(*) AS count 
            FROM residents 
            WHERE YEAR(registered_at) = YEAR(CURDATE())
            GROUP BY MONTH(registered_at)
            ORDER BY MONTH(registered_at)
        ");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $residentData[] = $row;
            }
        }
    ?>
    <div class="container">
        <div class="header">
            <div class="header-title">FLOODPING: FLOOD MONITORING AND ALERT SYSTEM</div>
        </div>

        <div class="main-content">
            <!-- Left Section -->
            <div class="left-section">
                <div>
                    <div class="titlee">USERS</div>
                    <div class="dashboard-row">
                        <div class="card">
                            <div class="card-value highlight-moderate"><?php echo $totalResidents; ?></div>
                            <h3>Number of Residents</h3>
                        </div>
                        <div class="card">
                            <div class="card-value"><?php echo $activeAccounts; ?></div>
                            <h3>Active Accounts</h3>
                        </div>
                        <div class="card">
                            <div style="color:red;" class="card-value"><?php echo $lockedAccounts; ?></div>
                            <h3>Locked Accounts</h3>
                        </div>
                        <div class="card">
                            <div class="card-value"><?php echo $deactivatedAccounts; ?></div>
                            <h3>Deactivated Accounts</h3>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="titlee">RESIDENT REGISTRATION GROWTH</div>
                    <div class="dashboard-row">
                        <canvas id="residentChart" style="width: 100%; height: auto;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right Section -->
            <div class="right-section">
                <div class="titlee">Flood Height Point Graph</div>
                <img src="./images/floodgraph.png" alt="Flood Graph" style="width: 100%; height: auto;">
                <div style="background-color: white; border-radius: 8px; text-align: center; padding: 20px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-top: 50px; margin-bottom: 50px; font-size: 14px; color: gray;">
                    <b>Legend:</b><br> Normal (9m) • Low (10m) • Moderate (13m) • Critical (15m)
                </div>

                <div class="station-info">
                    <img src="./images/darius.png" alt="Station Image">
                    <h3>DARIUS CREEK</h3>
                    <p><strong>Station Location:</strong> Near Santolan Street</p>
                    <div class="info-buttons">
                    <button class="watch-live" onclick="window.location.href='adminlivecam.php';">WATCH LIVESTREAM</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        
        const labels = <?php echo json_encode(array_column($residentData, 'month')); ?>;
        const data = <?php echo json_encode(array_column($residentData, 'count')); ?>;

       
        const ctx = document.getElementById('residentChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Resident Registrations',
                    data: data,
                    borderColor: '#02476A',
                    backgroundColor: 'rgba(2, 71, 106, 0.2)',
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
