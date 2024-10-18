#!/bin/bash

# Get CPU Load
cpu_load=$(uptime | awk -F'load average:' '{ print $2 }' | sed 's/,//g')

# Get Memory Usage
mem_total=$(grep MemTotal /proc/meminfo | awk '{print $2}')
mem_free=$(grep MemFree /proc/meminfo | awk '{print $2}')
mem_used=$((mem_total - mem_free))
mem_usage=$(awk "BEGIN {print $mem_used/$mem_total * 100}")

# Get Disk Usage
disk_usage=$(df -h / | grep / | awk '{print $5}')

# Get Network Traffic
rx_bytes=$(cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/statistics/rx_bytes)
tx_bytes=$(cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/statistics/tx_bytes)
rx_mb=$(awk "BEGIN {print $rx_bytes/1024/1024}")
tx_mb=$(awk "BEGIN {print $tx_bytes/1024/1024}")

# Output JSON format
echo "{ \"cpu_load\": \"$cpu_load\", \"mem_total\": \"$mem_total\", \"mem_used\": \"$mem_used\", \"mem_usage\": \"$mem_usage\", \"disk_usage\": \"$disk_usage\", \"rx_mb\": \"$rx_mb\", \"tx_mb\": \"$tx_mb\" }"
