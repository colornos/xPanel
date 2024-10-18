<?php

// Get CPU Load using 'uptime' command
$cpu_load = shell_exec('uptime');
preg_match('/load average: ([0-9.]+), ([0-9.]+), ([0-9.]+)/', $cpu_load, $loadavg_matches);
$loadavg_1min = $loadavg_matches[1];
$loadavg_5min = $loadavg_matches[2];
$loadavg_15min = $loadavg_matches[3];

// Get Memory Usage using 'free' command
$memory_info = shell_exec('free -m');
preg_match('/Mem:\s+(\d+)\s+(\d+)/', $memory_info, $mem_matches);
$mem_total = $mem_matches[1];
$mem_used = $mem_matches[2];
$mem_usage = round(($mem_used / $mem_total) * 100, 2);

// Get Disk Usage using 'df' command
$disk_info = shell_exec('df -h /');
preg_match('/\d+%\s+/', $disk_info, $disk_matches);
$disk_usage = trim($disk_matches[0]);

// Get Network Traffic (RX and TX) from 'ifconfig' or 'ip' command
$rx_bytes = shell_exec("cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/statistics/rx_bytes");
$tx_bytes = shell_exec("cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/statistics/tx_bytes");

// Convert bytes to MB
$rx_mb = round($rx_bytes / 1024 / 1024, 2);
$tx_mb = round($tx_bytes / 1024 / 1024, 2);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>xPanel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        .container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .card {
            background: #fff;
            margin: 10px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 200px;
            text-align: center;
        }

        a {
            text-decoration: none;
            color: #007bff;
            font-size: 18px;
        }

        .stats {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 500px;
            margin: 20px auto;
        }

        .stats h2 {
            color: #007bff;
        }
    </style>
</head>
<body>
    <h1>xPanel</h1>

    <div class="container">
        <div class="card"><a href="file_manager.php">File Manager</a></div>
        <div class="card"><a href="database.php">Database Management</a></div>
        <div class="card"><a href="server_management.php">Server Management</a></div>
        <div class="card"><a href="domain_management.php">Domain Management</a></div>
        <div class="card"><a href="/phpmyadmin" target="_blank">phpMyAdmin</a></div>
    </div>

    <!-- System Info Section -->
    <div class="stats">
        <h2>CPU Load</h2>
        <p>1 Minute: <?php echo $loadavg_1min; ?></p>
        <p>5 Minutes: <?php echo $loadavg_5min; ?></p>
        <p>15 Minutes: <?php echo $loadavg_15min; ?></p>
    </div>

    <div class="stats">
        <h2>Memory Usage</h2>
        <p>Total: <?php echo $mem_total; ?> MB</p>
        <p>Used: <?php echo $mem_used; ?> MB</p>
        <p>Usage: <?php echo $mem_usage; ?>%</p>
    </div>

    <div class="stats">
        <h2>Disk Usage</h2>
        <p>Usage: <?php echo $disk_usage; ?></p>
    </div>

    <div class="stats">
        <h2>Network Traffic</h2>
        <p>Received: <?php echo $rx_mb; ?> MB</p>
        <p>Transmitted: <?php echo $tx_mb; ?> MB</p>
    </div>

</body>
</html>
