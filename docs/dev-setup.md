# Development Environment Setup
This document outlines how to setup development locally using Docker containers and the development workflow. All
examples assume development on a UNIX operating system or from a Bash shell.

1. Install [Docker CE](https://docs.docker.com/install/). If on Linux, you should also add your user to the `docker`
   group so that you don't have to use `sudo` in front of the `docker` command. Instructions can be found
   [here](https://docs.docker.com/install/linux/linux-postinstall/).

1. Create a new folder to house the project (referred to as the *project root* in the docs).

    ```sh
    mkdir $HOME/Work/capstone-website
    ```

1. Create `private` folder to house things like database and authentication information.

    ```sh
    cd $HOME/Work/capstone-website
    mkdir private
    ```

1. Clone the contents of the capstone website repository into a folder called `public`. (Example uses SSH)

    ```sh
    git clone git@github.com:osu-tekbots/capstone-website.git public/
    ```

1. Now that we have the website source, we need the scripts that will help us setup the development environment. These
   are located in a different repository. Outside of the project root, clone the [osu-tekbots/container-dev-env] 
   repository. (Example uses SSH)

    ```sh
    cd $HOME/Work
    git clone git@github.com:osu-tekbots/container-dev-env.git
    ```

1. From the `container-dev-env` repository, run the setup script. Running the script without any arguments will
   output usage information. Note that you must provide the *absolute* path to the public and private files in the
   project root for the script to work properly.

    ```sh
    cd container-dev-env
    sh dev-setup.sh /home/<user>/Work/capstone-website/public /home/<user>/Work/capstone website
    ```

    Replace the `<user>` placeholder with the username

1. In the `public` directory of the project root, add a `config.ini` file that follows the format defined in the
   [README](../README.md) of this repository.

    ```sh
    cd $HOME/Work/capstone-website/public
    touch config.ini
    # Add configuration to the file
    ```

1. In the `private` directory of the project root, add a `database.ini` file that has the database authentication
   information provided in the final output of the `dev-setup.sh` script. Also add an `out.log` file with world write
   permissions. You should also create an empty `auth.ini` file to avoid unexpected errors, even though we will not
   be needing the contents of the file during development.

    ```sh
    cd $HOME/Work/capstone-website/private
    touch database.ini
    # Add database configuration
    touch out.log
    chmod a+w out.log
    touch auth.ini
    ```

1. We need to setup the database schema so that we have all the tables and appropriate enumerations available
   before we start developing.     

1. Now things get a little tricky! The website uses CAS and OAuth2 to authenticate users, but we can't do that from
   out local development environment because the authentication endpoints do not accept requests from `localhost`.
   There is masquerading functionality provided for development under the `masq` directory. In order to use it, we
   have to create users using the `create-user.php` script from *inside* the container we started.

    ```sh
    # Exec into the container
    docker exec -it osu-local-web-server /bin/bash
    $ cd scripts
    $ php create-user.php
    ```

1. We can now use the fake ONID we gave the newly-created user to masquerade as a user while developing the site.

    1. Navigate to http://localhost:7000/masq/ (NOTE: the port may be different if you configured your own variables
       before running the setup script)
    1. Enter the ONID of the user you created and click "Start Masquerading".
    1. If successful, you should be redirected to the home page logged in as the user you just created.

    You can create as many users with different permissions as you see fit.

You're development environment is now set up! The `config.ini` file is not tracked by Git, so any modifications made
to it will not be pushed to source control.

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
> `dev-setup` script, you will need to run the `dev-setup` script again.

[osu-tekbots/container-dev-env]: https://github.com/osu-tekbots/container-dev-env