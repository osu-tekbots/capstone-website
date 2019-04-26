<?php
/**
 * This page handles client requests to modify or fetch projecgt-related data. All requests made to this page should
 * be a POST request with a corresponding `action` field in the request body.
 */
use DataAccess\UsersDao;
use Api\Response;
use DataAccess\CapstoneProjectsDao;
use Api\ProjectsActionHandler;
use Email\ProjectMailer;

session_start();

// Setup our data access and handler classes
$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$usersDao = new UsersDao($dbConn, $logger);
$mailer = new ProjectMailer($configManager->getEmailFromAddress(), $configManager->getEmailSubjectTag());
$handler= new ProjectsActionHandler($projectsDao, $usersDao, $mailer, $configManager, $logger);

// Authorize the request
if (isset($_SESSION['userID']) && !empty($_SESSION['userID'])) {
    // Handle the request
    $handler->handleRequest();
} else {
    $handler->respond(new Response(Response::UNAUTHORIZED, 'You do not have permission to access this resource'));
}
