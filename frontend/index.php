<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>xPanel - Live Server Stats</title>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Add some basic styling -->
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
        .chart-container {
            width: 80%;
            margin: auto;
        }
        canvas {
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
    <h1>xPanel - Live Server Stats</h1>

    <!-- CPU Load Chart -->
    <div class="chart-container">
        <h2>CPU Load (1, 5, 15 min)</h2>
        <canvas id="cpuLoadChart"></canvas>
    </div>

    <!-- Memory Usage Chart -->
    <div class="chart-container">
        <h2>Memory Usage</h2>
        <canvas id="memoryUsageChart"></canvas>
    </div>

    <!-- Disk Usage Chart -->
    <div class="chart-container">
        <h2>Disk Usage</h2>
        <canvas id="diskUsageChart"></canvas>
    </div>

    <!-- Network Traffic Chart -->
    <div class="chart-container">
        <h2>Network Traffic (MB)</h2>
        <canvas id="networkTrafficChart"></canvas>
    </div>

    <!-- JavaScript to handle the live updates -->
    <script>
        const updateInterval = 5000; // Update every 5 seconds

        // Initialize charts
        const cpuLoadChart = new Chart(document.getElementById('cpuLoadChart'), {
            type: 'line',
            data: {
                labels: ['1 min', '5 min', '15 min'],
                datasets: [{
                    label: 'CPU Load',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    data: [0, 0, 0]
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
                    data: [0, 100] // Initial data
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
                    data: [0, 100] // Initial data
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
                    data: [0, 0] // Initial data
                }]
            },
            options: { responsive: true }
        });

        // Function to update charts via AJAX
        function updateCharts() {
            fetch('/xpanel/get_live_stats.php')
                .then(response => response.json())
                .then(data => {
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
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Update charts on page load and every 5 seconds
        updateCharts();
        setInterval(updateCharts, updateInterval);
    </script>
</body>
</html>
