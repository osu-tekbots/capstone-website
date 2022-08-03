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
  
## User Structure
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

