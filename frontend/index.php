<?php
// Execute the bash script to get system stats
$system_stats = shell_exec('sudo /var/www/html/xpanel/get_system_stats.sh');
$stats = json_decode($system_stats, true);

// Extract the data
$cpu_load = $stats['cpu_load'];
$mem_total = round($stats['mem_total'] / 1024, 2); // Convert to MB
$mem_used = round($stats['mem_used'] / 1024, 2); // Convert to MB
$mem_usage = round($stats['mem_usage'], 2);
$disk_usage = $stats['disk_usage'];
$rx_mb = round($stats['rx_mb'], 2);
$tx_mb = round($stats['tx_mb'], 2);
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
        <p><?php echo $cpu_load; ?></p>
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
