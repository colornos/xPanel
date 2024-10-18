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
        'disk_total' => 100, // Placeholder
        'disk_used' => (float) trim($stats['disk_usage'], '%'), // Disk usage as percentage
        'rx_mb' => (float) $stats['rx_mb'],
        'tx_mb' => (float) $stats['tx_mb'],
        'current_user' => $current_user,
        'primary_domain' => $primary_domain,
        'home_directory' => $home_directory,
        'last_login_ip' => $last_login_ip
    ];

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

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
    <title>xPanel - Control Panel</title>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Styling for cPanel-like layout -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }

        /* Top Navigation Bar */
        .top-bar {
            background-color: #1a202c;
            padding: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar input[type="search"] {
            padding: 7px;
            border-radius: 5px;
            border: none;
        }

        .top-bar .user-info {
            display: flex;
            align-items: center;
        }

        .top-bar .user-info button {
            background-color: #e53e3e;
            color: white;
            border: none;
            padding: 7px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Layout */
        .container {
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            background-color: #2d3748;
            height: 100vh;
            color: white;
            padding: 20px;
        }

        .sidebar h3 {
            color: #a0aec0;
            margin-bottom: 10px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 0;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background-color: #4a5568;
            padding-left: 10px;
            transition: 0.3s;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
        }

        /* Right Sidebar (Line Graph + Stats) */
        .right-sidebar {
            width: 300px;
            background-color: #f7fafc;
            padding: 20px;
            border-left: 1px solid #e2e8f0;
        }

        .chart-container {
            width: 100%;
            margin-bottom: 20px;
        }

        /* Navigation Links */
        .nav-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card img {
            max-width: 50px;
            margin-bottom: 10px;
        }

        .card a {
            text-decoration: none;
            color: #1a202c;
        }

        .card a:hover {
            color: #2b6cb0;
        }

        /* General Information Section */
        .right-sidebar p {
            margin: 5px 0;
        }

        .stats-item {
            margin-bottom: 10px;
        }

        .stats-item strong {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <input type="search" placeholder="Find functions quickly by typing here..." />
        <div class="user-info">
            <span><?php echo $current_user; ?></span>
            <button>Logout</button>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>FILES</h3>
            <a href="file_manager.php">File Manager</a>
            <a href="#">Disk Usage</a>
            <a href="#">FTP Connections</a>
            <a href="#">Backup</a>

            <h3>DATABASES</h3>
            <a href="database.php">Database Management</a>
            <a href="/phpmyadmin" target="_blank">phpMyAdmin</a>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Navigation Links -->
            <div class="nav-links">
                <div class="card"><a href="file_manager.php">File Manager</a></div>
                <div class="card"><a href="database.php">Database Management</a></div>
                <div class="card"><a href="server_management.php">Server Management</a></div>
                <div class="card"><a href="domain_management.php">Domain Management</a></div>
                <div class="card"><a href="/phpmyadmin" target="_blank">phpMyAdmin</a></div>
            </div>

            <!-- Mini Graphs (Below the Main Content) -->
            <div class="chart-container">
                <h3>CPU Load</h3>
                <canvas id="cpuLoadChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Memory Usage</h3>
                <canvas id="memoryUsageChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Disk Usage</h3>
                <canvas id="diskUsageChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Network Traffic</h3>
                <canvas id="networkTrafficChart"></canvas>
            </div>
        </div>

        <!-- Right Sidebar (General Info & Stats) -->
        <div class="right-sidebar">
            <h3>General Information</h3>
            <p><strong>Current User:</strong> <?php echo $current_user; ?></p>
            <p><strong>Primary Domain (Server IP):</strong> <?php echo $primary_domain; ?></p>
            <p><strong>Home Directory:</strong> <?php echo $home_directory; ?></p>
            <p><strong>Last Login IP:</strong> <?php echo $last_login_ip; ?></p>

            <!-- Line Graph for File Usage, Memory, etc. -->
            <div class="chart-container">
                <h3>System Resource Usage</h3>
                <canvas id="resourceUsageChart"></canvas>
            </div>

            <h3>Statistics</h3>
            <div class="stats-item">
                <strong>CPU Load:</strong>
                <span><?php echo $stats['cpu_load']; ?></span>
            </div>
            <div class="stats-item">
                <strong>Memory Usage:</strong>
                <span><?php echo round($stats['mem_used'] / 1024, 2) . ' MB / ' . round($stats['mem_total'] / 1024, 2) . ' MB'; ?></span>
            </div>
            <div class="stats-item">
                <strong>Disk Usage:</strong>
                <span><?php echo $stats['disk_usage']; ?>%</span>
            </div>
            <div class="stats-item">
                <strong>Network Traffic:</strong>
                <span><?php echo round($stats['rx_mb'], 2) . ' MB received, ' . round($stats['tx_mb'], 2) . ' MB transmitted'; ?></span>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle the live updates using Chart.js -->
    <script>
        const updateInterval = 5000; // Update every 5 seconds

        // Initialize line graph for system resource usage (right sidebar)
        const resourceUsageChart = new Chart(document.getElementById('resourceUsageChart'), {
            type: 'line',
            data: {
                labels: ['File Usage', 'Memory Usage', 'Processes'],
                datasets: [{
                    label: 'Resource Usage',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    data: [45, 80, 20] // Replace with dynamic data later
                }]
            },
            options: { responsive: true }
        });

        // Initialize mini graphs (for CPU, Memory, Disk, Network Traffic)
        const cpuLoadChart = new Chart(document.getElementById('cpuLoadChart'), {
            type: 'line',
            data: {
                labels: ['1 min', '5 min', '15 min'],
                datasets: [{
                    label: 'CPU Load',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    data: [<?php echo $stats['cpu_load']; ?>, 0, 0]
                }]
            },
            options: { responsive: true }
        });

        const memoryUsageChart = new Chart(document.getElementById('memoryUsageChart'), {
            type: 'doughnut',
            data: {
                labels: ['Used', 'Free'],
                datasets: [{
                    label: 'Memory Usage',
                    backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(75, 192, 192, 0.2)'],
                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(75, 192, 192, 1)'],
                    data: [<?php echo $stats['mem_used']; ?>, <?php echo $stats['mem_total'] - $stats['mem_used']; ?>]
                }]
            },
            options: { responsive: true }
        });

        const diskUsageChart = new Chart(document.getElementById('diskUsageChart'), {
            type: 'doughnut',
            data: {
                labels: ['Used', 'Free'],
                datasets: [{
                    label: 'Disk Usage',
                    backgroundColor: ['rgba(255, 205, 86, 0.2)', 'rgba(75, 192, 192, 0.2)'],
                    borderColor: ['rgba(255, 205, 86, 1)', 'rgba(75, 192, 192, 1)'],
                    data: [<?php echo $stats['disk_used']; ?>, <?php echo 100 - $stats['disk_used']; ?>]
                }]
            },
            options: { responsive: true }
        });

        const networkTrafficChart = new Chart(document.getElementById('networkTrafficChart'), {
            type: 'line',
            data: {
                labels: ['Received', 'Transmitted'],
                datasets: [{
                    label: 'Network Traffic (MB)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    data: [<?php echo $stats['rx_mb']; ?>, <?php echo $stats['tx_mb']; ?>]
                }]
            },
            options: { responsive: true }
        });

        // Function to update charts via AJAX
        function updateCharts() {
            fetch('?action=get_stats')
                .then(response => response.json())
                .then(data => {
                    // Update resource usage line chart (right sidebar)
                    resourceUsageChart.data.datasets[0].data = [data.disk_used, data.mem_used, data.cpu_load_1];
                    resourceUsageChart.update();

                    // Update CPU Load Chart
                    cpuLoadChart.data.datasets[0].data = [data.cpu_load_1, data.cpu_load_5, data.cpu_load_15];
                    cpuLoadChart.update();

                    // Update Memory Usage Chart
                    memoryUsageChart.data.datasets[0].data = [data.mem_used, data.mem_total - data.mem_used];
                    memoryUsageChart.update();

                    // Update Disk Usage Chart
                    diskUsageChart.data.datasets[0].data = [data.disk_used, data.disk_total - data.disk_used];
                    diskUsageChart.update();

                    // Update Network Traffic Chart
                    networkTrafficChart.data.datasets[0].data = [data.rx_mb, data.tx_mb];
                    networkTrafficChart.update();

                    // Update live stats in the General Info sidebar
                    document.querySelector('.right-sidebar [data-key="current_user"]').textContent = data.current_user;
                    document.querySelector('.right-sidebar [data-key="primary_domain"]').textContent = data.primary_domain;
                    document.querySelector('.right-sidebar [data-key="home_directory"]').textContent = data.home_directory;
                    document.querySelector('.right-sidebar [data-key="last_login_ip"]').textContent = data.last_login_ip;
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Update charts on page load and every 5 seconds
        updateCharts();
        setInterval(updateCharts, updateInterval);
    </script>
</body>
</html>
