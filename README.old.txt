/*********************************************************************************
* Title: Senior Design Capstone Web Application
*
* Initial Development: Winter Term 2019
*
* Contributors: Symon Ramos, Thien Nam
*
* Description: Senior Design Capstone is an application that enables students to
* browse Senior Design projects proposed by individuals from industry and other
* sponsors.
*
*********************************************************************************/

/*********************************************************************************
* Structural Overview
*********************************************************************************/
- All HTML pages are rendered inside of php files in the ./pages/ folder.

- All database management is handled in the ./db/dbManager.php file. Please maintain
  this consistency.

- The ./db/upload.php file handles uploading images to the ./images/ folder that the
  user provides.

- All external css and js files are located in the ./assets/css/ and ./assets/js/
  respectively. An internal css file called ./assets/css/capstone.css contains
  customized css proporties relevant to this application.

	- Please be aware that this css file is global and will modify the entire
	  application to adhere to its standards. (EX: modifying the background color
	  of the "body" element will modify all "body" elements of all pages, not just
	  a single one.) Please create new classes whenever applicable.

- The ./includes/config.php file contains DB and Authentication credentials.

- The ./includes/header.php file contains all references to external css and js files.
  header.php should be included in all files in the ./pages/ folder.
  
- The ./modules/mailer.php file contains all e-mail handling functions.

- The ./modules/ folder contains encapsulated code that is shared between files in the
  ./pages/ folder. Whenever possible , please deprecate duplicate functionality into
  a single module or folder. For example, the ./modules/createCards.php will contain
  functions utilized in ./pages/browseProjects.php and ./pages/myProjects.php to
  render project cards with different attributes.
  

/*********************************************************************************
* Workflow Design
*********************************************************************************/

Proposers...
	1. create new projects.
	2. edit projects.
	3. submit projects for approval.
	4. review student applications. (FUTURE IMPLEMENTATION)

Students...
	1. browse projects.
	2. apply for projects that are interesting to them. (FUTURE IMPLEMENTATION)
	3. have proposer functionality per user design.

Admins...
	1. have proposer functionality.
	2. can edit any project.
	3. approve or deny submitted projects for public view.
	4. grant other users admin functionality.
	5. assign categories (EX: CS, ECE) to projects.

/*********************************************************************************
* Database Architecture
*********************************************************************************/

/*****************************************
* As of 3/4/19                           *
*****************************************/

Authentication data is located in ./includes/config.php.

Database Name: eecs_projectsubmission
Server Name: engr-db Groups

Tables:
	/***************************************
	* keywords
	***************************************/
		- keyword_id (Primary Key, Auto Increment)
		- name

	/***************************************
	* projects
	***************************************/
		*All fields are "text" type unless otherwise stated.

		- project_id (Primary Key, Auto Increment)
		- proposer_id
		- status
		- category
		- is_hidden (tinyint boolean, defaults to 0)
		- type
		- year (int)
		- section (int)
		- title
		- website
		- video
		- additional_emails
		- start_by
		- complete_by
		- date_created (datetime, defaults to CURRENT_TIMESTAMP)
		- last_updated (datetime)
		- preferred_qualifications
		- minimum_qualifications
		- motivation
		- description
		- objectives
		- NDA/IP
		- compensation
		- comments
		- focus
		- image
		- number_groups (int)
		- keywords (comma separated string)

		Triggers:
			- ins_year (sets `year` before the insertion of a row as current year.)
			- upd_last_updated (sets `last_updated` before the updating of a row as CURRENT_TIMESTAMP)

	/***************************************
	* project_assignments
	***************************************/
		- 3/4/19: In Development

	/***************************************
	* users
	***************************************/
		- user_id (Primary Key, NOT auto incremented. Pulled from OAUTH_CLIENT authenticator.)
		- first_name
		- last_name
		- student_id
		- salutation
		- email
		- phone
		- affiliation
		- major
		- auth_provider
		- type
		- project_assigned

	/***************************************
	* users_application
	***************************************/
		- 3/4/19: In Development

	/***************************************
	* user_application_review
	***************************************/
		- 3/4/19: In Development

/*********************************************************************************
* Login Authentication
*********************************************************************************/
- Within ./pages/login.php, the ./auth_providers/login_with_[authenticator].php
  script is executed on login button click.

- Login credentials required to interface with the authenticator are:
	- redirect_uri
	- client_id
	- client_secret

- Each authenticator will provide different user info configurations but will have
  sufficient data needed to create a new user. All new users are defaulted as Students
  and are re-directed to ./pages/login.php with a new portal section.

- Users must contact an administrator of this application in order to be given the
  access level of admin.

/*********************************************************************************
* Admin Interface
*********************************************************************************/


/*********************************************************************************
* Session Variables Used
*********************************************************************************/

- Session variables are used to persist user data throughout the course of a user's 
  active session. The instantiation of these variables occur in the following workflow: 
  
  1. The user visits the ./pages/login.php page. 
  2. The user selects a login authentication type (EX: Google, Microsoft).
  3. After successful authentication, the following session variables are instantiated 
     and can be used in PHP throughout the entire application: 
		a. $_SESSION['userID']
			i. This variable is a string of numbers. 
				a. NOTE: Please do NOT reference $_SESSION['userID'] in javascript, as 
				Google Authentication may provide a userID that is longer than the acceptable
				max character length for javascript. Instead, echo the session varible in a 
				hidden div and reference that text of that div in order to use the userID in 
				javascript.
		b. $_SESSION['accessLevel']
			i. This variable is a string that can be either: 
				a. "Student"
				b. "Proposer"
				c. "Admin"
		c. $_SESSION['newUser'] 
			i. This variable is a boolean (either true or false).


/*********************************************************************************
* Future Implementation
*********************************************************************************/
- Application process for Proposers
- Application process for Users
- Refinement of Alpha Release

/*********************************************************************************
* Troubleshooting and Helpful Notes
*********************************************************************************/

- The user_id columns in the database are char(64) and because Google Authentication
  returns an ID that is often times more than 64 bits, the SESSION variable for userID 
  can't be explicitly referenced in Javascript and will be truncated.
  
  SOLUTION: Create a hidden div and echo out the SESSION variable there.
            Then reference that div in the javascript.
			
	Found in ./pages/viewSingleProject.php: 
		//Bug Fix 4/1/19: An invalid userID was being returned when attempting 
		//to echo out the SESSION variable for the userID within the Javascript 
		//code here. The fix I found was to create a hidden div on the page itself 
		//and echo out the SESSION variable there and reference it here.

		//This is because Google Authentication provides user IDs that are larger 
		//than the 64 bit character columns for user IDs in the database and thus, 
		//because Javascript only supports a certain character length for a string,
		//the user ID was truncated. 