<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Rounded" rel="stylesheet">
    <link rel="icon" href="./images/Floodpinglogo.png" type="image/png">

    <style>
        body {
            font-family: Poppins, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden; 

        }

        .sidebar {
            display: flex;
            flex-direction: column;
            height: 100vh;
            background-color: #E8F3F8;
            padding-top: 20px;
            width: 250px;
            min-width: 250px; 
            position: fixed;
            z-index: 1;
            overflow-y: auto;
            transition: width 0.9s ease;
        }

        .main-content {
            margin-left: 10px; 
            padding: 20px;
            flex-grow: 1;
            min-height: 100vh;
            background-color: #f8f9fa;
            box-sizing: border-box;
            overflow-y: auto; 

        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: left;
            
            padding: 10px 0;
            background-color: #02476A;
            color: white;
            gap: 10px;
        }

        .sidebar-logo img {
            width: 45px;
            margin-left: 15px;
            margin-right:5px;
        }

        .sidebar-content {
            flex-grow: 1;
            margin-top: 18px;
            font-size: 14px;
        }

        .time-section {
            color: white;
            padding: 0px 10px 10px 10px;
            text-align: center;
            background-color: #02476A;
        }

        .time-section .time {
            font-size: 27px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .time-section .date {
            font-size: 13px;
            margin-bottom: 16px;
        }

        .station {
            background-color: #386A83;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            padding-left: 30px;
            margin: 6px 5px;
            border-radius: 5px;
            font-size: 13px;
            border: none;
            position: relative;
        }

        .station::before {
            content: "";
            position: absolute;
            left: 30px;
            width: 12px;
            height: 12px;
            background-color: #F2CA00;
            border-radius: 50%;
        }

        .section-title {
            font-size: 12px;
            color: #4F8AA7;
            padding: 2px 20px 7px 20px;
            margin-top: 10px;
            margin-bottom: 0px;
        }

        .nav-link {
            color: #002D44;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            border-radius: 0 10px 10px 0;
            transition: background-color 0.3s ease, padding 0.3s ease;
            margin: 0 0 7px 0;
            font-size: 14px;
        }

        .nav-link:hover {
            background-color: #4597C0;
            color: #fff;
            border-radius: 0 25px 25px 0;
            padding-left: 25px;
        }

        .material-symbols-rounded {
            margin-right: 10px;
            font-size: 20px;
        }

        .user-info-container {
            margin-top: auto;
            background-color: #d6e7f0;
            padding: 10px;
            display: flex;
            align-items: center;
            border-top: 1px solid #e9ecef;
            gap: 10px;
            justify-content: space-between;
        }

        .user-link {
            color: #002D44;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-grow: 1;
        }

        .user-icon {
            font-size: 25px;
            color: #002D44;
        }

        .user-name {
            font-size: 12px;
            color: #000000;
        }

        .user-role {
            font-size: 11px;
            color: #0C517C;
        }

        .logout-button {
            font-size: 24px;
            color: #02476A;
            cursor: pointer;
            transition: color 0.3s;
        }

        .logout-button:hover {
            color: white;
            background-color: #02476A;
        }

        h2 {
            font-size: 12px;
            color: #02476A;
            font-weight: 500;
            margin: 40px 0 5px 10px;
        }
        
        .active-link {
            background-color: #4597C0;
            color: #fff;
            border-radius: 0 25px 25px 0;
            padding: 12px 20px;
            width: 100%;
            text-decoration: none;
            position: absolute;
        }

        .active-link .material-symbols-rounded {
            vertical-align: middle; 
        }
        

        /* Responsive  */
        @media (max-width: 992px) {
            .sidebar {
                display: none;
            }

            .top-bar {
                display: flex;
                position: fixed;
                top: 0;
                width: 100%;
                height: 50px;
                background-color: #E8F3F8;
                color: white;
                justify-content: space-around;
                align-items: center;
                z-index: 2;
            }

            .main-content {
                margin-left: 0;
                padding-top: 50px;
                margin-top: 50px
            }
        }

        @media (max-width: 575px) {
            .top-bar .icon-text {
                display: none;
            }
        }
    </style>
</head>

<body>
<?php
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest User';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Unknown Role';
?>
       <!-- Top bar  -->
       <div class="top-bar d-md-none">
        <a href="admin_dashboard.php" class="nav-link">
            <span class="material-symbols-rounded">dashboard</span>
        </a>
        <a href="activitylog.php" class="nav-link">
            <span class="material-symbols-rounded">article</span>
        </a>
        <a href="adminlivecam.php" class="nav-link">
            <span class="material-symbols-rounded">videocam</span>
        </a>
        <a href="add_user.php" class="nav-link">
            <span class="material-symbols-rounded">manage_accounts</span>
        </a>
        <a href="workforce_manager.php" class="nav-link">
            <span class="material-symbols-rounded">history</span>
        </a>
        <a href="admin_profile.php" class="nav-link">
            <span class="material-symbols-rounded">account_circle</span>
        </a>
        <span class="material-symbols-rounded logout-button" onclick="window.location.href='logout.php';">
    chevron_right
</span>

      </div>

    <!-- Sidebar -->
    <nav class="sidebar d-none d-md-flex flex-column p-0">
        <div class="sidebar-logo">
            <img src="./images/Floodpinglogo.png" alt="floodping">
            <div class="sidebar-logo-text" style="font-size: 16px; font-weight: 600;">FLOODPING</div>
        </div>

        <div class="time-section">
            <div class="time" id="current-time"></div>
            <div class="date" id="current-date"></div>
            <div class="station">DARIUS STATION</div>
        </div>

        <div class="sidebar-content">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="admin_dashboard.php" class="nav-link">
                        <span class="material-symbols-rounded">dashboard</span> <span class="icon-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="activitylog.php" class="active-link">
                        <span class="material-symbols-rounded">article</span> <span class="icon-text">Activity Logs</span>
                    </a>
                </li>

                <div class="section-title" style="margin-top: 60px;">Flood Monitoring</div>
                <li class="nav-item">
                    <a href="adminlivecam.php" class="nav-link">
                        <span class="material-symbols-rounded">videocam</span> <span class="icon-text">Live Camera Feed</span>
                    </a>
                </li>

                <div class="section-title">User Management</div>
                <li class="nav-item">
                    <a href="add_user.php" class="nav-link">
                        <span class="material-symbols-rounded">manage_accounts</span> <span class="icon-text">Account Services</span>
                    </a>
                </li>

                <div class="section-title">Workforce Manager</div>
                <li class="nav-item">
                    <a href="workforce_manager.php" class="nav-link">
                        <span class="material-symbols-rounded">history</span> <span class="icon-text">Shift Management</span>
                    </a>
                </li>
            </ul>
        </div>
        <h2>Logged in as:</h2>
<div class="user-info-container">
    <a href="admin_profile.php" class="user-link">
        <span class="material-symbols-rounded user-icon">account_circle</span>
        <div>
            <div class="user-name"><?= htmlspecialchars($fullName); ?></div>
            <div class="user-role"><?= htmlspecialchars($role); ?></div>
        </div>
    </a>
    <span class="material-symbols-rounded logout-button" onclick="window.location.href='logout.php';">
    chevron_right
</span>

</div>


    </nav>

    <!-- content area -->
    <main class="main-content">
        
    </main>

    <script>
    function updateTimeAndDate() {
        const now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        document.getElementById('current-time').textContent = `${hours}:${minutes} ${ampm}`;

        // Format date
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const formattedDate = now.toLocaleDateString(undefined, options);
        const [weekday, month, day, year] = formattedDate.match(/(\w+), (\w+) (\d+), (\d+)/).slice(1);

        document.getElementById('current-date').innerHTML = `${weekday}&nbsp;&nbsp;|&nbsp;&nbsp;${month} ${day}, ${year}`;
    }

    setInterval(updateTimeAndDate, 1000);
    updateTimeAndDate();
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
