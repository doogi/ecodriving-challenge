# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.box = "ubuntu/trusty64"
    config.vm.network :private_network, ip: "192.168.56.115"
    config.vm.hostname = "ecodriving-vm"

    config.vm.provider :virtualbox do |vb|
        vb.name = "ecodriving-vm"
        vb.customize ["modifyvm", :id, "--memory", "3072" ]
        vb.customize ["modifyvm", :id, "--ostype", "Ubuntu_64" ]
        vb.customize ["modifyvm", :id, "--cpus", "3" ]
    end

    config.vm.provision "shell", privileged: true, inline: <<-shell
        echo "\nPHP & TOOLS INSTALL, PHP MODULES INSTALL, CONFIG \n------------------------------------------------------------"
        apt-get -y install curl git mc lftp htop php5-fpm php5-cli php5-curl php-pear php5-dev php5-json php5-intl


        echo "\nMONGODB INSTALL AND PECL MONGO EXTENSION \n------------------------------------------------------------"
        sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
        echo "deb http://repo.mongodb.org/apt/ubuntu trusty/mongodb-org/3.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.0.list
        apt-get update
        apt-get install -y mongodb-org
        sed -i 's/bindIp/#bindIp/' /etc/mongod.conf
        service mongod restart
    shell
end
