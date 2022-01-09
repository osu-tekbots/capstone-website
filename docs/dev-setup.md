# Setup Instructions
These instructions step through the process of setting up a development environment using docker. 

## Before we begin
There is a configuration file where some of the development parameters can be configured.

The [dev-vars.sh](../scripts/dev-vars.sh) script contains a number of variables that will be populated by executing the setup instructions. 

The variables you may be mo

## Linux
### 1. Install Git and Docker
Git will be used to download the repository, and docker will be used to host the LAMP stack.

The instructions may differ if your distribution uses a different package manager, or if your distribution doesn't use systemctl.

First, Install git and docker 
``` bash
$ sudo apt update
$ sudo apt install git docker
```
Next, add your user account to the docker group.
``` bash
$ sudo usermod -aG docker $USER
```
Next, start and enable the docker service.
``` bash
$ sudo systemctl enable docker
$ sudo systemctl start docker
```

**Finally, REBOOT YOUR COMPUTER.**

This will ensure that your user account has been successfully added to the docker user group.

### 2. Clone the repo
Clone this repo to your computer
``` bash
$ git clone https://github.com/osu-tekbots/capstone-website.git
```

### 3. Run the setup script
In the [scripts](../scripts) folder, launch the setup.sh script.
``` bash
$ capstone-website/dev-setup.sh
```
If you receive a `permission denied` error, you may need to add the execute bit to the setup script in order to execute it.
``` bash
$ chmod +x capstone-website/dev-setup.sh
$ capstone-website/dev-setup.sh
```

### 4. Wait for completion
Wait until the setup is complete. This should take < 2 minutes

To verify that the setup has completed, you can execute `docker ps` to see the docker containers.
``` bash
$ docker ps
CONTAINER ID   IMAGE                           COMMAND                  CREATED        STATUS        PORTS                                       NAMES
50638a1060d0   osu-apache-php                  "docker-php-entrypoi…"   4 hours ago    Up 4 hours    0.0.0.0:7000->80/tcp, :::7000->80/tcp       osu-local-web-server
1ee2261496af   phpmyadmin/phpmyadmin           "/docker-entrypoint.…"   4 hours ago    Up 4 hours    0.0.0.0:5000->80/tcp, :::5000->80/tcp       osu-mysql-admin
c602322539ac   mariadb:10.3                    "docker-entrypoint.s…"   4 hours ago    Up 4 hours    0.0.0.0:3306->3306/tcp, :::3306->3306/tcp   osu-mysql-db

```
All three of these docker containers should be present with their relevent port forwards.

- osu-local-web-server (0.0.0.0:7000->80/tcp)
- osu-mysql-admin (0.0.0.0:5000->80/tcp)
- osu-mysql-db (0.0.0.0:3306->3306/tcp)

Now, available at http://localhost:7000 you should be able to access a development version of the website. 

Furthermore, the output of step 3 will show you the details for the auto-generated development ONID account that can be used for masquerading around the website. Enter the ONID username on the http://localhost:7000/masq endpoint to masquerade as that ONID user.

### 5. Make changes
To modify the website, make changes in the [src/public](../src/public) folder. 
``` bash
$ echo "<html>hello world</html>" >> capstone-website/src/public/index.html
```

Some changes (like changes to the SQL schema) will require that the software stack be re-setup. Re-run the [setup script](../scripts/dev-setup.sh) to automatically teardown and rebuild the software stack.
``` bash
$ capstone-website/dev-setup.sh
```

## Windows

TODO: Create setup script for windows and add instructions here.