#!/bin/bash

echo "Starting xPanel Installation..."

# Update and upgrade the system
sudo apt update && sudo apt upgrade -y

# Install Apache, PHP, MySQL, SSH, Certbot, and phpMyAdmin
sudo apt install apache2 php libapache2-mod-php mysql-server php-mysql sshpass certbot python3-certbot-apache phpmyadmin xdg-utils -y

# Configure phpMyAdmin (automatically link it to Apache)
sudo ln -s /usr/share/phpmyadmin /var/www/html/phpmyadmin

# Enable required PHP modules for phpMyAdmin
sudo phpenmod mbstring
sudo systemctl restart apache2

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

# Install SSL using Let's Encrypt (adjust to your domain name)
DOMAIN_NAME="colornos.ddns.net"  # Replace with your domain name
sudo certbot --apache -d $DOMAIN_NAME

# Create a shortcut command to open xPanel in the default browser using the server's IP address
echo "Creating a command to easily open xPanel..."
sudo bash -c "echo 'xdg-open https://$DOMAIN_NAME/xpanel' > /usr/local/bin/xpanel"
sudo chmod +x /usr/local/bin/xpanel

# Automatically open xPanel after installation using the domain name
echo "Opening xPanel in your default browser..."
xdg-open "https://$DOMAIN_NAME/xpanel"

# Completion message
echo "Installation complete! You can now access xPanel at https://$DOMAIN_NAME/xpanel or by typing 'xpanel' in the terminal."
