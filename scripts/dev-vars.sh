#!/bin/bash
if [ -z "${ROOT_FOLDER}" ]; then
    echo "Error: The ROOT_FOLDER environment variable is not set.";
    echo "       The dev-vars.sh script should be sourced by dev-setup.sh";
    echo "       Run dev-setup.sh instead of running dev-vars.sh directly.";
    exit 1;
fi

# Dev Environment Credentials
export DB_USERNAME="root"; # Do not change
export DB_PASSWORD="1234";
export DB_NAME="osulocaldev";

# ONID User for Masquerading
export ONID_USERNAME=jonesb
export ONID_FIRST=Bob
export ONID_LAST=Jones
export ONID_ACCOUNT_TYPE=3



# Default Docker images to use
export MYSQL_IMAGE="mariadb:10.3";
export PHP_MY_ADMIN_IMAGE='phpmyadmin/phpmyadmin';
export APACHE_PHP_IMAGE='osu-apache-php';

# Default container names
export MYSQL_CONTAINER_NAME='osu-mysql-db';
export PHP_MY_ADMIN_CONTAINER_NAME='osu-mysql-admin';
export APACHE_PHP_CONTAINER_NAME='osu-local-web-server';

# Default Docker bridge network name
export NETWORK_NAME='osu-local-dev-net';

# Default port mappings
export MYSQL_LOCAL_PORT=3306;
export PHP_MY_ADMIN_LOCAL_PORT=5000;
export APACHE_PHP_LOCAL_PORT=7000;

# Folder locations
export PUBLIC_FOLDER="${ROOT_FOLDER}/src/public"; # Do not change
export PRIVATE_FOLDER="${ROOT_FOLDER}/src/private"; # Do not change
export APACHE_DOCKERFILE_FOLDER="${ROOT_FOLDER}/scripts/docker/${APACHE_PHP_IMAGE}"; # Do not change
