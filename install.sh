#!/bin/bash

echo "Starting xPanel Installation..."

# Update and upgrade the system
sudo apt update && sudo apt upgrade -y

# Install Apache, PHP, MySQL, and SSH packages
sudo apt install apache2 php libapache2-mod-php mysql-server php-mysql sshpass xdg-utils -y

# Get the server's IP address
IP_ADDRESS=$(hostname -I | awk '{print $1}')

# Set up MySQL database
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

# Restart Apache
sudo systemctl restart apache2

# Create a shortcut command to open xPanel in the default browser using the server's IP address
echo "Creating a command to easily open xPanel..."
sudo bash -c "echo 'xdg-open http://$IP_ADDRESS/xpanel' > /usr/local/bin/xpanel"
sudo chmod +x /usr/local/bin/xpanel

# Automatically open xPanel after installation using the server's IP address
echo "Opening xPanel in your default browser..."
xdg-open "http://$IP_ADDRESS/xpanel"

# Completion message
echo "Installation complete! You can now access xPanel at http://$IP_ADDRESS/xpanel or by typing 'xpanel' in the terminal."
