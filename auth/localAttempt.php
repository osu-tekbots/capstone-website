<?php
include_once '../bootstrap.php';

use DataAccess\UsersDao;

if(!isset($_SESSION)) {
    session_start();
}

$isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);
if ($isLoggedIn) {
    // Redirect to their profile page
    $redirect = $configManager->getBaseUrl() . 'pages/myProfile.php';
    echo "<script>alert('Already logged in.');window.location.replace('$redirect');</script>";
    die();
}

if ( !isset($_POST) || (isset($_POST) && !isset($_POST['localEmail'])) || (isset($_POST) && !isset($_POST['localPassword'])) ) {
    $redirect = $configManager->getBaseUrl() . 'pages/myProfile.php';
    echo "<script>alert('Missing Information');location.replace('" . $redirect . "');</script>";
	die();
} 


$dao = new UsersDao($dbConn, $logger);
$u = $dao->getLocalUserWithCredentials($_POST['localEmail'], $_POST['localPassword']);
if ($u) {
    $_SESSION['site'] = 'capstoneSubmission';
    $_SESSION['userID'] = $u->getId();
    $_SESSION['accessLevel'] = $u->getType()->getName();
    $_SESSION['newUser'] = false;

    $u->setDateLastLogin(new DateTime());
    $dao->updateUser($u);
    
    $redirect = $configManager->getBaseUrl() . 'pages/myProfile.php';
    echo "<script>location.replace('" . $redirect . "');</script>";
	die();
}

$isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);
if ($isLoggedIn) {
    $redirect = $configManager->getBaseUrl() . 'pages/myProfile.php';
    echo "<script>window.location.replace('$redirect');</script>";
    die();
}

$redirect = $configManager->getBaseUrl() . 'auth/index.php?provider=local';
echo "<script>alert('Login Failed');location.replace('" . $redirect . "');</script>";
die();


?>

