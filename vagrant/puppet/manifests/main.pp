class apt_update {

 file { "dotdeb.list":
        path => "/etc/apt/sources.list.d/dotdeb.list",
        ensure => file,
        owner => "root",
        group => "root",
        content => "deb http://ftp.ch.debian.org/debian squeeze main contrib non-free\ndeb http://packages.dotdeb.org squeeze all\ndeb-src http://packages.dotdeb.org squeeze all\ndeb http://packages.dotdeb.org squeeze-php54 all\ndeb-src http://packages.dotdeb.org squeeze-php54 all",

        notify => Exec["aptGetUpdate"],
    }


    exec { "aptGetUpdate":
        command => "wget -q -O - http://www.dotdeb.org/dotdeb.gpg | sudo apt-key add - && sudo apt-get update",
        path => ["/bin", "/usr/bin"]
    }
}

class apache {
    package { "apache2-mpm-prefork":
        ensure => present,
        require => Exec["aptGetUpdate"]
    }

   package { "libapache2-mod-php5":
        ensure => present,
        require => Package["apache2-mpm-prefork"]
    }

    service { "apache2":
        ensure => running,
        require => Package["apache2-mpm-prefork"],
        subscribe => File["main-vhost.conf", "httpd.conf", "mod_rewrite", "mod_actions"]
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

    file { "mod_actions":
        path => "/etc/apache2/mods-enabled/actions.load",
        ensure => "link",
        target => "/etc/apache2/mods-available/actions.load",
        require => Package["apache2-mpm-prefork"]
    }

    file { "mod_actions_conf":
        path => "/etc/apache2/mods-enabled/actions.conf",
        ensure => "link",
        target => "/etc/apache2/mods-available/actions.conf",
        require => Package["apache2-mpm-prefork"]
    }
}

class php54 {


    package { "php5-cli":
        ensure => present,
    }

    package { "php5-apc":
        ensure => present,
        require => Package["libapache2-mod-php5"]
    }

    package { "php5-xdebug":
        ensure => present,
        require => Package["libapache2-mod-php5"]
    }

    package { "php5-intl":
        ensure => present,
        require => Package["libapache2-mod-php5"]
    }

   file { "php-timezone.ini":
        path => "/etc/php5/cli/conf.d/30-timezone.ini",
        ensure => file,
        content => template('default/php-timezone.ini'),
        require => Package["php5-cli"]
    }




}

class symfony {

    exec { "vendorsInstall":
        cwd => "/vagrant",
        command => "php composer.phar install",
        path => ["/bin", "/usr/bin"],
        creates => "/vagrant/vendor",
        require => Exec["composerPhar"],
    }

}

class composer {
    exec { "composerPhar":
        cwd => "/vagrant",
        command => "curl -s http://getcomposer.org/installer | php",
        path => ["/bin", "/usr/bin"],
        creates => "/vagrant/composer.phar",
        require => Package["php5-cli","curl"   ],
    }


}

class groups {
    group { "puppet":
        ensure => present,
    }
}

class otherstuff {
     package { "git":
        ensure => present,
    }
     package { "curl":
        ensure => present,
    }
    package {"nfs-common":
        ensure => present,
    }
}



include apt_update
include otherstuff
include apache
include php54
include groups
include composer
include symfony
