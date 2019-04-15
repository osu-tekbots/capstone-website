<?php
/****************************************************************************************
* File Name: dbManager.php
* Authors: Symon Ramos and Thien Nam
* Date: 2/28/19
* Description: All database management is conducted in this file. This file is interfaced
* throughout this application using Ajax. Please reference the "action" variable passed into
* a given ajax call and the "Ajax POST Handling" section below to determine which functions
* will be executed for a given ajax call.
* Notes: Please maintain all database management in this file. Thanks <3
*****************************************************************************************/

//SQL database configuration is in the config.php file.
include_once('../includes/config.php');

include_once('../modules/mailer.php');

//Uncomment line below to echo queries for every function.
//define('DEBUG', 1);


/*********************************************************************************
* Function Name: dbConnect()
* Input: Connects with the database.
* Output: Returns a database object which can be manipulated to manipulate db data.
*********************************************************************************/
function dbConnect(){
	//These global variables are instantiated in the ../includes/config.php file.
	global $db_hostname, $db_username, $db_password, $db_name;
	$mysqli = new mysqli($db_hostname, $db_username, $db_password, $db_name);
	if ($mysqli->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		die;
	}

	return $mysqli;
}



/////////////////////////////////////////////////////////////
//Users Functions ///////////////////////////////////////////
/////////////////////////////////////////////////////////////

/*********************************************************************************
* Function Name: userExists()
* Input: User's ID.
* Output: Returns a 1 or 0 based on whether or not user data exists for given id.
*********************************************************************************/
function userExists($userID){
	$mysqli = dbConnect();
	$query = "select count(1) as ucount from users where user_id= '$userID'";
	if(defined('DEBUG')){
		echo $query;
	}
	$result = $mysqli->query($query);

	$row = $result->fetch_assoc();
	return $row['ucount'];
}

/*********************************************************************************
* Function Name: createUser()
* Input: userID, firstName, lastName, email, auth provider source.
* Output: Creates a user in the database based on OAUTH_CLIENT data returned from 
* login provider. As of 3/19/19, Google and Microsoft are supported.
*********************************************************************************/
function createUser($userID, $firstName, $lastName, $userEmail, $authProvider){
	$mysqli = dbConnect();

	//Per user design, new users are set as Students.
	$userAccessLevel = 'Student';

	$query = "INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `student_id`, `salutation`, `email`, `phone`, `affiliation`, `major`, `auth_provider`, `type`, `project_assigned`) VALUES ('" . $userID . "', '" . $firstName . "', '" . $lastName . "', NULL, NULL, '" . $userEmail . "', NULL, NULL, NULL, '" . $authProvider . "', '" . $userAccessLevel . "', NULL)";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}

