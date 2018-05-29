#!/usr/bin/env bash

# Installation Script for Local Development

# Install RPM packages
sudo zypper install git php7 php7-fileinfo php7-gettext php7-json php7-mbstring \
    php7-opcache php7-sqlite php-composer nodejs8 npm8

# Install Node packages
sudo npm install -g gulp-cli

# Download MediaWiki 1.27.4
wget https://releases.wikimedia.org/mediawiki/1.27/mediawiki-1.27.4.tar.gz
tar -xvzf mediawiki-1.27.4.tar.gz
cp -rf mediawiki-1.27.4/* .
rm -r mediawiki-1.27.4 mediawiki-1.27.4.tar.gz

# Download Extensions

function download {
    wget https://extdist.wmflabs.org/dist/extensions/$1
    tar -xvzf $1
    rm $1
}

cd extensions
download AbuseFilter-REL1_27-2072d2f.tar.gz
download Auth_remoteuser-REL1_27-89faa0c.tar.gz
download CategoryTree-REL1_27-b454f2c.tar.gz
download intersection-REL1_27-38cdaf6.tar.gz
download MultiBoilerplate-REL1_27-bb13f76.tar.gz
download ReplaceText-REL1_27-7676bf8.tar.gz
download RSS-REL1_27-d945221.tar.gz
download UserMerge-REL1_27-31ea86d.tar.gz
download UserPageEditProtection-REL1_27-8affdda.tar.gz
cd ..

# Install Composer Packages
composer install

# Copy Test Settings
cp wiki_settings.example.php wiki_settings.php

# Make Directories
mkdir /tmp/wiki_sessions

# Run Installation Script
rm -r data
mkdir data
mv LocalSettings.php _LocalSettings.php
php maintenance/install.php --dbuser="" --dbpass="" --dbname=wiki --dbpath=./data \
    --dbtype=sqlite --confpath=./ --scriptpath=/ --pass=evergreen openSUSE Geeko

rm LocalSettings.php
mv _LocalSettings.php LocalSettings.php
