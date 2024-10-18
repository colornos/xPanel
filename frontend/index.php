<?php
// Check if this is an AJAX request for live stats
if (isset($_GET['action']) && $_GET['action'] == 'get_stats') {
    // Execute the bash script to get system stats
    $system_stats = shell_exec('sudo /var/www/html/xpanel/get_system_stats.sh');
    $stats = json_decode($system_stats, true);

    // Prepare additional system stats
    $current_user = trim(shell_exec('whoami'));
    $home_directory = trim(shell_exec('echo ~' . $current_user));
    $last_login_info = shell_exec("last -n 1 $current_user | awk '{print $3}'");
    $last_login_ip = trim($last_login_info);
    $primary_domain = trim(shell_exec("hostname -I | awk '{print $1}'"));

    $data = [
        'cpu_usage' => (float) $stats['cpu_usage'],
        'gpu_usage' => $stats['gpu_usage'] ?? 'N/A',
        'cpu_temp' => $stats['cpu_temp'] ?? 'N/A',
        'mem_total' => (int) $stats['mem_total'] / 1024, // Convert to MB
        'mem_used' => (int) $stats['mem_used'] / 1024, // Convert to MB
        'mem_usage' => (float) $stats['mem_usage'], // Memory usage percentage
        'disk_used' => trim($stats['disk_usage'], '%'), // Disk usage as percentage
        'rx_mb' => (float) $stats['rx_mb'], // Network received (MB)
        'tx_mb' => (float) $stats['tx_mb'], // Network transmitted (MB)
        'current_user' => $current_user,
        'primary_domain' => $primary_domain,
        'home_directory' => $home_directory,
        'last_login_ip' => $last_login_ip,
        'block_devices' => $stats['block_devices'],
        'sys_logs' => $stats['sys_logs']
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

    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

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
        }
        .icons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            padding: 15px;
        }
        .icon {
            text-align: center;
        }
        .icon i {
            font-size: 50px;
            color: #0056A4;
            margin-bottom: 5px;
        }
        .icon div {
            font-size: 14px;
            font-weight: 500;
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
        }
        .progress-container {
            margin-bottom: 20px;
        }
        .progress-label {
            font-size: 12px;
            margin-bottom: 5px;
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
        .stat-label {
            font-weight: bold;
            color: #0056A4;
        }
        .stat-value {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">xPanel</div>
    </header>

    <div class="container">
        <!-- Main Content -->
        <div class="main-content">
            <!-- Files Section -->
            <div class="section">
                <div class="section-header">Files</div>
                <div class="icons-grid">
                    <div class="icon">
                        <i class="fas fa-folder"></i>
                        <div><a href="file_manager.php">File Manager</a></div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-image"></i>
                        <div>Images</div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                        <div>FTP Accounts</div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-network-wired"></i>
                        <div>FTP Connections</div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-lock"></i>
                        <div>Directory Privacy</div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hdd"></i>
                        <div>Disk Usage</div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-database"></i>
                        <div>Backup</div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-magic"></i>
                        <div>Backup Wizard</div>
                    </div>
                </div>
            </div>

            <!-- Databases Section -->
            <div class="section">
                <div class="section-header">Databases</div>
                <div class="icons-grid">
                    <div class="icon">
                        <i class="fas fa-database"></i>
                        <div><a href="/phpmyadmin" target="_blank">phpMyAdmin</a></div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-server"></i>
                        <div><a href="database.php">MySQL Databases</a></div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-database"></i>
                        <div><a href="database.php">MySQL Database Wizard</a></div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cloud"></i>
                        <div><a href="database.php">Remote MySQL</a></div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-server"></i>
                        <div>PostgreSQL Databases</div>
                    </div>
                </div>
            </div>

            <!-- Terminal Section -->
            <div class="section">
                <div class="section-header">Terminal</div>
                <div class="icons-grid">
                    <div class="icon">
                        <i class="fas fa-terminal"></i>
                        <div><a href="http://<?php echo $primary_domain; ?>:3389" target="_blank">Live Terminal (RDP)</a></div>
                    </div>
                </div>
            </div>
        </div>

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

            <!-- CPU Usage -->
            <div class="progress-container">
                <div class="progress-label">CPU Usage</div>
                <div class="stat-value">
                    <strong><span id="cpu_usage_value"><?php echo $stats['cpu_usage']; ?></span>%</strong>
                </div>
                <div class="progress-bar">
                    <span class="cpu-load" id="cpu_usage" style="width: <?php echo $stats['cpu_usage']; ?>%;"></span>
                </div>
            </div>

            <!-- GPU Usage -->
            <div class="stat">
                <div class="stat-label">GPU Usage:</div>
                <div class="stat-value">
                    <strong><span id="gpu_usage_value"><?php echo $stats['gpu_usage']; ?></span></strong>
                </div>
            </div>

            <!-- CPU Temperature -->
            <div class="stat">
                <div class="stat-label">CPU Temperature:</div>
                <div class="stat-value">
                    <strong><span id="cpu_temp_value"><?php echo $stats['cpu_temp']; ?></span></strong>
                </div>
            </div>

            <!-- Disk Usage -->
            <div class="progress-container">
                <div class="progress-label">Disk Usage</div>
                <div class="stat-value">
                    <span id="disk_usage_value"><?php echo $stats['disk_used']; ?>%</span> used
                </div>
                <div class="progress-bar">
                    <span class="disk-usage" id="disk_usage" style="width: <?php echo $stats['disk_used']; ?>%;"></span>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="progress-container">
                <div class="progress-label">Memory Usage</div>
                <div class="stat-value">
                    <?php echo round($stats['mem_used'], 2) . ' MB / ' . round($stats['mem_total'], 2) . ' MB'; ?>
                    <br>
                    <strong><span id="mem_usage_value"><?php echo $stats['mem_usage']; ?></span>%</strong>
                </div>
                <div class="progress-bar">
                    <span class="mem-usage" id="mem_usage" style="width: <?php echo $stats['mem_usage']; ?>%;"></span>
                </div>
            </div>

            <!-- Network Traffic (Received) -->
            <div class="progress-container">
                <div class="progress-label">Network Received (MB)</div>
                <div class="stat-value">
                    <strong><span id="rx_mb_value"><?php echo $stats['rx_mb']; ?></span> MB</strong>
                </div>
                <div class="progress-bar">
                    <span class="network-traffic" id="rx_mb" style="width: <?php echo $stats['rx_mb'] / 10; ?>%;"></span>
                </div>
            </div>

            <!-- Network Traffic (Transmitted) -->
            <div class="progress-container">
                <div class="progress-label">Network Transmitted (MB)</div>
                <div class="stat-value">
                    <strong><span id="tx_mb_value"><?php echo $stats['tx_mb']; ?></span> MB</strong>
                </div>
                <div class="progress-bar">
                    <span class="network-traffic" id="tx_mb" style="width: <?php echo $stats['tx_mb'] / 10; ?>%;"></span>
                </div>
            </div>

            <!-- Block Devices -->
            <div class="stat">
                <div class="stat-label">Block Devices:</div>
                <div class="stat-value">
                    <pre id="block_devices_value"><?php echo $stats['block_devices']; ?></pre>
                </div>
            </div>

            <!-- System Logs -->
            <div class="stat">
                <div class="stat-label">System Logs:</div>
                <div class="stat-value">
                    <pre id="sys_logs_value"><?php echo $stats['sys_logs']; ?></pre>
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
                    // Update CPU and GPU usage
                    document.getElementById('cpu_usage_value').textContent = data.cpu_usage + '%';
                    document.getElementById('cpu_usage').style.width = data.cpu_usage + '%';
                    document.getElementById('gpu_usage_value').textContent = data.gpu_usage;

                    // Update CPU temperature
                    document.getElementById('cpu_temp_value').textContent = data.cpu_temp;

                    // Update block devices
                    document.getElementById('block_devices_value').textContent = data.block_devices;

                    // Update system logs
                    document.getElementById('sys_logs_value').textContent = data.sys_logs;

                    // Existing updates for memory, disk, and network stats
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
