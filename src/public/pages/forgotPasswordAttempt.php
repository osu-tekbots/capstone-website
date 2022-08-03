<?php
include_once '../bootstrap.php';

use DataAccess\UsersDao;

global $dbConn, $logger, $configManager;


if(!isset($_SESSION)) {
    session_start();
}

// $isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);
// if ($isLoggedIn) {
//     // Redirect to their profile page
//     $redirect = $configManager->getBaseUrl() . 'pages/myProfile.php';
//     echo "<script>window.location.replace('$redirect');</script>";
//     die();
// }

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




if ( !isset($_POST) || (isset($_POST) && !isset($_POST['userEmail'])) ) {
    $url = $pageURL . '/pages/forgotPassword.php';
    echo "<script>location.replace('" . $url . "');</script>";
    die();
} 




$dao = new UsersDao($dbConn, $logger);

$resetCode = $dao->addNewLocalUserResetAttempt($_POST['userEmail']);

if ($resetCode) {
    //email the code to the user here? or it sends in the addNewLocalUserResetAttempt()
    $_SESSION['userEmail'] = $_POST['userEmail'];
    $redirect = $configManager->getBaseUrl() . 'pages/resetPassword.php';
    echo "<script>window.location.replace('$redirect');</script>";
    die();
}

$logger->error("Code generation for local user password reset failed");
$redirect = $configManager->getBaseUrl() . 'pages/login.php';
echo "<script>window.location.replace('$redirect');</script>";
die();


?>