# -*- mode: ruby -*-
# vi: set ft=ruby :

# Get Vagrant from http://downloads.vagrantup.com/
$script = <<SCRIPT
if [ ! -f "$HOME/.provisioned" ]; then

# setup the server

## install dependencies like php, mysql, and apache
sudo DEBIAN_FRONTEND=noninteractive apt-get -q -y update
#sudo DEBIAN_FRONTEND=noninteractive apt-get -q -y upgrade
sudo DEBIAN_FRONTEND=noninteractive apt-get -o dir::cache::archives="/vagrantcache/apt" -o Dpkg::Options::='--force-confnew' -f -q -y install \
  libapache2-mod-php5 php-pear php5-cli php5-mysql php5-mcrypt mysql-server curl git vim

## configure mysql
echo "CREATE DATABASE lanager;" | mysql
echo "CREATE USER 'lanager'@'localhost' IDENTIFIED BY 'vrfRB9PLEAYzw5UH';" | mysql
echo "GRANT ALL PRIVILEGES ON  lanager.* TO  'lanager'@'localhost' WITH GRANT OPTION;" | mysql
echo "CREATE USER 'lanager'@'%' IDENTIFIED BY 'vrfRB9PLEAYzw5UH';" | mysql
echo "GRANT ALL PRIVILEGES ON  lanager.* TO  'lanager'@'%' WITH GRANT OPTION;" | mysql
mysqladmin -u root password vagrant
sed -i 's/bind-address/;bind-address/g' /etc/mysql/my.cnf # bind to all nics for external access
sudo service mysql restart

## configure apache
sudo a2enmod rewrite
echo "<VirtualHost *:80>
ServerName lanager.dev
ServerAlias www.lanager.dev
DocumentRoot /vagrant/public
<Directory />
  Options FollowSymLinks
  AllowOverride None
</Directory>
<Directory /vagrant/public/>
  Options Indexes FollowSymLinks MultiViews
  AllowOverride All
  Order allow,deny
  allow from all
</Directory>
ErrorLog /var/log/apache2/lanager.dev/error.log
CustomLog /var/log/apache2/lanager.dev/access.log combined
LogLevel info
</VirtualHost>" | sudo tee /etc/apache2/sites-available/lanager.dev > /dev/null
sudo mkdir -p /var/log/apache2/lanager.dev/
sudo touch /var/log/apache2/lanager.dev/{error,access}.log
sudo chown -R www-data:www-data /var/log/apache2/lanager.dev
sudo a2ensite lanager.dev
sudo a2dissite default
sudo service apache2 restart

# setup php
## install composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin
sudo ln /usr/local/bin/composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer.phar

# mount shared directory
ln -s /vagrant /home/vagrant/lanager

# install laravel and dependencies
cd /home/vagrant/lanager
composer install

# set vm as provisioned
touch $HOME/.provisioned
else
  echo "Already provisioned."
fi

echo
echo "######################"
echo "# START READING HERE #"
echo "######################"
echo
echo "The DSN is mysql://lanager:vrfRB9PLEAYzw5UH@localhost:3307/lanager".
echo
echo "Ensure that you have configured lanager.dev to point to 127.0.0.1"
echo "in your hosts file, and you should be able to access the VM from"
echo "http://lanager.dev:8080"
echo
echo "To get started, do these locally:"
echo '    chmod -R 777 storage #only for Unix-y platforms'
echo '    vagrant ssh -c "cd /vagrant; php artisan migrate:install; php artisan migrate" '
SCRIPT

Vagrant.configure("2") do |config|
  config.vm.guest = :linux
  #config.vm.hostname = "lanager.dev"
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"
  
  #8080->80 for web, 3307->3306 for mysql
  config.vm.network :forwarded_port, guest: 80, host: 8080
  config.vm.network :forwarded_port, guest: 3306, host: 3307
  
  #see the top of this file
  config.vm.provision :shell, :inline => $script
  
  #used primarily for apt installs and upgrades, useful for working on the
  #vagrant box itself
  config.vm.synced_folder ".vagrantcache/", "/vagrantcache/"
end
