#!/bin/bash

echo "Starting xPanel Installation..."

# Update and upgrade the system
sudo apt update && sudo apt upgrade -y

# Install Apache, PHP, MySQL, SSH, Certbot, phpMyAdmin, Shellinabox, and xRDP
sudo apt install apache2 php libapache2-mod-php mysql-server php-mysql sshpass certbot python3-certbot-apache phpmyadmin xdg-utils shellinabox xrdp -y

# Configure phpMyAdmin (automatically link it to Apache)
sudo ln -s /usr/share/phpmyadmin /var/www/html/phpmyadmin

# Enable required PHP modules for phpMyAdmin
sudo phpenmod mbstring
sudo systemctl restart apache2

# Get the server's IP address (this will be used as the domain for local testing)
IP_ADDRESS=$(hostname -I | awk '{print $1}')

# Fetch the Ubuntu username
UBUNTU_USER=$(whoami)
echo "Detected Ubuntu user: $UBUNTU_USER"

# Prompt for the Ubuntu user's password (to use it for MySQL as well)
read -sp "Enter the password for MySQL user (same as Ubuntu user $UBUNTU_USER): " MYSQL_PASSWORD
echo

# Set up MySQL user with the same credentials as the Ubuntu user
echo "Setting up MySQL user with Ubuntu credentials..."
sudo mysql -e "CREATE USER '$UBUNTU_USER'@'localhost' IDENTIFIED BY '$MYSQL_PASSWORD';"
sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO '$UBUNTU_USER'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Set up MySQL database for xPanel
echo "Setting up the MySQL database..."
sudo mysql -e "CREATE DATABASE xpanel_db;"
sudo mysql -e "CREATE USER 'xpanel_user'@'localhost' IDENTIFIED BY 'password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON xpanel_db.* TO 'xpanel_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Create the xPanel subdirectory
sudo mkdir -p /var/www/html/xpanel

