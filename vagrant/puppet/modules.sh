#!/bin/sh

if [ ! -d "/etc/puppet/modules" ]; then
    mkdir -p /etc/puppet/modules;
fi

if [ ! -d "/etc/puppet/modules/apache" ]; then
    puppet module install puppetlabs-apache;
fi

if [ ! -d "/etc/puppet/modules/mysql" ]; then
    puppet module install puppetlabs-mysql;
fi

if [ ! -d "/etc/puppet/modules/apt" ]; then
    puppet module install puppetlabs-apt;
fi

if [ ! -d "/etc/puppet/modules/git" ]; then
    puppet module install puppetlabs-git;
fi
