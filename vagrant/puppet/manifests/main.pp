include apt_update
include php5

# If you want PHP 5.4 uncomment the following line, and comment out the php53debian line
#  then run "vagrant provision" and you should have php 5.4

#include php54dotdeb
include php53debian

include otherstuff
include apache
include groups
include composer
include symfony

# If you want the mysql package and server, uncomment the following line
#  then run "vagrant provision"
# The database is named `symfony`, the user is `symfony` and the password `Shub9aiJ`

#include mysql


class apt_update {
    exec { "aptGetUpdate":
        command => "apt-get update",
        path => ["/bin", "/usr/bin"]
    }
}

class apache {
    package { "apache2-mpm-prefork":
        ensure => latest,
        require => Exec["aptGetUpdate"]
    }

    package { "libapache2-mod-php5":
        ensure => latest,
        require => Package["apache2-mpm-prefork"],
        notify => Service["apache2"],
    }

    service { "apache2":
        ensure => running,
        require => Package["apache2-mpm-prefork"],
        subscribe => File["main-vhost.conf", "httpd.conf", "mod_rewrite"]
    }

    file { "main-vhost.conf":
        path => '/etc/apache2/conf.d/main-vhost.conf',
        ensure => file,
        content => template('default/main-vhost.conf'),
        require => Package["apache2-mpm-prefork"]
    }

    file { "httpd.conf":
        path => "/etc/apache2/httpd.conf",
        ensure => file,
        content => template('default/httpd.conf'),
        require => Package["apache2-mpm-prefork"]
    }

    file { "mod_rewrite":
        path => "/etc/apache2/mods-enabled/rewrite.load",
        ensure => "link",
        target => "/etc/apache2/mods-available/rewrite.load",
        require => Package["apache2-mpm-prefork"]
    }
}

class php5 {

    package { "php5-cli":
        ensure => latest,
        require => Exec["aptGetUpdate"],
    }

    package { ["php5-xdebug", "php5-intl", "php5-sqlite"]:
        ensure => latest,
        require => Package["libapache2-mod-php5"],
        notify => Service["apache2"]
    }

    package { "php5-suhosin":
        ensure => purged,
        notify => Service["apache2"]
    }

    file { "php-timezone.ini":
        path => "/etc/php5/cli/conf.d/30-timezone.ini",
        ensure => file,
        content => template('default/php-timezone.ini'),
        require => Package["php5-cli"]
    }
}

class php54dotdeb {
    file { "dotdeb.list":
        path => "/etc/apt/sources.list.d/dotdeb.list",
        ensure => file,
        owner => "root",
        group => "root",
        content => "deb http://ftp.ch.debian.org/debian squeeze main contrib non-free\ndeb http://packages.dotdeb.org squeeze all\ndeb-src http://packages.dotdeb.org squeeze all\ndeb http://packages.dotdeb.org squeeze-php54 all\ndeb-src http://packages.dotdeb.org squeeze-php54 all",
        notify => Exec["dotDebKeys"]
    }

#there's a conflict when you upgrade from 5.3 to 5.4 in xdebug.ini.
# you don't need this, if you directly install 5.4
    file { "xdebug.ini":
        path => "/etc/php5/mods-available/xdebug.ini",
        ensure => file,
        owner => "root",
        group => "root",
        source => "/usr/share/php5/xdebug/xdebug.ini",
        require => Package['php5-xdebug']
    }

    exec { "dotDebKeys":
        command => "wget -q -O - http://www.dotdeb.org/dotdeb.gpg | sudo apt-key add -",
        path => ["/bin", "/usr/bin"],
        notify => Exec["aptGetUpdate"],
        unless => "apt-key list | grep dotdeb"
    }

    package { ["php5-apc", "php5-xhprof"]:
        ensure => latest,
        require => Package["libapache2-mod-php5"],
        notify => Service["apache2"],
    }

    package { ["phpapi-20090626", "php-apc"]:
        ensure => purged,
    }

}

class php53debian {
    package { "php-apc":
        ensure => latest,
        require => Package["libapache2-mod-php5"]
    }

    file { "dotdeb.list":
        path => "/etc/apt/sources.list.d/dotdeb.list",
        ensure => absent,
        notify => Exec["aptGetUpdate"],
    }
}

class symfony {

    exec { "vendorsInstall":
        cwd => "/vagrant",
        command => "php composer.phar install",
        timeout => 1200,
        path => ["/bin", "/usr/bin"],
        creates => "/vagrant/vendor",
        logoutput => true,
        require => Exec["composerPhar"],
    }
}

class composer {
    exec { "composerPhar":
        cwd => "/vagrant",
        command => "curl -s http://getcomposer.org/installer | php",
        path => ["/bin", "/usr/bin"],
        creates => "/vagrant/composer.phar",
        require => Package["php5-cli", "curl", "git"],
    }
}

class groups {
    group { "puppet":
        ensure => present,
    }
}

class otherstuff {
     package { ["git", "curl", "nfs-common"]:
        ensure => latest,
    }
}

class mysql {
    service { "mysql":
        ensure => running,
        require => Package["mysql-server"],
    }

    mysqldb { "symfony":
        user => "symfony",
        password => "Shub9aiJ"
    }

    package { ["mysql-client", "mysql-server"]:
        ensure => latest,
    }

    package { ["php5-mysql"]:
        ensure => latest,
        require => Package["libapache2-mod-php5", "mysql-client"],
        notify => Service["apache2"],
    }

}

define mysqldb( $user, $password ) {
    exec { "create-${name}-db":
        unless => "/usr/bin/mysql -u${user} -p${password} ${name}",
        command => "/usr/bin/mysql -uroot -p$mysql_password -e \"CREATE DATABASE ${name}; GRANT ALL ON ${name}.* TO ${user}@localhost IDENTIFIED BY '$password'; GRANT ALL ON ${name}.* TO ${user}@'%' IDENTIFIED BY '$password'; GRANT ALL ON ${name}.* TO root@'%'; FLUSH PRIVILEGES;\"",
        require => Service["mysql"],
    }
}

