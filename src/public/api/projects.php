<?php
/**
 * This page handles client requests to modify or fetch projecgt-related data. All requests made to this page should
 * be a POST request with a corresponding `action` field in the request body.
 */
include_once '../bootstrap.php';

use DataAccess\UsersDao;
use Api\Response;
use DataAccess\CapstoneProjectsDao;
use DataAccess\KeywordsDao;
use DataAccess\CategoriesDao;
use Api\ProjectsActionHandler;
use Email\ProjectMailer;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Setup our data access and handler classes
$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$usersDao = new UsersDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);
$categoriesDao = new CategoriesDao($dbConn, $logger);
$mailer = new ProjectMailer($configManager->get('email.from_address'), $configManager->get('email.subject_tag'));
$handler = new ProjectsActionHandler($projectsDao, $usersDao, $keywordsDao, $categoriesDao, $mailer, $configManager, $logger);

// Authorize the request
if (isset($_SESSION['userID']) && !empty($_SESSION['userID'])) {
    // Handle the request
    $handler->handleRequest();
} else {
	$handler->respond(new Response(Response::UNAUTHORIZED, 'You do not have permission to access this resource'));
}
