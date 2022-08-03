## Login Authentication
Within `pages/login.php`, the `auth/[authenticator].php` script is executed on login button click. 
Login credentials required to interface with the authenticator are:
- redirect_uri
- client_id
- client_secret

Each authenticator will provide different user info configurations but will have sufficient data needed to create a 
new user. All new users are defaulted as Students and are re-directed to `pages/login.php` with a new portal section.

Users must contact an administrator of this application in order to be given the access level of admin.
