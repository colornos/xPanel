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
        <div class="card"><a href="/phpmyadmin" target="_blank">phpMyAdmin</a></div> <!-- Link to phpMyAdmin -->
    </div>

    <!-- System Info Section -->
<?php
// Get CPU load using sudo
$cpu_load = sys_getloadavg();

// Get Memory usage using sudo
$mem_info = shell_exec('sudo cat /proc/meminfo');
preg_match("/MemTotal:\s+(\d+) kB/", $mem_info, $matches);
$mem_total = $matches[1];
preg_match("/MemFree:\s+(\d+) kB/", $mem_info, $matches);
$mem_free = $matches[1];
$mem_used = $mem_total - $mem_free;
$mem_usage = round(($mem_used / $mem_total) * 100, 2);

// Get Disk usage (using PHP built-in functions)
$disk_total = disk_total_space("/");
$disk_free = disk_free_space("/");
$disk_used = $disk_total - $disk_free;
$disk_usage = round(($disk_used / $disk_total) * 100, 2);

// Display the information
?>
<div class="stats">
    <h2>CPU Load</h2>
    <p>1 Minute: <?php echo $cpu_load[0]; ?></p>
    <p>5 Minutes: <?php echo $cpu_load[1]; ?></p>
    <p>15 Minutes: <?php echo $cpu_load[2]; ?></p>
</div>

<div class="stats">
    <h2>Memory Usage</h2>
    <p>Total: <?php echo round($mem_total / 1024); ?> MB</p>
    <p>Used: <?php echo round($mem_used / 1024); ?> MB</p>
    <p>Usage: <?php echo $mem_usage; ?>%</p>
</div>

<div class="stats">
    <h2>Disk Usage</h2>
    <p>Total: <?php echo round($disk_total / 1024 / 1024 / 1024, 2); ?> GB</p>
    <p>Used: <?php echo round($disk_used / 1024 / 1024 / 1024, 2); ?> GB</p>
    <p>Usage: <?php echo $disk_usage; ?>%</p>
</div>

</body>
</html>
