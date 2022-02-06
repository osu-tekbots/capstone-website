# Frequently Asked Questions


## How do I setup a development environment?
The instructions are outlined in the [dev-setup](./dev-setup.md) guide.

## How do I sign into a user account in the development environment?
The development environment allows one to masquerade as one of the pre-defined user accounts. The [setup script](../scripts/dev-setup.sh) defines multiple user accounts to masquerade with (admin, proposer, student, and a custom user defined in the [vars file](../scripts/dev-vars.sh).

In order to masquerade as one of these users, visit http://localhost:7000/masq and enter the ONID of the account you would like to masquerade as.

## How do I create additional user accounts to masquerade as?

After launching the docker containers, the apache web server can be used to execute a PHP script for creating new onid accounts.

``` bash
docker exec -it osu-local-web-server /bin/bash -c "cd /var/www/html/scripts; php create-user.php"
```

You can also pre-define the account details when you execute the php script.
``` bash
docker exec -it osu-local-web-server /bin/bash -c "cd /var/www/html/scripts; php create-user.php 'admin' 'admin_first' 'admin_last' 3"
```

The value indicies are as follows
1. ONID Username
2. First Name
3. Last Name
4. Account Type (0=Student, 1=Proposer, 2=Admin)

## How do I switch the account I am masquerading as?

Visit http://localhost:7000/masq and click the `stop` butotn.