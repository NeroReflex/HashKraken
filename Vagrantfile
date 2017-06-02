# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

   config.vm.network "forwarded_port", guest: 80, host: 8088

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
   config.vm.box = "NeroReflex/Gishiki"

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
     vb.memory = "4096"

     # Avoid ubuntu network problems at boot
     vb.customize ["modifyvm", :id, "--cableconnected1", "on"]

     # Limit CPU usage
     vb.customize ["modifyvm", :id, "--cpuexecutioncap", "100"]
   end

  # Enable USB Controller on VirtualBox
  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--usb", "on"]
    vb.customize ["modifyvm", :id, "--usbehci", "on"]
  end

  ###############################################################
   config.vm.provision "shell", inline: <<-SHELL
     sudo chmod 0777 -R /vagrant/errors.txt

     cd /vagrant
     composer install --no-dev

     printf "\n\n\n\nThe box is ready. Now simply run \"vagrant ssh\" to connect!\n"
   SHELL
end
