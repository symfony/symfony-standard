# update system first before new packages are installed
class { 'apt':
    always_apt_update => true,
}
Exec['apt_update'] -> Package <| |>


# install Apache
class { 'apache': }
class { 'apache::mod::php': }


# install MySQL
class { 'mysql': }
class { 'mysql::server':
    config_hash => { 'root_password' => 'symfony' },
}
class { 'mysql::php': }


# install Git for composer
class { 'git': }


# install PHP Extensions used with Symfony
class php-extensions {
    package { ['php-apc', 'php5-intl', 'php5-xdebug']:
        ensure  => latest,
        require => Package['httpd'],
        notify  => Service['httpd'],
    }
}

include php-extensions


# install a local composer.phar file
class composer {
    exec { 'composerPhar':
        cwd     => '/vagrant',
        command => 'curl -s http://getcomposer.org/installer | php',
        path    => ['/bin', '/usr/bin'],
        creates => '/vagrant/composer.phar',
        require => [ Class['apache::mod::php', 'git'], Package['curl'] ],
    }

    package { 'curl':
        ensure => present,
    }
}

include composer


# install the Symfony vendors using composer
class symfony {
    exec { 'vendorsInstall':
        cwd       => '/vagrant',
        command   => 'php composer.phar install',
        timeout   => 1200,
        path      => ['/bin', '/usr/bin'],
        creates   => '/vagrant/vendor',
        logoutput => true,
        require   => Exec['composerPhar'],
    }
}

include symfony


# Create a web server host using the Symfony web/ directory
apache::vhost { 'www.symfony.local':
    priority      => '10',
    port          => '80',
    docroot_owner => 'vagrant',
    docroot_group => 'vagrant',
    docroot       => '/vagrant/web/',
    logroot       => '/vagrant/app/logs/',
    serveraliases => ['symfony.local',],
}

# Create a database for Symfony
mysql::db { 'symfony':
    user     => 'symfony',
    password => 'symfony',
    host     => 'localhost',
    grant    => ['all'],
}


# Configure Apache files to run as the "vagrant" user so that Symfony app/cache
# and app/logs files can be successfully created and accessed by the web server

file_line { 'apache_user':
    path    => '/etc/apache2/httpd.conf',
    line    => 'User vagrant',
    require => Package['httpd'],
    notify  => Service['httpd'],
}

file_line { 'apache_group':
    path    => '/etc/apache2/httpd.conf',
    line    => 'Group vagrant',
    require => Package['httpd'],
    notify  => Service['httpd'],
}


# Configure php.ini to follow recommended Symfony web/config.php settings

file_line { 'php5_apache2_short_open_tag':
    path    => '/etc/php5/apache2/php.ini',
    match   => 'short_open_tag =',
    line    => 'short_open_tag = Off',
    require => Class['apache::mod::php'],
    notify  => Service['httpd'],
}

file_line { 'php5_cli_short_open_tag':
    path    => '/etc/php5/cli/php.ini',
    match   => 'short_open_tag =',
    line    => 'short_open_tag = Off',
    require => Class['apache::mod::php'],
    notify  => Service['httpd'],
}

file_line { 'php5_apache2_date_timezone':
    path    => '/etc/php5/apache2/php.ini',
    match   => 'date.timezone =',
    line    => 'date.timezone = UTC',
    require => Class['apache::mod::php'],
    notify  => Service['httpd'],
}

file_line { 'php5_cli_date_timezone':
    path    => '/etc/php5/cli/php.ini',
    match   => 'date.timezone =',
    line    => 'date.timezone = UTC',
    require => Class['apache::mod::php'],
    notify  => Service['httpd'],
}

file_line { 'php5_apache2_xdebug_max_nesting_level':
    path    => '/etc/php5/apache2/conf.d/xdebug.ini',
    line    => 'xdebug.max_nesting_level = 250',
    require => [ Class['apache::mod::php'], Package['php5-xdebug'] ],
    notify  => Service['httpd'],
}

file_line { 'php5_cli_xdebug_max_nesting_level':
    path    => '/etc/php5/cli/conf.d/xdebug.ini',
    line    => 'xdebug.max_nesting_level = 250',
    require => [ Class['apache::mod::php'], Package['php5-xdebug'] ],
    notify  => Service['httpd'],
}


# Configure Symfony dev controllers so that the Vagrant host machine at the
# host_ipaddress (specified in the Vagrantfile) has access

file_line { 'symfony_web_config_host_ipaddress':
    path  => '/vagrant/web/config.php',
    match => '::1',
    line  => "    '::1', '${::host_ipaddress}',",
}

file_line { 'symfony_web_app_dev_host_ipaddress':
    path  => '/vagrant/web/app_dev.php',
    match => '::1',
    line  => "    || !in_array(@\$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1', '${::host_ipaddress}'))",
}
