<?php 
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LA Dashboard</title>
    <link rel="icon" href="./images/Floodpinglogo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Symbols+Rounded">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Poppins;
            display: flex;
        }

        .container {
            width: 100%;
            padding: 20px;
            position: absolute;
            left: 245px;
            background-color: #f8f9fa;
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
            width: 1230px;
            margin-left: 20px;
        }

        .header-title {
            font-size: 24px;
             background-color: transparent;
        }

        .header-date {
            font-size: 18px;
            background-color: transparent;
        }

        .main-content {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .left-section {
            flex: 3;
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: absolute;
            left: 35px;
            top: 140px;
            width: 720px;
        }

        .right-section {
            flex: 2;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: absolute;
            width: 480px;
            height: 340px;
            gap: 20px;
            left: 775px;
            top: 140px;
            
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
            font-size: 13px;
            background-color: transparent;
        }

        .card-value {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
            color: #4597C0;
            background-color: transparent;
        }

        .highlight-moderate {
            color: orange;
            font-weight: bold;
        }

        .station-info {
            text-align: center;
            margin-left: 15px;
        }

        .station-info h3 {
            margin-top: -190px;
            margin-left: 140px;
        }

        .station-info p {
             margin-left: 140px;
        }

        .station-info img {
            max-width: 37%;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            text-align: left;
            margin-left: -10px;
        }

        .info-buttons button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            -ms-flex-align: end;
            margin-left: 400px;
        }

        .info-buttons .watch-live {
            background-color: #28a745;
            color: white;
            display: flex;
            margin-left: 280px;
        }

        .info-buttons .view-alerts {
            margin-top: -46px;
            margin-left: 455px;
            background-color: #007bff;
            color: white;
            display: flex;
        }
    </style>
</head>
<body>
    
    <?php 
        include('./db_conn2.php');
        include('./sidebar-LAdashboard.php'); 
        date_default_timezone_set('Asia/Manila'); 

     
        $totalResidents = 0;
        $result = $conn->query("SELECT COUNT(*) AS total FROM residents");
        if ($result) {
            $row = $result->fetch_assoc();
            $totalResidents = $row['total'];
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
                    <div class="titlee">Flood Monitoring</div>
                    <div class="dashboard-row">
                        <div class="card">
                            <div class="card-value highlight-moderate">MODERATE</div>
                            <h3>Water Level</h3>
                        </div>
                        <div class="card">
                            <div class="card-value">10.6 meters</div>
                            <h3>Height</h3>
                        </div>
                        <div class="card">
                            <div style="color:red;" class="card-value">0.02 m/min</div>
                            <h3>Actual Speed Rate</h3>
                        </div>
                        <div class="card">
                            <div class="card-value">0.01 m/hr</div>
                            <h3>Average Speed Rate</h3>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="titlee">Resident Alerts</div>
                    <div class="dashboard-row">
                        <div class="card">
                            <div class="card-value"><?php echo $totalResidents; ?></div>
                            <h3>Number of Residents</h3>
                        </div>
                        <div class="card">
                            <div class="card-value">2</div>
                            <h3>Issued Flood Alerts</h3>
                        </div>
                        <div class="card">
                            <div class="card-value">980</div>
                            <h3>Credits Available</h3>
                        </div>
                    </div>
                </div>
                <div class="station-info">
                    <img src="./images/darius.png" alt="Station Image">
                    <h3>DARIUS CREEK</h3>
                    <p><strong>Station Location:</strong> Near Santolan Street</p>
                    <div class="info-buttons">
                       <button class="watch-live" onclick="window.location.href='livecam.php';">WATCH LIVESTREAM</button>
                        <button class="view-alerts" onclick="window.location.href='flood_alerts.php';">VIEW FLOOD ALERTS</button>
                    </div>
                </div>
            </div>

            <!-- Right Section -->
            <div class="right-section">
                <div class="titlee">Flood Height Point Graph</div>
                <img src="./images/floodgraph.png" alt="Flood Graph" style="width: 100%; height: auto;">
                <div style="margin-top: 10px; font-size: 14px; color: gray;">Legend: Normal (9m) • Low (10m) • Moderate (13m) • Critical (15m)</div>
            </div>
        </div>
    </div>
</body>
</html>
