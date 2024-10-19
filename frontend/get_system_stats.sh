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

# Get Network Traffic
rx_bytes=$(cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/statistics/rx_bytes)
tx_bytes=$(cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/statistics/tx_bytes)
rx_mb=$(awk "BEGIN {print $rx_bytes/1024/1024}")
tx_mb=$(awk "BEGIN {print $tx_bytes/1024/1024}")

# Get the current logged-in users (from who)
logged_in_users=$(who | awk '{print $1}' | sort | uniq | paste -sd "," -)

# Output as JSON
echo "{ 
    \"cpu_usage\": \"$cpu_usage\", 
    \"mem_total\": \"$((mem_total / 1024)) MB\", 
    \"mem_used\": \"$((mem_used / 1024)) MB\", 
    \"mem_usage\": \"$mem_usage\", 
    \"disk_usage\": \"$disk_usage\", 
    \"rx_mb\": \"$rx_mb MB\", 
    \"tx_mb\": \"$tx_mb MB\", 
    \"logged_in_users\": \"$logged_in_users\" 
}"
