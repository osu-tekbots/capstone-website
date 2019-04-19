# Development Environment Setup
This document outlines how to setup development locally using Docker containers and the development workflow.

> **NOTE**: Setup will require that you have access to some private files and passwords not included in the repository.
> Contact the Tekbots Web Dev Team for more information.

## TL;DR
From the [osu-tekbots/container-dev-env] repository.

```sh
sh dev-setup.sh /path/to/private/files /path/to/capstone/repository/root
```

After following setup below, the directory structure inside the container should look like the following (use
`docker exec -it osu-local-web-server /bin/bash` to interact with the web server container):

```sh
/var/www/
  |- # Private files (outside the repository). Mounted from a host directory.
  |- .htpasswd
  |- auth.ini
  |- database.ini
  |- out.log
  |- html/
       |- # Public files (the Git repository root). Mounted from a host directory.
       |- ...
       |- .htaccess # Update path to bootstrap.php to /var/www/html/bootstrap.php
       |- config/
            |- ...
            |- site.ini # Mode is set to 'local'
            |- local.ini # Configuration set for local development
       |- masq/
            |- ...
            |- .htaccess # Location to password file set to /var/www/.htpasswd
```

You can stop and start your development environment at anytime without having to repeat the whole process with the 
following:

```sh
sh dev-stop.sh
sh dev-start.sh
```

## Local Development Setup
We are able to use containerization with [Docker](https://www.docker.com/) to allow for local development on 
individual laptops (i.e. off the ENGR servers). Before completing the below steps, **make sure Docker is installed**
on your local development machine. You can follow instructions [here][osu-tekbots/container-dev-env] for installing
Docker on your machine.

1. Make sure you have the necessary private files locally on your machine. These include:
   - `auth.ini`: OAuth client IDs and secrets used by the application. This file could be empty, but it needs to
      exist in the private directory.
   - `out.log`: the output log file for the logger. It needs to have write permissions enabled for all users.
     (`chmod a+w out.log`)

1. Clone the [osu-tekbots/container-dev-env] repository locally.

1. Run the `dev-setup` script from the above mentioned repository to download and build the required containers. The
   script will also start the containers for you. The example below uses the shell on Linux.

       sh dev-setup.sh /path/to/private/files /path/to/capstone/repository/root

   Once the script has finished executing, it will output the necessary configuration for access to the database. Place
   this configuration inside the private directory in `database.ini`.

1. Now we need to generate a `.htpasswd` file so that we can restrict access to the masquerading feature that allows us
   to bypass third-party authentication while we are doing development.
   1. "Exec" into the container running the website. This opens an interactive shell inside the container.

          docker exec -it osu-local-web-server /bin/bash

   1. Change into the `/var/www` directory.

          cd /var/www

   1. Run the `htpasswd` command to generate a `.htpasswd` file to use for authentication. Replace *name* and *password*
      with values of your choice. They will be the same values you provide when you navigate to the password protected
      page on the local web server.

          htpasswd -nbm name password > .htpasswd
    
    1. Make sure that the `masq/.htaccess` configuration is pointing to `/var/www/.htpasswd`

1. Finally, make sure that all of your configuration for the site is in order.
   1. Copy the `development.ini` as a `local.ini` file in the `config/` directory of the repository. Change the 
      necessary configurations.
   1. Do a search for `CONFIG` in the repository (text search, match case) and ensure that file paths are all pointing
      to the right locations.

## Active Development
The above process only needs to be completed once. After that, you can easily start/stop develpoment by using the
`dev-start` and `dev-stop` scripts provided the [osu-tekbots/container-dev-env] repository. Below are examples when
running on Linux using shell.

```sh
# Stop the containers emulating the development environment
sh dev-stop.sh

# Start the containers again
sh dev-start.sh
```

> **NOTE**: if you execute the `dev-teardown` script or in any other way destroy the containers created by the
> `dev-setup` script, you will need to repeat the process all over again.

[osu-tekbots/container-dev-env]: https://github.com/osu-tekbots/container-dev-env