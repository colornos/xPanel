#!/bin/bash

# Get CPU Load and Usage (from mpstat)
cpu_usage=$(mpstat 1 1 | awk '/Average:/ { print 100 - $12 }')

# Get Memory Usage
mem_total=$(grep MemTotal /proc/meminfo | awk '{print $2}')
mem_free=$(grep MemFree /proc/meminfo | awk '{print $2}')
mem_used=$((mem_total - mem_free))
mem_usage=$(awk "BEGIN {print $mem_used/$mem_total * 100}")

# Get Disk Usage
disk_usage=$(df -h / | grep / | awk '{print $5}')

# Get Detailed Disk I/O (from iotop)
disk_io=$(iotop -b -n 1 | head -n 10)

# Get Network Traffic (from ifconfig/ip)
rx_bytes=$(cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/statistics/rx_bytes)
tx_bytes=$(cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/statistics/tx_bytes)
rx_mb=$(awk "BEGIN {print $rx_bytes/1024/1024}")
tx_mb=$(awk "BEGIN {print $tx_bytes/1024/1024}")

# Get Network Interface Details (from ifconfig/ip)
network_interfaces=$(ip -br a)

# Get Open Ports and Services (from netstat)
open_ports=$(netstat -tuln)

# Get System Uptime and Load Average (from uptime)
uptime=$(uptime -p)
load_average=$(uptime | awk -F 'load average: ' '{print $2}')

# Get Running Processes (from ps aux)
process_list=$(ps aux --sort=-%cpu | head -n 10)

# Get GPU Usage (if NVIDIA GPU is present, from nvidia-smi)
if command -v nvidia-smi &> /dev/null; then
    gpu_usage=$(nvidia-smi --query-gpu=utilization.gpu,memory.total,memory.used --format=csv,noheader,nounits)
else
    gpu_usage="N/A"
fi

# Get CPU Temperature (from sensors)
if command -v sensors &> /dev/null; then
    cpu_temp=$(sensors | grep 'Core 0' | awk '{print $3}')
else
    cpu_temp="N/A"
fi

# Get Block Devices (from lsblk)
block_devices=$(lsblk -o NAME,SIZE,TYPE,MOUNTPOINT)

# Get System Logs (from journalctl)
sys_logs=$(journalctl -n 10)

# Output as JSON
echo "{ 
    \"cpu_usage\": \"$cpu_usage\", 
    \"mem_total\": \"$((mem_total / 1024)) MB\", 
    \"mem_used\": \"$((mem_used / 1024)) MB\", 
    \"mem_usage\": \"$mem_usage\", 
    \"disk_usage\": \"$disk_usage\", 
    \"rx_mb\": \"$rx_mb MB\", 
    \"tx_mb\": \"$tx_mb MB\", 
    \"network_interfaces\": \"$network_interfaces\", 
    \"open_ports\": \"$open_ports\", 
    \"uptime\": \"$uptime\", 
    \"load_average\": \"$load_average\", 
    \"process_list\": \"$process_list\", 
    \"gpu_usage\": \"$gpu_usage\", 
    \"cpu_temp\": \"$cpu_temp\", 
    \"block_devices\": \"$block_devices\", 
    \"sys_logs\": \"$sys_logs\" 
}"
