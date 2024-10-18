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
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="xPanel dashboard.">
    <meta name="keywords" content="Control Panel">
    <meta name="author" content="Colornos">
    <title>xPanel</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/material-icons/material-icons.css">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/material-vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/ui/prism.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/material.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/material-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/material-colors.css">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/material-vertical-compact-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/colors/material-palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/mobiriseicons/24px/mobirise/style.css">
    <!-- END: Page CSS-->

    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Custom styling for the sections */
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
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-compact-menu material-vertical-layout material-layout 1-column   fixed-navbar" data-open="click" data-menu="vertical-compact-menu" data-col="1-column">

    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-light navbar-shadow navbar-brand-center">
        <div class="navbar-wrapper">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                    <li class="nav-item"><a class="navbar-brand" href="index.php"><img class="brand-logo" alt="x" src="app-assets/images/logo/logo.png">
                            <h3 class="brand-text">xPanel</h3>
                        </a></li>
                    <li class="nav-item d-md-none"><a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="la la-ellipsis-v"></i></a></li>
                </ul>
            </div>
            <div class="navbar-container content">
                <div class="collapse navbar-collapse" id="navbar-mobile">
                    <ul class="nav navbar-nav float-right">
                        <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown"><span class="avatar avatar-online"><img src="app-assets/images/portrait/small/avatar-s-19.png" alt="avatar"><i></i></span></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"><i class="ft-user"></i> Edit Profile</a>
                                <div class="dropdown-divider"></div><a class="dropdown-item" href="#"><i class="ft-power"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- END: Header-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-header row">
            <div class="content-header-light col-12">
                <div class="row">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                        <h3 class="content-header-title">x</h3>
                        <div class="row breadcrumbs-top">
                            <div class="breadcrumb-wrapper col-12">
<?php
// Get the current directory
$current_directory = getcwd();
$base_directory = '/var/www/html/xpanel'; // Adjust this based on your server's base directory
$relative_path = str_replace($base_directory, '', $current_directory);
$directory_parts = array_filter(explode('/', $relative_path));

// Breadcrumb generation
echo '<ol class="breadcrumb">';
echo '<li class="breadcrumb-item"><a href="index.php">Home</a></li>'; // Always show Home

$path_accumulation = '';
foreach ($directory_parts as $part) {
    $path_accumulation .= '/' . $part;
    if ($part !== end($directory_parts)) {
        echo '<li class="breadcrumb-item"><a href="' . $path_accumulation . '">' . ucfirst($part) . '</a></li>';
    } else {
        echo '<li class="breadcrumb-item active">' . ucfirst($part) . '</li>'; // Active folder (current directory)
    }
}
echo '</ol>';
?>

                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="content-overlay"></div>
        <div class="content-wrapper">
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
                        </div>
                    </div>

                    <!-- Terminal Section -->
                    <div class="section">
                        <div class="section-header">Terminal</div>
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
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light navbar-border navbar-shadow">
        <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
            <span class="float-md-left d-block d-md-inline-block">Copyright &copy; 2019 Colornos</span>
            <span class="float-md-right d-none d-lg-block">Hand-crafted & Made with<i class="ft-heart pink"></i><span id="scroll-top"></span></span>
        </p>
    </footer>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/material-vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/ui/prism.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/material-app.js"></script>

    <!-- Dynamic Stats Fetching -->
    <script>
        function updateStats() {
            fetch('?action=get_stats')
                .then(response => response.json())
                .then(data => {
                    // Update stats dynamically
                    document.getElementById('cpu_usage_value').textContent = data.cpu_usage + '%';
                    document.getElementById('mem_usage_value').textContent = data.mem_usage.toFixed(2) + '%';
                    document.getElementById('disk_usage_value').textContent = data.disk_used + '%';
                    document.getElementById('rx_mb_value').textContent = data.rx_mb.toFixed(2) + ' MB';
                    document.getElementById('tx_mb_value').textContent = data.tx_mb.toFixed(2) + ' MB';
                    document.getElementById('block_devices_value').textContent = data.block_devices;
                    document.getElementById('sys_logs_value').textContent = data.sys_logs;
                })
                .catch(error => console.error('Error fetching stats:', error));
        }

        // Fetch stats every 5 seconds
        setInterval(updateStats, 5000);
    </script>
    <!-- END: Page JS-->
</body>
<!-- END: Body-->
</html>
