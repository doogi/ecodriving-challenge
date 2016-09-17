# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.box = "ubuntu/trusty64"
    config.vm.network :private_network, ip: "192.168.56.115"
    config.vm.hostname = "ecodriving-vm"
    config.vm.network "forwarded_port", guest: 8000, host: 8000

    config.vm.provider :virtualbox do |vb|
        vb.name = "ecodriving-vm"
        vb.customize ["modifyvm", :id, "--memory", "3072" ]
        vb.customize ["modifyvm", :id, "--cpus", "2" ]
    end

    config.vm.provision "shell", privileged: true, inline: <<-shell
        apt-get update

        echo "\nPHP & TOOLS INSTALL, PHP MODULES INSTALL, CONFIG \n------------------------------------------------------------"
        apt-get -y install curl git mc lftp htop php5-fpm php5-cli php5-curl php-pear php5-dev php5-json php5-intl
        apt-get install -y autoconf g++ make openssl libssl-dev libcurl4-openssl-dev
        apt-get install -y libcurl4-openssl-dev pkg-config
        apt-get install -y libsasl2-dev

        apt-get install libpcre3 libpcre3-dev

        echo "\nMONGODB INSTALL AND PECL MONGO EXTENSION \n------------------------------------------------------------"
        sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
        echo "deb http://repo.mongodb.org/apt/ubuntu trusty/mongodb-org/3.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.0.list
        apt-get update
        apt-get install -y mongodb-org
        sed -i 's/bindIp/#bindIp/' /etc/mongod.conf
        service mongod restart

        pecl install mongodb
        echo "extension=mongodb.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`

        mongoimport -d data -c rows /vagrant/data.json

        apt-get -y install curl
        composer_exists=`which composer`
        if [ -z $composer_exists ]
        then
          curl -sS https://getcomposer.org/installer | php
          mv composer.phar /usr/local/bin/composer
        else
          composer self-update
        fi

        cd /vagrant

        composer install

        cd /vagrant/web
        php -S 0.0.0.0:8000 -t /vagrant/web &
    shell
end
