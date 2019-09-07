# Senior Design Capstone Web Application

![image](https://user-images.githubusercontent.com/20714895/59056597-68958700-884d-11e9-9879-7158ca8a879a.png)

Senior Design Capstone is an application that enables students to browse Senior Design projects proposed by 
individuals from industry and other sponsors. See [the changelog](./CHANGELOG.md) for detailed information about
updates to the website.

**Initial Development**: Winter Term 2019 - Spring Term 2019

**Contributors**
- Symon Ramos (ramossy@oregonstate.edu)
- Thien Nam (namt@oregonstate.edu)
- Braden Hitchcock (hitchcob@oregonstate.edu)

## Development
The following resources provide information about how to develop the website locally and the workflow for pushing
changes to the staging area and subsequently deploying them to production.

- [Local Development Setup](./docs/dev-setup.md)
- [Development Workflow](./docs/dev-workflow.md)

In addition, **create a pre-commit hook** that will ensure fill permissions are set accordingly before you commit
code. To do this, copy the `scripts/pre-commit.sh` file and save it as `pre-commit` in your local `.git/hooks`
directory. Also ensure it is executable.

```sh
cp scripts/pre-commit.sh .git/hooks/pre-commit
chmod a+x .git/hooks/pre-commit
```

## Configuration
### Database
There should be an INI file located in the private files for this site (not in the repository) with the following
contents:

```ini
host = 
user =
password = 
db_name = 
```

### Server
Server configuration is now inside of a `config.ini` file at the root of the repository. This file is **NOT** to be
checked into source control. The file should have the following contents:

```ini
; All files referenced through the configuration are relative to this private path
private_files = ; directory containing private files (outside the web root)

[server]
display_errors = ; yes|no
display_errors_severity = ; all|warning|error
auth_providers_config_file = ; auth.ini

[email]
subject_tag = ; optional tag to prepend all email subjects with
from_address = ; main from address used when sending email from the server
admin_addresses[] = ; array of email addresses of admins that need important site notifications

[client]
base_url = ; base URL used by the frontend (e.g. http://eecs.oregonstate.edu/capstone/)

[logger]
log_file = ; out.log, or another name pointing to the log file
level = ; trace|info|warn|error

[database]
config_file = ; database.ini, or another name pointing to the database configuration file (see above for contents)
```

The `.htaccess` file has also been removed from the repository to further simplify configuration and is being ignored
by Git. When used, place the `.htaccess` file at the root of the repository with the following configuration:

```ini
# Deny access to files with specific extensions
<FilesMatch "\.(ini|sh|sql)$">
Order allow,deny
Deny from all
</FilesMatch>

# Deny access to filenames starting with dot(.)
<FilesMatch "^\.">
Order allow,deny
Deny from all
</FilesMatch>

RewriteEngine On

RewriteBase <CHANGEME>

# If the requested file is not a directory or a file, we need to append .php
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} (pages|auth|api|masq)/
RewriteRule ^([^\.]+)$ $1.php [NC,L]

# Prepend `pages/` to the URI if it needs it
RewriteCond %{REQUEST_URI} !/(api|assets|images|auth|pages|masq)
RewriteRule ^(.*)$ pages/$1
```

Notice the `<CHANGEME>` text above. This should be changed to be the root URI of the website hosting the application.
For example, if the website is hosted at `http://eecs.oregonstate.edu/education/capstone/`, then you would replace
`<CHANGEME>` with `/education/capstone/`. **The trailing and leading slashes are required**.

## Structural Overview
- All HTML pages are rendered inside of PHP files in the `pages/` folder.

- All database management is handled by database access objects in the `lib/classes/DataAccess/` and 
  `lib/shared/classes/DataAccess/` directories. Any additional queries required to accomplish site functionality
  should be included in these DAOs (or in a new DAO in the same namespace/file location).

- All database configuration is located in a private directory *outside this repository* in a `database.ini` file.

- Third-party authentication provider IDs and secrets are located *outside this repository* in a `auth.ini` file.

- All external CSS and JS files are located in the `assets/css/` and `assets/js/` respectively. An internal CSS 
  file called `assets/css/capstone.css` contains customized CSS proporties relevant to this application.

   > Please be aware that this CSS file is global and will modify the entire application to adhere to its standards. 
   > (EX: modifying the background color of the "body" element will modify all "body" elements of all pages, not just
   > a single one.) Please create new classes whenever applicable.

- The `modules/header.php` file contains all references to external CSS and JS files. The `header.php` and 
  `footer.php` files should be included in all files in the `pages/` directory.
  
- The `modules/` folder contains encapsulated code that is shared between multiple files in the `pages/` folder. 
  Whenever possible , please consolidate duplicate functionality into a single module or folder. For example, the 
  `modules/cards.php` will contain functions utilized in `pages/browseProjects.php` and 
  `pages/myProjects.php` to render project cards with different attributes.
  

## User Roles
**Proposers**
1. create new projects.
2. edit projects.
3. submit projects for approval.
4. review student applications.

**Students**
1. browse projects.
2. apply for projects that are interesting to them. 
3. have proposer functionality per user design.

**Admins**
1. have proposer functionality.
2. can edit any project.
3. approve or deny submitted projects for public view.
4. grant other users admin functionality.
5. assign categories (EX: CS, ECE) to projects.

## Database Architecture

Authentication data is located in a `database.ini` file **outside this repository**. The Tekbots Web Dev Team's shared
Google Drive contains documentation on the internal structure of database tables used in this site.

Database Name: `eecs_projectsubmission`
Server Name: `engr-db Groups`

## Login Authentication
Within `pages/login.php`, the `auth/[authenticator].php` script is executed on login button click. 
Login credentials required to interface with the authenticator are:
- redirect_uri
- client_id
- client_secret

Each authenticator will provide different user info configurations but will have sufficient data needed to create a 
new user. All new users are defaulted as Students and are re-directed to `pages/login.php` with a new portal section.

Users must contact an administrator of this application in order to be given the access level of admin.


## Session Variables
Session variables are used to persist user data throughout the course of a user's active session. The instantiation 
of these variables occur in the following workflow:
  
1. The user visits the `pages/login.php` page. 
2. The user selects a login authentication type (EX: Google, Microsoft).
3. After successful authentication, the following session variables are instantiated and can be used in PHP throughout the entire application: 
   - `$_SESSION['userID']`: This variable is a string of numbers. 
   - `$_SESSION['accessLevel']`: This variable is a string that can be either: 
      - "Student"
      - "Proposer"
      - "Admin"
   - `$_SESSION['newUser']`: This variable is a boolean (either true or false).

> **NOTE**: Please do NOT reference `$_SESSION['userID']` in javascript, as Google Authentication may provide a 
> userID that is longer than the acceptable max character length for javascript. Instead, echo the session varible in a 
> hidden div and reference that text of that div in order to use the userID in JavaScript.


## Future Implementation
- Ability for admins to assign students to projects.
- Github Login Authentication Support.
- Mobile Support.

# Current Migration onto official capstone site (eecs.oregonstate.edu/capstone/submission)
1. Push all changes from STAGE directory (education/capstone/stage) to github for version maintence
2. Clear all files from official capstone directory EXCEPT
   - images dir
   - .htaccess
   - .config.ini
3. Copy everything over from STAGE directory EXCEPT
   - config.ini
   - .git dir
   - .gitignore

Future Implementation: Move everything to a github branch and set up .gitignore for unecessary files and just git pull for new changes.

## Troubleshooting and Helpful Notes

### Problem
The `u_uap_provided_id` columns in the database are `VARCHAR(256)` and because Google Authentication returns an ID that 
is often times more than 64 bits, the session variable for userID can't be explicitly referenced in Javascript and will 
be truncated.
  
#### Solution 
Create a hidden div and echo out the SESSION variable there. Then reference that div in the javascript. Found in 
`pages/viewSingleProject.php`: 
		 
## Screenshots 

![image](https://user-images.githubusercontent.com/20714895/59056636-806d0b00-884d-11e9-8a94-606cb1e5f667.png)

![image](https://user-images.githubusercontent.com/20714895/59057000-43eddf00-884e-11e9-833a-ad1d8b329c7a.png)

![image](https://user-images.githubusercontent.com/20714895/59057030-55cf8200-884e-11e9-8937-fd465a732039.png)

![image](https://user-images.githubusercontent.com/20714895/59057421-2e2ce980-884f-11e9-83ad-6035f7787e94.png)

