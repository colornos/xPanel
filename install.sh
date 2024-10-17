#!/bin/bash

echo "Starting cPanel-like Control Panel Installation..."

# Update and upgrade the system
sudo apt update && sudo apt upgrade -y

# Install Apache, PHP, MySQL, and SSH packages
sudo apt install apache2 php libapache2-mod-php mysql-server php-mysql sshpass xdg-utils -y

# Get the server's IP address
IP_ADDRESS=$(hostname -I | awk '{print $1}')

# Set up MySQL database
echo "Setting up the MySQL database..."
sudo mysql -e "CREATE DATABASE control_panel_db;"
sudo mysql -e "CREATE USER 'cpanel_user'@'localhost' IDENTIFIED BY 'password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON control_panel_db.* TO 'cpanel_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Create the control panel subdirectory
sudo mkdir -p /var/www/html/controlpanel

# Copy frontend and backend files to the subdirectory (control panel)
sudo cp -r frontend/* /var/www/html/controlpanel/
sudo cp -r backend/* /var/www/html/controlpanel/

# Set proper permissions
sudo chown -R www-data:www-data /var/www/html/controlpanel/

# Restart Apache
sudo systemctl restart apache2

# Create a shortcut command to open the control panel in the default browser using the server's IP address
echo "Creating a command to easily open the control panel..."
sudo bash -c "echo 'xdg-open http://$IP_ADDRESS/controlpanel' > /usr/local/bin/open-cpanel"
sudo chmod +x /usr/local/bin/open-cpanel

# Automatically open the control panel after installation using the server's IP address
echo "Opening control panel in your default browser..."
xdg-open "http://$IP_ADDRESS/controlpanel"

# Completion message
echo "Installation complete! You can now access your control panel at http://$IP_ADDRESS/controlpanel or by typing 'open-cpanel' in the terminal."
