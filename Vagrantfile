# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

   config.vm.network "forwarded_port", guest: 80, host: 8088

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
   config.vm.box = "ubuntu/xenial64"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
   config.vm.network "public_network"

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
   config.vm.provider "virtualbox" do |vb|
     # Display the VirtualBox GUI when booting the machine
     vb.gui = false

     vb.name = "HashKraken"
     # Customize the amount of memory on the VM:
     vb.memory = "1024"

     # Avoid ubuntu network problems at boot
     vb.customize ["modifyvm", :id, "--cableconnected1", "on"]

     # Limit CPU usage
     vb.customize ["modifyvm", :id, "--cpuexecutioncap", "65"]
   end

  # Enable USB Controller on VirtualBox
  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--usb", "on"]
    vb.customize ["modifyvm", :id, "--usbehci", "on"]
  end

  ###############################################################
   config.vm.provision "shell", inline: <<-SHELL
     printf "\n\nInstalling software\n"
     sudo apt-get update && sudo apt-get upgrade -y
     sudo DEBIAN_FRONTEND=noninteractive
     sudo apt-get -y install curl git openssl pkg-config libssl-dev python wget zlib1g-dev unzip openssh-client php7.0 php7.0-mbstring php7.0-cli php7.0-curl php7.0-json php7.0-xml php7.0-sqlite php7.0-pgsql php7.0-mysql php7.0-dev apache2 libapache2-mod-php7.0

     printf "Enabling mod_rewrite"
     sudo a2enmod rewrite
     sudo a2enmod headers
     sudo cp -f /vagrant/000-default.conf /etc/apache2/sites-available/000-default.conf
     sudo service apache2 restart

     printf "\n\nInstalling PECL PHP extensions\n"
     sudo rm -f /usr/local/etc/php/conf.d/pecl.ini
     sudo touch /usr/local/etc/php/conf.d/pecl.ini
     sudo chmod 0775 -R /usr/local/etc/php/conf.d
     pecl config-set php_ini /usr/local/etc/php/conf.d/pecl.ini
     pear config-set php_ini /usr/local/etc/php/conf.d/pecl.ini
     pecl install xdebug-2.4.0

     printf "\n\nInstalling Composer\n"
     curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

     printf "\n\nSystem info:\n"
     php -i

     # installing the framework
     cd /vagrant
     composer install --no-dev

     printf "\n\n\n\nThe box is ready. Now simply run \"vagrant ssh\" to connect!\n"

   SHELL



end
