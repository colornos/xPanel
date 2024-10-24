#!/bin/bash

echo "Starting xPanel Installation..."

# Update and upgrade the system
echo "Updating and upgrading the system..."
sudo apt update && sudo apt upgrade -y

# Minimal desktop install
sudo apt-get install ubuntu-desktop-minimal -y
sudo apt install tigervnc-standalone-server -y

# Install basic utilities
echo "Installing basic utilities..."
sudo apt install -y net-tools dos2unix

# Install Apache, PHP, MariaDB, SSH, Certbot, phpMyAdmin, Shellinabox, and xRDP
echo "Installing required packages..."
sudo apt install -y apache2 php libapache2-mod-php mariadb-server php-mysql sshpass certbot python3-certbot-apache phpmyadmin xdg-utils shellinabox xrdp vim

# Configure phpMyAdmin (automatically link it to Apache)
echo "Configuring phpMyAdmin..."
sudo ln -s /usr/share/phpmyadmin /var/www/html/phpmyadmin

# Enable required PHP modules for phpMyAdmin
echo "Enabling PHP modules for phpMyAdmin..."
sudo phpenmod mbstring
sudo systemctl restart apache2

# Get the server's IP address (this will be used as the domain for local testing)
IP_ADDRESS=$(hostname -I | awk '{print $1}')

# Fetch the Ubuntu username
UBUNTU_USER=$(whoami)
echo "Detected Ubuntu user: $UBUNTU_USER"

# Prompt for the Ubuntu user's password (to use it for MariaDB as well)
read -sp "Enter the password for MariaDB user (same as Ubuntu user $UBUNTU_USER): " MYSQL_PASSWORD
echo

# Set up MariaDB user with the same credentials as the Ubuntu user
echo "Setting up MariaDB user with Ubuntu credentials..."
sudo mariadb -e "CREATE USER '$UBUNTU_USER'@'localhost' IDENTIFIED BY '$MYSQL_PASSWORD';"
sudo mariadb -e "GRANT ALL PRIVILEGES ON *.* TO '$UBUNTU_USER'@'localhost';"
sudo mariadb -e "FLUSH PRIVILEGES;"

# Set up MariaDB database for xPanel
echo "Setting up the MariaDB database..."
sudo mariadb -e "CREATE DATABASE xpanel_db;"
sudo mariadb -e "CREATE USER 'xpanel_user'@'localhost' IDENTIFIED BY 'password';"
sudo mariadb -e "GRANT ALL PRIVILEGES ON xpanel_db.* TO 'xpanel_user'@'localhost';"
sudo mariadb -e "FLUSH PRIVILEGES;"

# Create the xPanel subdirectory
echo "Creating xPanel directory..."
sudo mkdir -p /var/www/html/xpanel

# Copy frontend and backend files to the subdirectory (xPanel)
echo "Copying xPanel frontend and backend files..."
sudo cp -r frontend/* /var/www/html/xpanel/
sudo cp -r backend/* /var/www/html/xpanel/

# Convert line endings of all .sh files in xPanel to Unix format
echo "Converting line endings to Unix format for all .sh files in xPanel..."
find /var/www/html/xpanel -type f -name "*.sh" -exec dos2unix {} \;

# Set proper permissions
echo "Setting proper permissions for xPanel..."
sudo chown -R www-data:www-data /var/www/html/xpanel/

# Make the get_system_stats.sh script executable
echo "Making get_system_stats.sh executable..."
sudo chmod +x /var/www/html/xpanel/get_system_stats.sh

# Shellinabox installation and configuration
echo "Installing and configuring Shellinabox..."
sudo apt-get install -y shellinabox

# Start Shellinabox service
echo "Enabling and starting Shellinabox service..."
sudo systemctl enable shellinabox
sudo systemctl start shellinabox

# Modify the Shellinabox default configuration to bind to port 4200
echo "Modifying Shellinabox configuration to bind to port 4200..."
sudo sed -i 's/^SHELLINABOX_PORT=.*/SHELLINABOX_PORT=4200/' /etc/default/shellinabox

# Restart Shellinabox service
echo "Restarting Shellinabox service..."
sudo systemctl restart shellinabox

# Verify if Shellinabox is running on port 4200
echo "Verifying if Shellinabox is running on port 4200..."
sudo netstat -nap | grep shellinabox

# RDP installation
echo "Enabling and starting xRDP..."
sudo systemctl enable xrdp
sudo systemctl start xrdp

# Install SSL using Let's Encrypt for the local IP (use --register-unsafely-without-email for testing purposes)
echo "Installing SSL with Let's Encrypt..."
sudo certbot --apache -d $IP_ADDRESS --register-unsafely-without-email --non-interactive --agree-tos

# Configure sudoers to allow www-data to run get_system_stats.sh without a password
echo "Configuring sudoers for get_system_stats.sh..."
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
