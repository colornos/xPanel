<?php
// Check if this is an AJAX request for live stats
if (isset($_GET['action']) && $_GET['action'] == 'get_stats') {
    // Execute the bash script to get system stats
    $system_stats = shell_exec('sudo /var/www/html/xpanel/get_system_stats.sh');
    $stats = json_decode($system_stats, true);

    // Prepare system stats
    $current_user = trim(shell_exec('whoami'));
    $home_directory = trim(shell_exec('echo ~' . $current_user));
    $last_login_info = shell_exec("last -n 1 $current_user | awk '{print $3}'");
    $last_login_ip = trim($last_login_info);
    $primary_domain = trim(shell_exec("hostname -I | awk '{print $1}'"));

    $data = [
        'cpu_load' => (float) trim(explode(' ', $stats['cpu_load'])[0]),
        'mem_total' => (int) $stats['mem_total'] / 1024, // Convert to MB
        'mem_used' => (int) $stats['mem_used'] / 1024, // Convert to MB
        'mem_usage' => (float) $stats['mem_usage'], // Memory usage percentage
        'disk_used' => (float) trim($stats['disk_usage'], '%'), // Disk usage as percentage
        'rx_mb' => (float) $stats['rx_mb'], // Network received (MB)
        'tx_mb' => (float) $stats['tx_mb'], // Network transmitted (MB)
        'current_user' => $current_user,
        'primary_domain' => $primary_domain,
        'home_directory' => $home_directory,
        'last_login_ip' => $last_login_ip
    ];

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Fetch data for the initial page load
$system_stats = shell_exec('sudo /var/www/html/xpanel/get_system_stats.sh');
$stats = json_decode($system_stats, true);
$current_user = trim(shell_exec('whoami'));
$home_directory = trim(shell_exec('echo ~' . $current_user));
$last_login_info = shell_exec("last -n 1 $current_user | awk '{print $3}'");
$last_login_ip = trim($last_login_info);
$primary_domain = trim(shell_exec("hostname -I | awk '{print $1}'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>xPanel Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #2a3f54;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header .logo {
            font-size: 26px;
            font-weight: bold;
            font-family: 'Arial', sans-serif;
        }
        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }
        .main-content {
            flex: 3;
            margin-right: 20px;
        }
        .section {
            background-color: #fff;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .section-header {
            background-color: #0056A4;
            color: #fff;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .sidebar {
            flex: 1;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar-header {
            background-color: #0056A4;
            color: #fff;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .progress-container {
            margin-bottom: 20px;
        }
        .progress-label {
            font-size: 12px;
            margin-bottom: 5px;
            color: #333;
        }
        .progress-bar {
            height: 20px;
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 10px;
            position: relative;
        }
        .progress-bar span {
            display: block;
            height: 100%;
            border-radius: 10px;
            position: absolute;
            left: 0;
            top: 0;
        }
        .cpu-load {
            background-color: #f39c12;
        }
        .mem-usage {
            background-color: #3498db;
        }
        .disk-usage {
            background-color: #FF6C6C;
        }
        .network-traffic {
            background-color: #27ae60;
        }
        /* Labels for side info */
        .stat-label {
            font-weight: bold;
            color: #0056A4;
        }
        .stat-value {
            margin-bottom: 15px;
        }
        /* Adding values next to the progress bars */
        .progress-number {
            position: absolute;
            right: 10px;
            top: 0;
            font-size: 12px;
            color: #000;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">xPanel</div>
    </header>

    <div class="container">
        <!-- Sidebar with Live Statistics -->
        <div class="sidebar">
            <div class="sidebar-header">General Information</div>
            <div class="stat">
                <div class="stat-label">Current User:</div>
                <div class="stat-value" id="current_user"><?php echo $current_user; ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Primary Domain (Server IP):</div>
                <div class="stat-value" id="primary_domain"><?php echo $primary_domain; ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Home Directory:</div>
                <div class="stat-value" id="home_directory"><?php echo $home_directory; ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Last Login IP:</div>
                <div class="stat-value" id="last_login_ip"><?php echo $last_login_ip; ?></div>
            </div>

            <div class="sidebar-header">Live Statistics</div>

            <!-- Disk Usage -->
            <div class="progress-container">
                <div class="progress-label">Disk Usage</div>
                <div class="stat-value">
                    <?php echo $stats['disk_used']; ?>% used
                    <br>
                    <strong><?php echo $stats['disk_used']; ?>%</strong> 
                    <!-- Adjust this based on actual available/used data -->
                </div>
                <div class="progress-bar">
                    <span class="disk-usage" id="disk_usage" style="width: <?php echo $stats['disk_usage']; ?>%;"></span>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="progress-container">
                <div class="progress-label">Memory Usage</div>
                <div class="stat-value">
                    <?php echo round($stats['mem_used'], 2) . ' MB / ' . round($stats['mem_total'], 2) . ' MB'; ?>
                    <br>
                    <strong><?php echo $stats['mem_usage']; ?>%</strong>
                </div>
                <div class="progress-bar">
                    <span class="mem-usage" id="mem_usage" style="width: <?php echo $stats['mem_usage']; ?>%;"></span>
                </div>
            </div>

            <!-- Network Traffic (Received) -->
            <div class="progress-container">
                <div class="progress-label">Network Received (MB)</div>
                <div class="stat-value">
                    <strong><?php echo $stats['rx_mb']; ?> MB</strong>
                </div>
                <div class="progress-bar">
                    <span class="network-traffic" id="rx_mb" style="width: <?php echo $stats['rx_mb'] / 10; ?>%;"></span>
                </div>
            </div>

            <!-- Network Traffic (Transmitted) -->
            <div class="progress-container">
                <div class="progress-label">Network Transmitted (MB)</div>
                <div class="stat-value">
                    <strong><?php echo $stats['tx_mb']; ?> MB</strong>
                </div>
                <div class="progress-bar">
                    <span class="network-traffic" id="tx_mb" style="width: <?php echo $stats['tx_mb'] / 10; ?>%;"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to fetch and update stats dynamically -->
    <script>
        function updateStats() {
            fetch('?action=get_stats')
                .then(response => response.json())
                .then(data => {
                    // Update live stats
                    document.getElementById('current_user').textContent = data.current_user;
                    document.getElementById('primary_domain').textContent = data.primary_domain;
                    document.getElementById('home_directory').textContent = data.home_directory;
                    document.getElementById('last_login_ip').textContent = data.last_login_ip;

                    // Update progress bars and values
                    document.getElementById('disk_usage').style.width = data.disk_used + '%';
                    document.getElementById('mem_usage').style.width = data.mem_usage + '%';
                    document.getElementById('rx_mb').style.width = (data.rx_mb / 10) + '%';
                    document.getElementById('tx_mb').style.width = (data.tx_mb / 10) + '%';

                    // Update number values
                    document.getElementById('disk_usage_value').textContent = data.disk_used + '%';
                    document.getElementById('mem_usage_value').textContent = data.mem_usage.toFixed(2) + '%';
                    document.getElementById('rx_mb_value').textContent = data.rx_mb.toFixed(2) + 'MB';
                    document.getElementById('tx_mb_value').textContent = data.tx_mb.toFixed(2) + 'MB';
                })
                .catch(error => console.error('Error fetching stats:', error));
        }

        // Fetch stats every 5 seconds
        setInterval(updateStats, 5000);
    </script>
</body>
</html>