/*********************************************************************************
* Function Name: updateUser()
* Input: User data.
* Output: Updates a specific user in the database.
*********************************************************************************/
function updateUser($userID, $firstName, $lastName, $salutation, $email, $phone, $affiliation, $projectAssigned, $major){
	$mysqli = dbConnect();
	$query = "UPDATE `users` SET first_name = '$firstName', last_name = '$lastName', salutation = '$salutation', email = '$email', phone = '$phone', affiliation = '$affiliation', project_assigned = '$projectAssigned', major = '$major' WHERE user_id = '$userID'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}

/*********************************************************************************
* Function Name: setUserType()
* Input: User's ID and type.
* Output: Update a specific user's type in the database.
*********************************************************************************/
function setUserType($userID, $type){
	$mysqli = dbConnect();
	$query = "UPDATE `users` SET type = '$type' WHERE user_id = '$userID'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}

/*********************************************************************************
* Function Name: getUserInfo()
* Input: User ID.
* Output: Returns user data.
*********************************************************************************/
function getUserInfo($userID){
	$query = "SELECT * FROM `users` where user_id = '$userID'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);

}

/*********************************************************************************
* Function Name: getListUsers()
* Input:
* Output: Returns all users in db.
*********************************************************************************/
function getListUsers(){
	$query = "SELECT * FROM `users`";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

/*********************************************************************************
* Function Name: getUserType()
* Input: User's ID.
* Output: Returns the user's type.
*********************************************************************************/
function getUserType($userID){
	$query = "SELECT `type` FROM `users` where user_id = '$userID'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////
//Projects Functions ////////////////////////////////////////
/////////////////////////////////////////////////////////////

/*********************************************************************************
* Function Name: createProject()
* Input: Project title and user ID.
* Output: Inserts a new project into the database. Returns the newly created
*         project's ID so that client-side may be redirected to the Edit Project page.
*********************************************************************************/
function createProject($title, $userID){
	$mysqli = dbConnect();

	$query = "INSERT INTO `projects` (`project_id`, `proposer_id`, `status`, `type`, `year`, `section`, `title`, `website`, `video`, `additional_emails`, `start_by`, `complete_by`, `date_created`, `last_updated`, `preferred_qualifications`, `minimum_qualifications`, `motivation`, `description`, `objectives`, `NDA/IP`, `compensation`, `comments`, `focus`, `number_groups`) VALUES (NULL, '$userID', 'Created', 'Capstone', NULL, NULL, '$title', NULL, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, NULL, NULL, NULL, NULL, 'None', NULL, 'Development', 1)";

	if($mysqli->query($query)){
		$query = "SELECT * FROM `projects` ORDER BY `project_id` DESC";
		if($result = $mysqli->query($query)){
			$row = $result->fetch_assoc();
			echo $row['project_id'];
			//When a project is created, the client-side (within ./pages/myProjects.php)
			//expects the id of the newly created project to be echoed to redirect
			//to the edit project page.
		}
	}
}

/*********************************************************************************
* Function Name: deleteProject()
* Input: Project ID
* Output: Deletes a current project in the database
*********************************************************************************/
function deleteProject($projectID){
	$mysqli = dbConnect();
	$query = "DELETE FROM projects WHERE project_id = $projectID";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}

/*********************************************************************************
* Function Name: getRelatedProjects()
* Description: This creates the Related Cards section in the View Single Project page.
* The first key in the keywords column of the project being viewed is used as a metric 
* to generate its related projects. This is used in ./pages/viewSingleProject.php.
* Input: A keyword and the project ID from the project being viewed.
* Output: Returns data from related projects.
*********************************************************************************/
function getRelatedProjects($key, $projectID){
	$mysqli = dbConnect();
	
	$query = "SELECT * FROM `projects` WHERE keywords LIKE '%$key%' AND status = 'Published' AND is_hidden = 0 AND project_id != $projectID";
	if(defined('DEBUG')){
		echo $query;
	}

	return $mysqli->query($query);
}


/*********************************************************************************
* Function Name: getSingleProject()
* Input: Project ID
* Output: Returns data for a specific project.
*********************************************************************************/
function getSingleProject($projectID){
	$query = "SELECT * FROM `projects` WHERE project_id='" . $projectID . "'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

/*********************************************************************************
* Function Name: getAllProjects()
* Input: 
* Output: Returns data from all proejcts.
*********************************************************************************/
function getAllProjects(){
	$query = "SELECT * FROM `projects`";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);
}


/*********************************************************************************
* Function Name: getAdminProjects()
* Input:
* Output: Projects that are pending or published are returned for admins to review.
*********************************************************************************/
function getAdminProjects(){
	$query = "SELECT * FROM `projects` WHERE `status`='Pending' OR `status`='Published' OR `status`='Denied' ";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

/*********************************************************************************
* Function Name: getProjectByID()
* Input: Project ID
* Output: Returns data from a specific project.
*********************************************************************************/
function getProjectByID($projectID){
	$query = "SELECT * FROM `projects` WHERE `project_id`='" . $projectID . "'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

/*********************************************************************************
* Function Name: getMyProjects()
* Input: 
* Output: Returns data from all the projects that belong to the user that is logged in.
*********************************************************************************/
function getMyProjects(){
	$userID = $_SESSION['userID'];

	$query = "SELECT * FROM `projects` WHERE `proposer_id`='" . $userID . "'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

/*********************************************************************************
* Function Name: changeProjectStatus()
* Input: Project ID and status to be changed into
* Output: Updates the 'status' column of a particular project in the DB.
*********************************************************************************/
function changeProjectStatus($projectID, $status){
	$mysqli = dbConnect();
	/* Possible project status types include:
	 * 		Created
	 * 		Pending
	 * 		Published
	 */
	$query = "UPDATE `projects` SET `status` = '$status' WHERE `project_id` = '$projectID'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}


/*********************************************************************************
* Function Name: updateAccessLevel()
* Input: Project ID and type to be changed into
* Output: Updates the 'type' column of a particular project in the DB.
*********************************************************************************/
function updateAccessLevel($accessLevel, $userID){
	$mysqli = dbConnect();
	$query = "UPDATE `users` SET `type` = '$accessLevel' WHERE `user_id` = '$userID'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
	$_SESSION['accessLevel'] = $accessLevel;
}

/*********************************************************************************
* Function Name: updateProject()
* Input: An object instantiated in ./pages/EditProject.php that holds all 
* relevant data to be updated.
* Output: Updates a row in the projects table of the database.
*********************************************************************************/
function updateProject($Project){
	$mysqli = dbConnect();

	//All values here are user-editable and must be real_escape_string'ed 
	//to avoid SQL injection and parsing errors.
	$title = $mysqli->real_escape_string($Project['title']);
	$proposerName = $mysqli->real_escape_string($Project['proposerName']);
	$id = $mysqli->real_escape_string($Project['id']);
	$type = $mysqli->real_escape_string($Project['type']);
	$focus = $mysqli->real_escape_string($Project['focus']);
	$compensation = $mysqli->real_escape_string($Project['compensation']);
	$description = $mysqli->real_escape_string($Project['description']);
	$motivation = $mysqli->real_escape_string($Project['motivation']);
	$objectives = $mysqli->real_escape_string($Project['objectives']);
	$minQualifications = $mysqli->real_escape_string($Project['minQualifications']);
	$prefQualifications = $mysqli->real_escape_string($Project['prefQualifications']);
	$nda = $mysqli->real_escape_string($Project['nda']);
	$website = $mysqli->real_escape_string($Project['website']);
	$video = $mysqli->real_escape_string($Project['video']);
	$additional_emails = $mysqli->real_escape_string($Project['additional_emails']);
	$startBy = $mysqli->real_escape_string($Project['startBy']);
	$completeBy = $mysqli->real_escape_string($Project['completeBy']);
	$comments = $mysqli->real_escape_string($Project['comments']);
	$image = $mysqli->real_escape_string($Project['image']);
	$number_groups = $mysqli->real_escape_string($Project['number_groups']);
	$keywords = $mysqli->real_escape_string($Project['keywords']);

	$query = "UPDATE `projects` SET `type` = '$type', `focus` = '$focus', `image` = '$image', `title` = '$title', `website` = '$website', `video` = '$video', `additional_emails` = '$additional_emails', `start_by` = '$startBy', `complete_by` = '$completeBy', `preferred_qualifications` = '$prefQualifications', `minimum_qualifications` = '$minQualifications', `motivation` = '$motivation', `description` = '$description', `objectives` = '$objectives', `NDA/IP` = '$nda', `compensation` = '$compensation', `comments` = '$comments', `number_groups` = '$number_groups', `keywords` = '$keywords' WHERE `projects`.`project_id` = '$id'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}

/*********************************************************************************
* Function Name: submitProject()
* Input: An object instantiated in ./pages/EditProject.php that holds all 
* relevant data to be updated.
* Output: Updates project data, its status, and sends an email notifying the 
* user that they have successfully submitted the project for approval.
*********************************************************************************/
function submitProject($Project){
	updateProject($Project);
	$mysqli = dbConnect();
	$id = $mysqli->real_escape_string($Project['id']);
	changeProjectStatus($id, "Pending");
	projectSubmissionEmail($id);
}


/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////
//Misc Functions ////////////////////////////////////////////
/////////////////////////////////////////////////////////////

function testConnection(){
	echo 'Connection Successful!';
}

function getKeywords(){
	$query = "SELECT * FROM `keywords` ORDER BY `name` ASC";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

function checkIfKeywordExists($keyword){
	$mysqli = dbConnect();
	$query = "select count(1) as ucount from `keywords` where `name` = '$keyword'";
	if(defined('DEBUG')){
		echo $query;
	}
	$result = $mysqli->query($query);

	$row = $result->fetch_assoc();
	return $row['ucount'];
}

function createKeyword($keyword){
	$query = "INSERT INTO `keywords` (`keyword_id`, `name`) VALUES (NULL, '$keyword')";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	$mysqli->query($query);
}

/*********************************************************************************
* Function Name: updateKeywords()
* Input: A comma-separated string called keywords.
* Output: Inserts any keywords that don't already exist into the DB.
*********************************************************************************/
function updateKeywords($keywords){
	$keywords = explode(",", $keywords);

	foreach($keywords as $key){
		if($key != ' ' && !checkIfKeywordExists($key)){
			createKeyword($key);
		}
	}
}

function getAllProjectsCount(){

}

function getAllUsersCount(){

}

function getPendingProjectsCount(){
	$query = "SELECT COUNT(*) as totalPendingProject FROM `projects` where `status` = 'Pending'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

function getPendingCategoryCount(){
	$query = "SELECT COUNT(*) as totalPendingCategory FROM `projects` where `category` = '' and `status` != 'Created'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

function getNameFromProjectId($project_id){
	$query = "SELECT CONCAT(first_name, ' ', last_name)
	FROM users
	INNER JOIN projects
	ON `projects`.proposer_id = `users`.user_id
	WHERE project_id = $project_id";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli = dbConnect();
	return $mysqli->query($query);

}


/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////
//Application Functions /////////////////////////////////////
/////////////////////////////////////////////////////////////

function createApplication($userID, $projectID){
	$mysqli = dbConnect();

	$query = "INSERT INTO `users_application` (`application_id`, `user_id`, `project_id`, `status`, `justification`, `time_available`, `skill_set`, `external_link`, `date_applied`, `last_updated`) VALUES (NULL, '$userID', '$projectID', 'Created', NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
	//echo $query;
	
	if($mysqli->query($query)){
		$query = "SELECT * FROM `users_application` ORDER BY `application_id` DESC";
		if($result = $mysqli->query($query)){
			$row = $result->fetch_assoc();
			echo $row['application_id'];
			//When an application is created, the client-side (within ./pages/viewSingleProject.php)
			//expects the id of the newly created application to be echoed to redirect
			//to the edit application page.
		}
	}
}

function updateApplication($Application){
	$mysqli = dbConnect();
	
	$id = $mysqli->real_escape_string($Application['id']);
	$justification = $mysqli->real_escape_string($Application['justification']);
	$skill_set = $mysqli->real_escape_string($Application['skill_set']);
	$time_available = $mysqli->real_escape_string($Application['time_available']);
	$external_link = $mysqli->real_escape_string($Application['external_link']);

	$query = "UPDATE `users_application` SET `justification` = '$justification', `skill_set` = '$skill_set', `time_available` = '$time_available', `external_link` = '$external_link' WHERE `users_application`.`application_id` = '$id'";
	
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}

/*********************************************************************************
* Function Name: changeApplicationStatus()
* Input: Application ID and status to be changed into
* Output: Updates the 'status' column of a particular application in the DB.
*********************************************************************************/
function changeApplicationStatus($applicationID, $status){
	$mysqli = dbConnect();
	/* Possible application status types include:
	 * 		Created
	 * 		Submitted
	 */
	$query = "UPDATE `users_application` SET `status` = '$status' WHERE `application_id` = '$applicationID'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}


function getApplicationsAssociatedWithProject($projectID){

	$query = "select *, `users_application`.last_updated AS last_updated, `users_application`.date_applied AS date_applied from `users_application` inner join `users` on `users_application`.user_id = `users`.user_id inner join `projects` on `users_application`.project_id = `projects`.project_id where `users_application`.project_id = '$projectID'";
	if(defined('DEBUG')){
		echo $query;
	}

	$mysqli = dbConnect();
	return $mysqli->query($query);
}


function getMyApplications($userID){
	$query = "select *, `users_application`.status AS status, `users_application`.last_updated AS last_updated, `users_application`.date_applied AS date_applied from `users_application` inner join `users` on `users_application`.user_id = `users`.user_id inner join `projects` on `users_application`.project_id = `projects`.project_id where `users_application`.user_id = '$userID'";
	if(defined('DEBUG')){
		echo $query;
	}
	
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

function getApplication($applicationID){

	$query = "select * from `users_application` inner join `users` on `users_application`.user_id = `users`.user_id inner join `projects` on `users_application`.project_id = `projects`.project_id where `users_application`.application_id = '$applicationID'";

	if(defined('DEBUG')){
		echo $query;
	}
	
	$mysqli = dbConnect();
	return $mysqli->query($query);
}

/*********************************************************************************
* Function Name: submitApplication()
* Input: An object instantiated in ./pages/EditApplication.php that holds all 
* relevant data to be updated.
* Output: Updates application data, its status, and sends an email notifying the 
* user that they have successfully submitted the application and the proposer of 
* the project that an application has been submitted.
*********************************************************************************/
function submitApplication($Application){
	updateApplication($Application);
	
	$mysqli = dbConnect();
	$id = $mysqli->real_escape_string($Application['id']);
	changeApplicationStatus($id, "Submitted");
	applicationSubmissionEmail($id);
}


/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////
//Admin Functions ///////////////////////////////////////////
/////////////////////////////////////////////////////////////

function adminApproveProject($id){
	$mysqli = dbConnect();
	$query = "UPDATE `projects` SET `status` = 'Published' WHERE `project_id` = '$id'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
	projectApprovalEmail($id);
}

function adminUnapproveProject($id, $reason){
	$mysqli = dbConnect();
	$query = "UPDATE `projects` SET `status` = 'Denied' WHERE `project_id` = '$id'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
	projectRejectionEmail($id, $reason);
}

function adminMakeProjectPrivate($id){
	$mysqli = dbConnect();
	$query = "UPDATE `projects` SET `is_hidden` = 1 WHERE `project_id` = '$id'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}

function adminMakeProjectNotPrivate($id){
	$mysqli = dbConnect();
	$query = "UPDATE `projects` SET `is_hidden` = 0 WHERE `project_id` = '$id'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}

function adminChooseProjectCategory($id, $category){
	$mysqli = dbConnect();
	$query = "UPDATE `projects` SET `category` = '$category' WHERE `project_id` = '$id'";
	if(defined('DEBUG')){
		echo $query;
	}
	$mysqli->query($query);
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////
//Ajax POST Handling ////////////////////////////////////////
/////////////////////////////////////////////////////////////


//When AJAX is used, application will divert to this logic.
//Based on whatever action is passed, certain statements will
//be executed.
if(isset($_POST['action'])){
	$mysqli = dbConnect();
	//NOTE: Please us $mysqli->real_escape_string() when handling
	//data that is manipulated by the user. This function performs
	//handling for escape characters and prevents SQL injection.
	$action = $mysqli->real_escape_string($_POST['action']);

	switch($action){
		case "createProject":
			$title = $mysqli->real_escape_string($_POST['title']);
			$userID = $mysqli->real_escape_string($_POST['userID']);
			createProject($title, $userID);
			exit();
			break;
		case "deleteProject":
			$projectID = $mysqli->real_escape_string($_POST['projectID']);
			deleteProject($projectID);
			exit();
			break;
		case "createApplication":
			$userID = $mysqli->real_escape_string($_POST['userID']);
			$projectID = $mysqli->real_escape_string($_POST['projectID']);
			createApplication($userID, $projectID);
			exit();
			break;
		case "adminEditProject":
			$id = $mysqli->real_escape_string($_POST['projectID']);
			echo $id;
			adminEditProject($id);
			exit();
			break;
		case "adminApproveProject":
			$id = $mysqli->real_escape_string($_POST['projectID']);
			echo $id;
			adminApproveProject($id);
			exit();
			break;
		case "adminUnapproveProject":
			$id = $mysqli->real_escape_string($_POST['projectID']);
			$reason = $mysqli->real_escape_string($_POST['reason']);
			echo $id;
			echo $reason;
			adminUnapproveProject($id, $reason);
			exit();
			break;
		case "adminMakeProjectPrivate":
			$id = $mysqli->real_escape_string($_POST['projectID']);
			echo $id;
			adminMakeProjectPrivate($id);
			exit();
			break;
		case "adminMakeProjectNotPrivate":
			$id = $mysqli->real_escape_string($_POST['projectID']);
			echo $id;
			adminMakeProjectNotPrivate($id);
			exit();
			break;
		case "adminChooseProjectCategory":
			$id = $mysqli->real_escape_string($_POST['projectID']);
			$category = $mysqli->real_escape_string($_POST['projectCategorySelect']);
			adminChooseProjectCategory($id, $category);
			exit();
			break;
		case "submitForApproval":
			submitProject($_POST['P']);
			exit();
			break;
		case "submitApplication":
			submitApplication($_POST['A']);
			exit();
			break;
		case "editAccessLevel":
			$accessLevel = $mysqli->real_escape_string($_POST['accessLevelSelected']);
			$userID = $mysqli->real_escape_string($_POST['userID']);
			updateAccessLevel($accessLevel, $userID);
			exit();
			break;
		case "saveProjectDraft":
			updateProject($_POST['P']);
			$keywords = $mysqli->real_escape_string($_POST['P']['keywords']);
			updateKeywords($keywords);
			exit();
			break;
		case "saveApplicationDraft":
			updateApplication($_POST['A']);
			exit();
			break;
		case "saveProfile":
			$profileUserID = $mysqli->real_escape_string($_POST['profileUserID']);
			$profileFirstName = $mysqli->real_escape_string($_POST['profileFirstName']);
			$profileLastName = $mysqli->real_escape_string($_POST['profileLastName']);
			$profileSalutation = $mysqli->real_escape_string($_POST['profileSalutation']);
			$profileEmail = $mysqli->real_escape_string($_POST['profileEmail']);
			$profilePhone = $mysqli->real_escape_string($_POST['profilePhone']);
			$profileAffiliation = $mysqli->real_escape_string($_POST['profileAffiliation']);
			$profileProjectAssigned = $mysqli->real_escape_string($_POST['profileProjectAssigned']);
			$profileMajor = $mysqli->real_escape_string($_POST['profileMajor']);
			updateUser($profileUserID, $profileFirstName, $profileLastName, $profileSalutation, $profileEmail, $profilePhone, $profileAffiliation, $profileProjectAssigned, $profileMajor);
			exit();
			break;
		case "loginWithGoogle":
			$id_token = $_POST['id_token'];
			loginWithGoogle($id_token);
			exit();
			break;
		default:
			break;
	}
}


/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

?>
