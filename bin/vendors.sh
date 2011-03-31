#!/bin/sh

DIR=`php -r "echo dirname(dirname(realpath('$0')));"`
VENDOR="$DIR/vendor"
VERSION=`cat "$DIR/VERSION"`
PROTOCOL='git'

# initialization
if [ "$1" = "--reinstall" -o "$2" = "--reinstall" -o "$3" = "--reinstall" ]; then
    rm -rf $VENDOR
fi

# just the latest revision
CLONE_OPTIONS=''
if [ "$1" = "--min" -o "$2" = "--min" -o "$3" = "--min" ]; then
    CLONE_OPTIONS='--depth 1'
fi

# just the latest revision
if [ "$1" = "--https" -o "$2" = "--https" -o "$3" = "--https" ]; then
    PROTOCOL='https'
fi

mkdir -p "$VENDOR" && cd "$VENDOR"

##
# @param destination directory (e.g. "doctrine")
# @param URL of the git remote (e.g. git://github.com/doctrine/doctrine2.git)
# @param revision to point the head (e.g. origin/HEAD)
#
install_git()
{
    INSTALL_DIR=$1
    SOURCE_URL=$2
    REV=$3

    if [ -z $REV ]; then
        REV=origin/HEAD
    fi

    if [ ! -d $INSTALL_DIR ]; then
        git clone $CLONE_OPTIONS $SOURCE_URL $INSTALL_DIR
    fi

    cd $INSTALL_DIR
    git fetch origin
    git reset --hard $REV
    cd ..
}

# Assetic
install_git assetic $PROTOCOL://github.com/kriswallsmith/assetic.git #v1.0.0alpha1

# Symfony
install_git symfony $PROTOCOL://github.com/symfony/symfony.git #v$VERSION

# Update the bootstrap files
$DIR/bin/build_bootstrap.php

# Doctrine ORM
install_git doctrine $PROTOCOL://github.com/doctrine/doctrine2.git 2.0.3

# Doctrine DBAL
install_git doctrine-dbal $PROTOCOL://github.com/doctrine/dbal.git 2.0.3

# Doctrine Common
install_git doctrine-common $PROTOCOL://github.com/doctrine/common.git 2.0.1

# Swiftmailer
install_git swiftmailer $PROTOCOL://github.com/swiftmailer/swiftmailer.git origin/4.1

# Twig
install_git twig $PROTOCOL://github.com/fabpot/Twig.git v1.0.0

# Twig Extensions
install_git twig-extensions $PROTOCOL://github.com/fabpot/Twig-extensions.git

# Zend Framework Log
mkdir -p zend-log/Zend
cd zend-log/Zend
install_git Log $PROTOCOL://github.com/symfony/zend-log.git
cd ../..

# SensioFrameworkExtraBundle
mkdir -p bundles/Sensio/Bundle
cd bundles/Sensio/Bundle
install_git FrameworkExtraBundle $PROTOCOL://github.com/sensio/SensioFrameworkExtraBundle.git
cd ../../..

# SecurityExtraBundle
mkdir -p bundles/JMS
cd bundles/JMS
install_git SecurityExtraBundle $PROTOCOL://github.com/schmittjoh/SecurityExtraBundle.git
cd ../..

# WebConfiguratorBundle
mkdir -p bundles/Symfony/Bundle
cd bundles/Symfony/Bundle
install_git WebConfiguratorBundle $PROTOCOL://github.com/symfony/WebConfiguratorBundle.git
cd ../../..

# Update assets
$DIR/app/console assets:install $DIR/web/
