<?php
// Start the session to access session variables
session_start();

// Check if this is an AJAX request for live stats
if (isset($_GET['action']) && $_GET['action'] == 'get_stats') {
    // Execute the bash script to get system stats
    $system_stats = shell_exec('sudo /var/www/html/xpanel/get_system_stats.sh');
    $stats = json_decode($system_stats, true);

    // Output the JSON response for the frontend
    header('Content-Type: application/json');
    echo json_encode($stats);
    exit;
}

// Fetch data for the initial page load
$system_stats = shell_exec('sudo /var/www/html/xpanel/get_system_stats.sh');
$stats = json_decode($system_stats, true);
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="xPanel dashboard.">
    <meta name="keywords" content="Control Panel">
    <meta name="author" content="Colornos">
    <title>xPanel</title>
    <link rel="stylesheet" type="text/css" href="app-assets/css/material.css">
    <style>
        .sidebar {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
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
        .cpu-load { background-color: #f39c12; }
        .mem-usage { background-color: #3498db; }
        .disk-usage { background-color: #FF6C6C; }
        .network-traffic { background-color: #27ae60; }
    </style>
</head>

<body>
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="container">
                <!-- Main Content -->
                <div class="main-content">
                    <!-- Files Section -->
                    <div class="section">
                        <div class="header-style">Files</div>
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
                        </div>
                    </div>

                    <!-- Databases Section -->
                    <div class="section">
                        <div class="header-style">Databases</div>
                        <div class="icons-grid">
                            <div class="icon">
                                <i class="fas fa-database"></i>
                                <div><a href="/phpmyadmin" target="_blank">phpMyAdmin</a></div>
                            </div>
                            <div class="icon">
                                <i class="fas fa-server"></i>
                                <div><a href="database.php">MySQL Databases</a></div>
                            </div>
                        </div>
                    </div>

                    <!-- Terminal Section -->
                    <div class="section">
                        <div class="header-style">Terminal</div>
                        <div class="icons-grid">
                            <div class="icon">
                                <i class="fas fa-terminal"></i>
                                <div><a href="https://<?php echo $primary_domain; ?>:4200" target="_blank">Terminal</a></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar with Live Statistics -->
                <div class="sidebar">
                    <div class="header-style">General Information</div>
                    <div class="stat">
                        <div class="stat-label">Primary Domain (Server IP):</div>
                        <div class="stat-value" id="primary_domain"><b><?php echo trim(shell_exec("hostname -I | awk '{print $1}'")); ?></b></div>
                    </div>

                    <div class="header-style">Live Statistics</div>

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
                            <span id="disk_usage_value"><?php echo $stats['disk_usage']; ?></span> used
                        </div>
                        <div class="progress-bar">
                            <span class="disk-usage" id="disk_usage" style="width: <?php echo $stats['disk_usage']; ?>;"></span>
                        </div>
                    </div>

                    <!-- Memory Usage -->
                    <div class="progress-container">
                        <div class="progress-label">Memory Usage</div>
                        <div class="stat-value">
                            <strong><span id="mem_usage_value"><?php echo round($stats['mem_usage'], 2); ?></span>%</strong>
                        </div>
                        <div class="progress-bar">
                            <span class="mem-usage" id="mem_usage" style="width: <?php echo round($stats['mem_usage'], 2); ?>%;"></span>
                        </div>
                    </div>

                    <!-- Network Traffic -->
                    <div class="progress-container">
                        <div class="progress-label">Network Received (MB)</div>
                        <div class="stat-value">
                            <strong><span id="rx_mb_value"><?php echo round($stats['rx_mb'], 2); ?></span> MB</strong>
                        </div>
                    </div>
                    <div class="progress-container">
                        <div class="progress-label">Network Transmitted (MB)</div>
                        <div class="stat-value">
                            <strong><span id="tx_mb_value"><?php echo round($stats['tx_mb'], 2); ?></span> MB</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- Dynamic Stats Fetching -->
    <script>
        function updateStats() {
            fetch('?action=get_stats')
                .then(response => response.json())
                .then(data => {
                    // Update stats dynamically
                    document.getElementById('cpu_usage_value').textContent = data.cpu_usage + '%';
                    document.getElementById('cpu_usage').style.width = data.cpu_usage + '%';

                    document.getElementById('gpu_usage_value').textContent = data.gpu_usage;

                    document.getElementById('cpu_temp_value').textContent = data.cpu_temp;

                    document.getElementById('disk_usage_value').textContent = data.disk_usage;
                    document.getElementById('disk_usage').style.width = data.disk_usage;

                    document.getElementById('mem_usage_value').textContent = data.mem_usage.toFixed(2) + '%';
                    document.getElementById('mem_usage').style.width = data.mem_usage + '%';

                    document.getElementById('rx_mb_value').textContent = data.rx_mb.toFixed(2) + ' MB';
                    document.getElementById('tx_mb_value').textContent = data.tx_mb.toFixed(2) + ' MB';
                })
                .catch(error => console.error('Error fetching stats:', error));
        }

        // Fetch stats every 5 seconds
        setInterval(updateStats, 5000);
    </script>
</body>
</html>
