#!/bin/bash

# Function to handle errors and set a default value
handle_error() {
  local value="$1"
  if [ -z "$value" ]; then
    echo "N/A"
  else
    echo "$value"
  fi
}

# Get CPU usage
cpu_usage=$(top -bn1 | grep "Cpu(s)" | awk '{print 100 - $8}')
cpu_usage=$(handle_error "$cpu_usage")

# Get GPU usage (if applicable)
gpu_usage=$(nvidia-smi --query-gpu=utilization.gpu --format=csv,noheader,nounits 2>/dev/null)
gpu_usage=$(handle_error "$gpu_usage")

# Get CPU temperature (if applicable)
cpu_temp=$(sensors | grep 'Package id 0:' | awk '{print $4}' | sed 's/+//g')
cpu_temp=$(handle_error "$cpu_temp")

# Get memory usage
mem_total=$(grep MemTotal /proc/meminfo | awk '{print $2}')
mem_free=$(grep MemFree /proc/meminfo | awk '{print $2}')
mem_used=$((mem_total - mem_free))
mem_usage=$(awk "BEGIN {printf \"%.2f\", $mem_used/$mem_total*100}")
mem_total=$(handle_error "$mem_total")
mem_used=$(handle_error "$mem_used")
mem_usage=$(handle_error "$mem_usage")

# Get disk usage (root partition)
disk_usage=$(df -h / | awk 'NR==2 {print $5}' | tr -d '%')
disk_usage=$(handle_error "$disk_usage")

# Get network traffic (received and transmitted in MB)
iface=$(ip route get 1.1.1.1 | awk '{print $5}')
rx_bytes=$(cat /sys/class/net/$iface/statistics/rx_bytes)
tx_bytes=$(cat /sys/class/net/$iface/statistics/tx_bytes)
rx_mb=$(awk "BEGIN {printf \"%.2f\", $rx_bytes/1024/1024}")
tx_mb=$(awk "BEGIN {printf \"%.2f\", $tx_bytes/1024/1024}")
rx_mb=$(handle_error "$rx_mb")
tx_mb=$(handle_error "$tx_mb")

# Get logged-in users
logged_in_users=$(who | awk '{print $1}' | sort | uniq | paste -sd "," -)
logged_in_users=$(handle_error "$logged_in_users")

# Output JSON
echo '{
  "cpu_usage": "'$cpu_usage'",
  "gpu_usage": "'$gpu_usage'",
  "cpu_temp": "'$cpu_temp'",
  "mem_total": "'$mem_total'",
  "mem_used": "'$mem_used'",
  "mem_usage": "'$mem_usage'",
  "disk_usage": "'$disk_usage'",
  "rx_mb": "'$rx_mb'",
  "tx_mb": "'$tx_mb'",
  "logged_in_users": "'$logged_in_users'"
}'
