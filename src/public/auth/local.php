<?php
include_once '../bootstrap.php';

use DataAccess\UsersDao;
use Model\User;
use Model\UserAuthProvider;
use Model\UserType;

if (!isset($_SESSION)) {
    session_start();
}

function authenticateWithLocal() {
    // Redirect to their profile page
    $isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);
    if ($isLoggedIn) {
        $redirect = $configManager->getBaseUrl() . 'pages/myProfile.php';
        echo "<script>window.location.replace('$redirect');</script>";
        die();
    }


    // Redirect to the local login page
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

    $url = $pageURL . '/pages/localLogin.php';
    echo "<script>location.replace('" . $url . "');</script>";
    die();

}

