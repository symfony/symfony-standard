Symfony Standard Edition with Vagrant
=====================================

You can easily setup a development environment for the Symfony Standard Edition
by using [Vagrant][1].

Prerequisites
-------------

1. Download and install the latest [Virtualbox][2].
2. Download and install the latest [Vagrant][3].
3. Install the Symfony Standard Edition as detailed in the main README.md file.

Setup
-----

After installing the Symfony Standard Edition, execute the following commands:

    cd vagrant
    vagrant up

A virtual machine is now being prepared in Virtualbox by Vagrant. When the
process has completed, you can view the Symfony demo site in a browser at:

<http://192.168.33.10/app_dev.php>

Now you can start developing with Symfony! Any changes made to your Symfony
project directory will appear in the virtual machine.

Further Configuration
---------------------

A MySQL database has been created on the Vagrant virtual machine which you can
use. Just update your app/config/parameters.yml file:

    parameters:
        database_driver:   pdo_mysql
        database_host:     127.0.0.1
        database_port:     ~
        database_name:     symfony
        database_user:     symfony
        database_password: symfony

The database name, user, and password are "symfony".

Other Vagrant Commands
----------------------

While you are in the "vagrant" directory, you can perform other commands.

If you need to access the virtual machine command line, execute:

    vagrant ssh

If you need to refresh the virtual machine, execute:

    vagrant reload

If you are done developing and want to remove the virtual machine, execute:

    vagrant destroy

And if you want to install again after destroying, execute:

    vagrant up

Enjoy!

[1]: http://www.vagrantup.com
[2]: https://www.virtualbox.org/wiki/Downloads
[3]: http://downloads.vagrantup.com/
