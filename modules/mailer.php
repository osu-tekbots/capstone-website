<?php
/*****************************************************************************************
* Filename: mailer.php
* Author: Jack Nam
* Date: 4/1/19
* Description: This file contains all mail-handler functions. 
* NOTE: This file is only referenced in the ./db/dbManager.php file.
******************************************************************************************/


/*****************************************************************************************
* GLOBAL VARIABLES
******************************************************************************************/

$headers = 'From:heer@oregonstate.edu' . "\r\n"; 
$subjectTag = '[OSU Capstone] ';



/*****************************************************************************************
* EMAILS RELATED TO PROJECTS
* Emails: 
* 	projectSubmissionEmail()
*   projectApprovalEmail()
*   projectRejectionEmail()
*   notifyAdminEmail()
******************************************************************************************/

function projectSubmissionEmail($projectID){
  global $headers, $subjectTag;
	
  $result = getSingleProject($projectID);
  $row = $result->fetch_assoc();
  $title = $row['title'];
  $proposer_id = $row['proposer_id'];

  $result = getUserInfo($proposer_id);
  $row = $result->fetch_assoc();
  $email = $row['email'];
  $first_name = $row['first_name'];
  $last_name = $row['last_name'];


  $to = $email;
  $link = "http://eecs.oregonstate.edu/education/capstone/newcapstone/pages/viewSingleProject.php?id=$projectID";
  $subject = $subjectTag . "Project Submission: $title";
  $NDA_message = "
  If your project requires an NDA and/or IP agreement, it must be indicated at the time the students select the projects.

  If your company intends to provide proprietary materials or confidential information requiring an NDA, OSU can arrange for a written agreement to reviewed and signed amongst the students, your company, and OSU.

  Such an agreement will authorize the students to use and discuss the provided materials or information with each other and their instructor in confidence.

  The university will not participate in any agreement that requires students to transfer intellectual property rights ownership to your company or puts overly burdensome confidentiality obligations on the students.

  Though OSU certainly appreciates your company’s sponsorship, we strongly discourage any agreements that could deter students from sharing the results of their academic work at OSU with fellow students, parents or future employers.

  This does not prevent a separate arrangement between you each student individually.";

  $message = "

  Dear $first_name $last_name,

  Thank you for submitting your project!
  ---------------------------
  Project ID: $projectID
  Project Title: $title
  ---------------------------

  Your project is now awaiting for approval from an administrator.

  Your project can now be viewed at: $link

  * Your project has the ability to be modified by an administrator for final revisions *

  ";

  if ($nda != "No Agreement Required"){
    $message = $message . ' ' . $NDA_message;
  }

  mail($to, $subject, $message, $headers); // Send our email

}

function projectApprovalEmail($projectID){
  global $headers, $subjectTag;
	
  $result = getSingleProject($projectID);
  $row = $result->fetch_assoc();
  $title = $row['title'];
  $proposer_id = $row['proposer_id'];

  $result = getUserInfo($proposer_id);
  $row = $result->fetch_assoc();
  $email = $row['email'];
  $first_name = $row['first_name'];
  $last_name = $row['last_name'];

  $to = $email;
  $link = "http://eecs.oregonstate.edu/education/capstone/newcapstone/pages/viewSingleProject.php?id=$projectID";
  $subject = $subjectTag . "Project Approval: $title";
  $message = "

  Dear $first_name $last_name,

  Your project has been approved!
  ---------------------------
  Project ID: $projectID
  Project Title: $title
  ---------------------------

  Your project is can now be viewed at: $link

  * Your project has the ability to be modified by an administrator for final revisions *

  ";

  mail($to, $subject, $message, $headers); // Send our email

}

function projectRejectionEmail($projectID, $reason){
  global $headers, $subjectTag;
	
  $result = getSingleProject($projectID);
  $row = $result->fetch_assoc();
  $title = $row['title'];
  $proposer_id = $row['proposer_id'];

  $result = getUserInfo($proposer_id);
  $row = $result->fetch_assoc();
  $email = $row['email'];
  $first_name = $row['first_name'];
  $last_name = $row['last_name'];

  $to = $email;
  $subject = $subjectTag . "Project Request Denied: $title";
  $message = "

  Dear $first_name $last_name,

  We are sorry to announce that your project was not approved.
  ---------------------------
  Project ID: $projectID
  Project Title: $title
  Reason for rejection: $reason
  ---------------------------

  If you have any further questions, please send us an email at heer@oregonstate.edu

  ";

  mail($to, $subject, $message, $headers); // Send our email

}

function notifyAdminEmail($pendingProjects, $pendingCategories){
  global $headers, $subjectTag;
	
  $subject = $subjectTag . "Projects Need To Be Approved!";
  $to = "namt@oregonstate.edu"; // To admin - Soon to mailer list

  $message = "
    Just a reminder, you have
    $pendingProjects - Pending Projects that need to be approved.
    $pendingCategories - Pending Projects that need categorization.

  ";

  mail($to, $subject, $message, $headers); // Send our email
  
}


/*****************************************************************************************
* EMAILS RELATED TO APPLICATIONS
* Emails: 
*   applicationSubmissionEmail()
******************************************************************************************/


/*****************************************************************************************
* Function Name: applicationSubmissionEmail()
* Description: Based on the application ID that is passed in, sends a 
* confirmation email to the student who applied for the project and an
* email to the proposer of the project.
* 
* NOTE: 2 emails are sent in this function - one to the student who applied 
* for the project and another for the proposer.
******************************************************************************************/
function applicationSubmissionEmail($applicationID){
	global $headers, $subjectTag;

	//Send an email to the student who applied for the project.
	$result = getApplication($applicationID);
	$row = $result->fetch_assoc();
	$title = $row['title'];
	$user_id = $row['user_id'];
	$first_name = $row['first_name'];
	$last_name = $row['last_name'];
	$email = $row['email'];

	$to = $email;
	$link = "http://eecs.oregonstate.edu/education/capstone/newcapstone/pages/myApplications.php";
	$subject = $subjectTag . "Application Submission for: $title";

	$message = "

	Dear $first_name $last_name,

	Thank you for submitting your application for the following project: 

	---------------------------
	Project Title: $title
	---------------------------

	Your application can be viewed at: $link. 
	
	Thank you.
	";

	mail($to, $subject, $message, $headers); // Send our email

	/*****************************************************************************************
	******************************************************************************************
	*****************************************************************************************/
	
	//Send an email to the proposer of the project.
	$result = getApplication($applicationID);
	$row = $result->fetch_assoc();
	$title = $row['title'];
	$proposer_id = $row['proposer_id'];

	$result = getUserInfo($proposer_id);
	$row = $result->fetch_assoc();
	$email = $row['email'];
	$first_name = $row['first_name'];
	$last_name = $row['last_name'];
	
	$to = $email;
	$link = "http://eecs.oregonstate.edu/education/capstone/newcapstone/pages/myApplications.php";
	$subject = $subjectTag . "An Application has been submitted for: $title";

	$message = "

	Dear $first_name $last_name,

	An application has been submitted for the following project:

	---------------------------
	Project Title: $title
	---------------------------

	You can view all of your existing applications at: $link. 
	
	Thank you.
	";

	mail($to, $subject, $message, $headers); // Send our email
	
}




  ?>
