<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Live Camera Feed</title>
    <link rel="icon" href="./images/Floodpinglogo.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Symbols+Rounded">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .container {
            padding: 20px;
            margin-left:200px;

        }

        .header {
            margin-bottom: 20px;
            color: #02476A;
            font-weight: bold;
            margin-left:50px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: bold;
            color: #02476A;
            margin-bottom: 10px;
        }

        .live-cam-feed {
            display: flex;
            gap: 20px;
            margin-left:50px;
        }

        .left-feed {
            flex: 3;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .right-stats {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .right-stats .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .right-stats .card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .right-stats .card .value {
            font-size: 18px;
            font-weight: bold;
        }

        .highlight-moderate {
            color: orange;
            font-weight: bold;
        }

        .station-info {
            margin-top: 20px;
        }

        .station-info .title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .recordings {
            margin-top: 20px;
            margin-left:50px;
        }

        .recordings table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-top: 10px;
        }

        .recordings table th, .recordings table td {
            text-align: center;
            border: 1px solid #ddd;
            padding: 8px;
        }

        .recordings table th {
            background-color: #f4f4f4;
        }

        .action-buttons button {
            padding: 5px 10px;
            font-size: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .action-buttons .watch {
            background-color: #28a745;
            color: white;
        }

        .action-buttons .download {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    include('auth_check.php');
    date_default_timezone_set('Asia/Manila'); 
    include('./adminsidebar-livecam.php'); 
     ?>
    <div class="container">
        <div class="header">
            <h1>LIVE CAMERA FEED</h1>
        </div>

        <div class="live-cam-feed">
            <!-- Left Section -->
            <div class="left-feed">
                <div>
                    <div style="background: #ddd; height: 400px; text-align: center; line-height: 400px;">
                        <span>Live Camera Feed Here</span>
                    </div>
                </div>
                <div class="station-info">
                    <div class="title">DARIUS CREEK</div>
                    <p><strong>Station Location:</strong> Near Santolan Street</p>
                    <button style="background-color: #02476A; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
                        MAXIMIZE LIVESTREAM
                    </button>
                </div>
            </div>

            <!-- Right Section -->
            <div class="right-stats">
                <div class="card">
                    <h3>LATEST UPDATE AS OF:</h3>
                    <div class="value"><?php echo date('h:i A'); ?></div>
                    <div><?php echo date('F d, Y'); ?></div>
                </div>
                <div class="card">
                    <h3>Water Level</h3>
                    <div class="value highlight-moderate">MODERATE</div>
                </div>
                <div class="card">
                    <h3>Height</h3>
                    <div class="value">13 meters</div>
                </div>
                <div class="card">
                    <h3>Actual Speed Rate</h3>
                    <div class="value">0.02 m/min</div>
                </div>
                <div class="card">
                    <h3>Average Speed Rate</h3>
                    <div class="value">0.01 m/hr</div>
                </div>
            </div>
        </div>

        <!-- Recordings Section -->
        <div class="recordings">
            <h2>LIVE RECORDINGS</h2>
            <table>
                <thead>
                    <tr>
                        <th>Recording ID</th>
                        <th>Date</th>
                        <th>Duration</th>
                        <th>Size</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recordings = [
                        ['id' => '1000007', 'date' => '10/20/2024', 'duration' => '24:00:00', 'size' => '24.7 GB'],
                        ['id' => '1000006', 'date' => '10/19/2024', 'duration' => '24:00:00', 'size' => '24.7 GB'],
                    ];

                    foreach ($recordings as $rec) {
                        echo "<tr>
                                <td>{$rec['id']}</td>
                                <td>{$rec['date']}</td>
                                <td>{$rec['duration']}</td>
                                <td>{$rec['size']}</td>
                                <td class='action-buttons'>
                                    <button class='watch'>WATCH</button>
                                    <button class='download'>DOWNLOAD</button>
                                </td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
