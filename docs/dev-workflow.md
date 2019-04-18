# Development Workflow
This document contains information about the development workflow of the software for the Senior Design Capstone
website.

## Development Workflow and GitHub
Essentially there are three locations of the website:

1. **Local**: this is where current development occurs.
1. **Staging**: this is where the legacy development occurred and is located on the ENGR server.
1. **Production**: this is the live site on the ENGR server.

The workflow for making changes to the source is as follows:

1. Pull the latest version from `master`. Make changes locally on a separate branch. Convention is to include your 
   name in the branch name.
1. Push the changes to a remote copy of the branch. Make sure to adjust configuration for the staging server in the 
   commit before merging in the next step.
1. Submit a pull request to the `stage` branch.
1. Once the pull request has been accepted and merged, pull and test the changes at 
   [the dev site on ENGR servers](http://eecs.oregonstate.edu/education/capstone/newcapstone/)
1. Once the changes have been tested on the dev site, make final configuration changes and save them on the 
   `stage` branch
1. Create a pull request to merge the changes from `stage` into `master`. Merge the changes.
1. Pull the latest changes from `master` to [the production site](http://eecs.oregonstate.edu/capstone/submission/)

Before continuing development on your branch, make sure to pull again from the master branch to get an up to date
copy of the master branch (including merge request commit logs).

<img src="https://drive.google.com/uc?id=1o_GVzpQ0bLwZqK4bUJFDYVR0NepNy-HM" alt="dev workflow visualized"
    style="width: 700px; display: block; margin: auto;"/>

## Masquerading to Develop
Since we cannot use our authentication providers from `localhost`, we will need create a user manually to use while
developing. This is beneficial in many ways, one of which being that it allows us to explore the site as a user, a
proposer, and an admin without needing to log in with credentials for different users.

1. "Exec" into the container running the website. This opens an interactive shell inside the container.

       docker exec -it osu-local-web-server /bin/bash

1. Navigate to the `scripts` directory of the repository.

       cd /var/www/html/scripts

1. Execute the `create-user.php` script. Follow the prompts to give the user an ONID, a name, and determine its type.

       php create-user.php

1. Once the user has been created, navigate to the masquerade page of the site in your browser. If you are running the
   `osu-local-web-server` container on port 8000, the URL will look like this: http://localhost:8000/masq/index.php

1. You will be prompted to enter the username and password that you configured in 
   [the development setup](./dev-setup.md)

1. On the ensuing page, enter the ONID of the user you created and click "Start Masquerading". This will "log you in"
   as the user and let you see the site through their eyes.

1. At any point to stop masquerading, return to the masquerade page and click the "Stop" button.