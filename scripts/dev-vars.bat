@ECHO OFF

rem Dev Environment Credentials (DO NOT CHANGE DB_USERNAME)
set DB_USERNAME=root
set DB_PASSWORD=1234
set DB_NAME=osulocaldev

rem ONID User for Masquerading
set ONID_USERNAME=jonesb
set ONID_FIRST=Bob
set ONID_LAST=Jones
set ONID_ACCOUNT_TYPE=3

rem Default Docker images to use
set MYSQL_IMAGE=mariadb:10.3
set PHP_MY_ADMIN_IMAGE=phpmyadmin/phpmyadmin
set APACHE_PHP_IMAGE=osu-apache-php

rem Default container names
set MYSQL_CONTAINER_NAME=osu-mysql-db
set PHP_MY_ADMIN_CONTAINER_NAME=osu-mysql-admin
set APACHE_PHP_CONTAINER_NAME=osu-local-web-server

rem Default Docker bridge network name
set NETWORK_NAME=osu-local-dev-net

rem Default port mappings
set MYSQL_LOCAL_PORT=3306
set PHP_MY_ADMIN_LOCAL_PORT=5000
set APACHE_PHP_LOCAL_PORT=7000

rem Folder locations (DO NOT CHANGE)
set PUBLIC_FOLDER=%ROOT_FOLDER%\src\public
set PRIVATE_FOLDER=%ROOT_FOLDER%\src\private
set APACHE_DOCKERFILE_FOLDER=%ROOT_FOLDER%\scripts\docker\%APACHE_PHP_IMAGE%