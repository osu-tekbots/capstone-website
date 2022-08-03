
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
Server configuration is now inside of a `config.ini` file at the public folder of the web server. This file is **NOT** to be checked into source control. The file should have the following contents:

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
