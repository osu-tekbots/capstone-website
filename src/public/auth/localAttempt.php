<?php
include_once '../bootstrap.php';

use DataAccess\UsersDao;

global $dbConn, $logger, $configManager;


if(!isset($_SESSION)) {
    session_start();
}

$isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);
if ($isLoggedIn) {
    // Redirect to their profile page
    $redirect = $configManager->getBaseUrl() . 'pages/myProfile.php';
    echo "<script>window.location.replace('$redirect');</script>";
    die();
}

$pageURL = 'http';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $pageURL .= 's';
}
$pageURL .= '://';

if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
    $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
} else {
    $pageURL .= $_SERVER['SERVER_NAME'];
}



if ( !isset($_POST) || (isset($_POST) && !isset($_POST['localEmail'])) || (isset($_POST) && !isset($_POST['localPassword'])) ) {
    $url = $pageURL . '/pages/localLogin.php';
    echo "<script>location.replace('" . $url . "');</script>";
    die();
} 


$dao = new UsersDao($dbConn, $logger);

$u = $dao->getLocalUserWithCredentials($_POST['localEmail'], $_POST['localPassword']);
if ($u) {
    $_SESSION['userID'] = $u->getId();
    $_SESSION['accessLevel'] = $u->getType()->getName();
    $_SESSION['newUser'] = false;
    return true;
}

$isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);
if ($isLoggedIn) {
    $redirect = $configManager->getBaseUrl() . 'pages/myProfile.php';
    echo "<script>window.location.replace('$redirect');</script>";
    die();
}

$redirect = $configManager->getBaseUrl() . 'pages/login.php';
echo "<script>window.location.replace('$redirect');</script>";
die();


?>

