<?php
// Check if this is an AJAX request for live stats
if (isset($_GET['action']) && $_GET['action'] == 'get_stats') {
    // Execute the bash script to get system stats
    $system_stats = shell_exec('sudo /var/www/html/xpanel/get_system_stats.sh');
    $stats = json_decode($system_stats, true);

    // Prepare system stats
    $current_user = get_current_user();
    $home_directory = getenv('HOME');
    $last_login_info = shell_exec("last -n 1 $current_user | awk '{print $3}'");
    $last_login_ip = trim($last_login_info);
    $primary_domain = trim(shell_exec("hostname -I | awk '{print $1}'"));

    $data = [
        'cpu_load_1' => (float) $stats['cpu_load'],
        'cpu_load_5' => (float) $stats['cpu_load'],
        'cpu_load_15' => (float) $stats['cpu_load'],
        'mem_total' => (int) $stats['mem_total'] / 1024, // Convert to MB
        'mem_used' => (int) $stats['mem_used'] / 1024, // Convert to MB
        'disk_total' => 100, // Placeholder for total disk space
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
$current_user = get_current_user();
$home_directory = getenv('HOME');
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
        .icons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            padding: 15px;
        }
        .icon {
            text-align: center;
        }
        .icon img {
            width: 50px;
            height: 50px;
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
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .sidebar .stat {
            margin-bottom: 15px;
        }
        .stat-label {
            font-weight: bold;
            color: #0056A4;
        }
        .stat-value {
            color: #333;
            margin-top: 5px;
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
        .disk-usage {
            width: <?php echo $stats['disk_used']; ?>%;
            background-color: #FF6C6C;
        }
        .mysql-usage {
            width: <?php echo 30; ?>%;
            background-color: #4CAF50;
        }
        .progress-label .edit-icon {
            float: right;
            color: #0056A4;
            font-size: 14px;
            cursor: pointer;
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
                        <img src="https://via.placeholder.com/50" alt="File Manager">
                        <div>File Manager</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="Images">
                        <div>Images</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="FTP Accounts">
                        <div>FTP Accounts</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="FTP Connections">
                        <div>FTP Connections</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="Directory Privacy">
                        <div>Directory Privacy</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="Disk Usage">
                        <div>Disk Usage</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="Backup">
                        <div>Backup</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="Backup Wizard">
                        <div>Backup Wizard</div>
                    </div>
                </div>
            </div>

            <!-- Databases Section -->
            <div class="section">
                <div class="section-header">Databases</div>
                <div class="icons-grid">
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="phpMyAdmin">
                        <div>phpMyAdmin</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="MySQL Databases">
                        <div>MySQL Databases</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="MySQL Database Wizard">
                        <div>MySQL Database Wizard</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="Remote MySQL">
                        <div>Remote MySQL</div>
                    </div>
                    <div class="icon">
                        <img src="https://via.placeholder.com/50" alt="PostgreSQL Databases">
                        <div>PostgreSQL Databases</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">General Information</div>
            <div class="stat">
                <div class="stat-label">Current User:</div>
                <div class="stat-value" data-key="current_user"><?php echo $current_user; ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Primary Domain (Server IP):</div>
                <div class="stat-value" data-key="primary_domain"><?php echo $primary_domain; ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Home Directory:</div>
                <div class="stat-value" data-key="home_directory"><?php echo $home_directory; ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Last Login IP:</div>
                <div class="stat-value" data-key="last_login_ip"><?php echo $last_login_ip; ?></div>
            </div>

            <div class="sidebar-header">Statistics</div>
            <!-- CPU Load -->
            <div class="stat">
                <div class="stat-label">CPU Load (1 min):</div>
                <div class="stat-value" data-key="cpu_load_1"><?php echo $stats['cpu_load']; ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">CPU Load (5 min):</div>
                <div class="stat-value" data-key="cpu_load_5">0.0</div>
            </div>
            <div class="stat">
                <div class="stat-label">CPU Load (15 min):</div>
                <div class="stat-value" data-key="cpu_load_15">0.0</div>
            </div>

            <!-- Memory Usage -->
            <div class="stat">
                <div class="stat-label">Memory Total (MB):</div>
                <div class="stat-value" data-key="mem_total"><?php echo round($stats['mem_total']); ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Memory Used (MB):</div>
                <div class="stat-value" data-key="mem_used"><?php echo round($stats['mem_used']); ?></div>
            </div>

            <!-- Disk Usage Progress Bar -->
            <div class="progress-container">
                <div class="stat-title">Disk Usage</div>
                <div class="progress-label">
                    <?php echo $stats['disk_used']; ?>% used
                </div>
                <div class="progress-bar">
                    <span class="disk-usage"></span>
                </div>
            </div>

            <!-- Network Traffic -->
            <div class="stat">
                <div class="stat-label">Network Received (MB):</div>
                <div class="stat-value" data-key="rx_mb"><?php echo $stats['rx_mb']; ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Network Transmitted (MB):</div>
                <div class="stat-value" data-key="tx_mb"><?php echo $stats['tx_mb']; ?></div>
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
                    document.querySelector('.stat-value[data-key="current_user"]').textContent = data.current_user;
                    document.querySelector('.stat-value[data-key="primary_domain"]').textContent = data.primary_domain;
                    document.querySelector('.stat-value[data-key="home_directory"]').textContent = data.home_directory;
                    document.querySelector('.stat-value[data-key="last_login_ip"]').textContent = data.last_login_ip;

                    // Update CPU load
                    document.querySelector('.stat-value[data-key="cpu_load_1"]').textContent = data.cpu_load_1;
                    document.querySelector('.stat-value[data-key="cpu_load_5"]').textContent = data.cpu_load_5;
                    document.querySelector('.stat-value[data-key="cpu_load_15"]').textContent = data.cpu_load_15;

                    // Update Memory
                    document.querySelector('.stat-value[data-key="mem_total"]').textContent = data.mem_total;
                    document.querySelector('.stat-value[data-key="mem_used"]').textContent = data.mem_used;

                    // Update Disk usage
                    document.querySelector('.disk-usage').style.width = data.disk_used + '%';

                    // Update Network
                    document.querySelector('.stat-value[data-key="rx_mb"]').textContent = data.rx_mb;
                    document.querySelector('.stat-value[data-key="tx_mb"]').textContent = data.tx_mb;
                })
                .catch(error => console.error('Error fetching stats:', error));
        }

        // Fetch stats every 5 seconds
        setInterval(updateStats, 5000);
    </script>
</body>
</html>
