<?php
 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../bootstrap.php';

use DataAccess\UsersDao;
use Model\User;
use Model\UserAuthProvider;
use Model\UserType;
use Email\Mailer;

if(!isset($_SESSION)) {
    session_start();
}

$dao = new UsersDao($dbConn, $logger);

if (!isset($_POST) || (isset($_POST) && !isset($_POST['userEmail'])) ) {
    $redirect = $configManager->getBaseUrl() . 'pages/forgotPassword.php';
	echo "<script>location.replace('" . $redirect . "');</script>";
    die();
} 

$logger->info("User password reset attempt: " . $_POST['userEmail']);
$user = $dao->getUserByEmail($_POST['userEmail']);
if (!$user) { // New user!!!
	$logger->error("User not Found");
	if ($_POST['userFirst'] != '' && $_POST['userLast'] != ''){
		$auth = new UserAuthProvider(6,'Local');
		$utype = new UserType(2, 'Proposer');
		$user = new User();
		
		$user->setFirstName($_POST['userFirst']);
		$user->setLastName($_POST['userLast']);
		$user->setEmail($_POST['userEmail']);
		$user->setType($utype);
		$user->setAuthProvider($auth);
		$dao->addNewuser($user);
		$dao->setupLocalUserPassword($user);
		$logger->error("Added User ". $_POST['userFirst'] . " " . $_POST['userLast']);
	}
}

if ($user->getAuthProvider()->getName() == "Local"){
	$resetCode = $dao->addNewLocalUserResetAttempt($_POST['userEmail']);
	///Need to send email and display success to 
	$mailer = new Mailer('eecs_capstone_staff@engr.orst.edu', 'EECS Projects');
	$link = $configManager->getBaseUrl() . 'pages/resetPassword.php?email='.$_POST['userEmail'].'&resetCode=' .$resetCode;
	$to = $_POST['userEmail'];
	$subject = "EECS Projects Password Reset";
	$message = "<p>There was a request to reset a password for this email on the EECS Projects page. 
		If this was not you or was in error, you may safely ignore this email. If you would like to 
			reset your password, follow the link below with in 45 minutes. If the link does not work, 
				you can copy and past the link into your address bar.</p>
				<a href='$link'>$link</a>";
	
	$mailer->sendEmail($to, $subject, $message, true);
	$redirect = $configManager->getBaseUrl() . 'pages/login.php';
	echo "<script>alert('Password reset email has been sent. Follow the link in the email.');location.replace('" . $redirect . "');</script>";
	die();
} else {
	$redirect = $configManager->getBaseUrl() . 'pages/login.php';
	echo "<script>alert('This email is already registered but not as a local user. Authenticate through another option on the log in page.');location.replace('" . $redirect . "');</script>";
	die();
}



$logger->error("Code generation for local user password reset failed");
$redirect = $configManager->getBaseUrl() . 'pages/login.php';
echo "<script>window.location.replace('$redirect');</script>";
die();


?>