# Copy frontend and backend files to the subdirectory (xPanel)
sudo cp -r frontend/* /var/www/html/xpanel/
sudo cp -r backend/* /var/www/html/xpanel/

# Set proper permissions
sudo chown -R www-data:www-data /var/www/html/xpanel/

# Create the get_system_stats.sh script for fetching system stats
cat <<EOL | sudo tee /var/www/html/xpanel/get_system_stats.sh
#!/bin/bash

# Get CPU Load and Usage (from mpstat)
cpu_usage=\$(mpstat 1 1 | awk '/Average:/ { print 100 - \$12 }')

# Get Memory Usage
mem_total=\$(grep MemTotal /proc/meminfo | awk '{print \$2}')
mem_free=\$(grep MemFree /proc/meminfo | awk '{print \$2}')
mem_used=\$((mem_total - mem_free))
mem_usage=\$(awk "BEGIN {print \$mem_used/\$mem_total * 100}")

# Get Disk Usage
disk_usage=\$(df -h / | grep / | awk '{print \$5}')

# Get Detailed Disk I/O (from iotop)
disk_io=\$(iotop -b -n 1 | head -n 10)

# Get Network Traffic
rx_bytes=\$(cat /sys/class/net/\$(ip route show default | awk '/default/ {print \$5}')/statistics/rx_bytes)
tx_bytes=\$(cat /sys/class/net/\$(ip route show default | awk '/default/ {print \$5}')/statistics/tx_bytes)
rx_mb=\$(awk "BEGIN {print \$rx_bytes/1024/1024}")
tx_mb=\$(awk "BEGIN {print \$tx_bytes/1024/1024}")

# Get Network Interface Details
network_interfaces=\$(ip -br a)

# Get Open Ports and Services
open_ports=\$(netstat -tuln)

# Get System Uptime and Load Average
uptime=\$(uptime -p)
load_average=\$(uptime | awk -F 'load average: ' '{print \$2}')

# Get Running Processes
process_list=\$(ps aux --sort=-%cpu | head -n 10)

# Get GPU Usage (if NVIDIA GPU is present)
if command -v nvidia-smi &> /dev/null; then
    gpu_usage=\$(nvidia-smi --query-gpu=utilization.gpu,memory.total,memory.used --format=csv,noheader,nounits)
else
    gpu_usage="N/A"
fi

# Get CPU Temperature (from sensors)
if command -v sensors &> /dev/null; then
    cpu_temp=\$(sensors | grep 'Core 0' | awk '{print \$3}')
else
    cpu_temp="N/A"
fi

# Get Block Devices (from lsblk)
block_devices=\$(lsblk -o NAME,SIZE,TYPE,MOUNTPOINT)

# Get System Logs (from journalctl)
sys_logs=\$(journalctl -n 10)

# Get the current logged-in users
logged_in_users=\$(who | awk '{print \$1}' | sort | uniq | paste -sd "," -)

# Output as JSON
echo "{ 
    \\"cpu_usage\\": \\"\$cpu_usage\\", 
    \\"mem_total\\": \\"\$((mem_total / 1024)) MB\\", 
    \\"mem_used\\": \\"\$((mem_used / 1024)) MB\\", 
    \\"mem_usage\\": \\"\$mem_usage\\", 
    \\"disk_usage\\": \\"\$disk_usage\\", 
    \\"rx_mb\\": \\"\$rx_mb MB\\", 
    \\"tx_mb\\": \\"\$tx_mb MB\\", 
    \\"network_interfaces\\": \\"\$network_interfaces\\", 
    \\"open_ports\\": \\"\$open_ports\\", 
    \\"uptime\\": \\"\$uptime\\", 
    \\"load_average\\": \\"\$load_average\\", 
    \\"process_list\\": \\"\$process_list\\", 
    \\"gpu_usage\\": \\"\$gpu_usage\\", 
    \\"cpu_temp\\": \\"\$cpu_temp\\", 
    \\"block_devices\\": \\"\$block_devices\\", 
    \\"sys_logs\\": \\"\$sys_logs\\", 
    \\"logged_in_users\\": \\"\$logged_in_users\\" 
}"
EOL

# Make the script executable
sudo chmod +x /var/www/html/xpanel/get_system_stats.sh

# Shellinabox installation and configuration
echo "Installing and configuring Shellinabox..."
sudo apt-get install shellinabox -y

# Start Shellinabox service
sudo systemctl enable shellinabox
sudo systemctl start shellinabox

# Modify the Shellinabox default configuration to bind to port 4200
sudo sed -i 's/^SHELLINABOX_PORT=.*/SHELLINABOX_PORT=4200/' /etc/default/shellinabox

# Restart Shellinabox service
sudo systemctl restart shellinabox

# Verify if Shellinabox is running on port 4200
sudo netstat -nap | grep shellinabox

# RDP installation
echo "Installing and configuring xRDP..."
sudo systemctl enable xrdp
sudo systemctl start xrdp

# Install SSL using Let's Encrypt for the local IP (use --register-unsafely-without-email for testing purposes)
sudo certbot --apache -d $IP_ADDRESS --register-unsafely-without-email --non-interactive --agree-tos

# Configure sudoers to allow www-data to run get_system_stats.sh without a password
echo "Configuring sudoers to allow www-data to run get_system_stats.sh without a password..."
sudo bash -c "echo 'www-data ALL=(ALL) NOPASSWD: /var/www/html/xpanel/get_system_stats.sh' >> /etc/sudoers"

# Create a shortcut command to open xPanel in the default browser using the IP address
echo "Creating a command to easily open xPanel..."
sudo bash -c "echo 'xdg-open https://$IP_ADDRESS/xpanel' > /usr/local/bin/xpanel"
sudo chmod +x /usr/local/bin/xpanel

# Automatically open xPanel after installation using the IP address
echo "Opening xPanel in your default browser..."
xdg-open "https://$IP_ADDRESS/xpanel"

# Completion message
echo "Installation complete! You can now access xPanel at https://$IP_ADDRESS/xpanel or by typing 'xpanel' in the terminal."
