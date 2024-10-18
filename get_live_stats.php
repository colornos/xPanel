<?php
// Execute the bash script to get system stats
$system_stats = shell_exec('sudo /var/www/html/xpanel/get_system_stats.sh');
$stats = json_decode($system_stats, true);

// Prepare data for the frontend
$data = [
    'cpu_load_1' => (float) $stats['cpu_load'],
    'cpu_load_5' => (float) $stats['cpu_load'],
    'cpu_load_15' => (float) $stats['cpu_load'],
    'mem_total' => (int) $stats['mem_total'] / 1024, // Convert to MB
    'mem_used' => (int) $stats['mem_used'] / 1024, // Convert to MB
    'disk_total' => 100, // Placeholder, replace with actual values
    'disk_used' => (float) trim($stats['disk_usage'], '%'), // Disk usage as percentage
    'rx_mb' => (float) $stats['rx_mb'],
    'tx_mb' => (float) $stats['tx_mb']
];

// Return JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
