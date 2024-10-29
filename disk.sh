#!/bin/bash

# Function to display a colored progress bar
display_colored_progress_bar() {
    local usage_percent=$1
    local bar_width=50
    local filled=$((usage_percent * bar_width / 100))
    local empty=$((bar_width - filled))

    # Define color codes
    local green="\e[42m"
    local yellow="\e[43m"
    local red="\e[41m"
    local reset="\e[0m"

    # Choose color based on usage percentage
    if [ $usage_percent -le 50 ]; then
        local color=$green
    elif [ $usage_percent -le 80 ]; then
        local color=$yellow
    else
        local color=$red
    fi

    # Print the progress bar
    printf "["
    printf "${color} %.0s${reset}" $(seq 1 $filled)
    printf " %.0s" $(seq 1 $empty)
    printf "] %d%%\n" "$usage_percent"
}

# Disk Usage Section
echo "=========================="
echo "      Disk Usage Summary"
echo "=========================="

# Extract total disk usage information
df -h --total | grep "total" | while read -r line; do
    total_size=$(echo $line | awk '{print $2}')
    used=$(echo $line | awk '{print $3}')
    available=$(echo $line | awk '{print $4}')
    usage=$(echo $line | awk '{print $5}' | tr -d '%')

    echo "Total Size: $total_size"
    echo "Used: $used"
    echo "Available: $available"
    echo -n "Usage: "
    display_colored_progress_bar "$usage"
done

# Separator
echo
echo "--------------------------"
echo "      Memory Usage Summary"
echo "--------------------------"

# Memory Usage Section
mem_info=$(free -m | grep Mem)

total_mem=$(echo $mem_info | awk '{print $2}')
used_mem=$(echo $mem_info | awk '{print $3}')
free_mem=$(echo $mem_info | awk '{print $4}')
usage_percent=$((used_mem * 100 / total_mem))

# Display memory information
echo "Total Memory: ${total_mem} MB"
echo "Used: ${used_mem} MB"
echo "Free: ${free_mem} MB"
echo -n "Usage: "
display_colored_progress_bar "$usage_percent"
