<?php
/**
 * This page handles client requests to modify or fetch projecgt-related data. All requests made to this page should
 * be a POST request with a corresponding `action` field in the request body.
 */
use DataAccess\UsersDao;
use Api\Response;
use Email\ApplicationMailer;
use DataAccess\CapstoneApplicationsDao;
use DataAccess\CapstoneProjectsDao;
use Api\ApplicationsActionHandler;

session_start();

// Setup our data access and handler classes
$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$applicationsDao = new CapstoneApplicationsDao($dbConn, $logger);
$usersDao = new UsersDao($dbConn, $logger);
$mailer = new ApplicationMailer($configManager->getEmailFromAddress(), $configManager->getEmailSubjectTag());
$handler= new ApplicationsActionHandler($applicationsDao, $projectsDao, $usersDao, $mailer, $configManager, $logger);

// Authorize the request
if (isset($_SESSION['userID']) && !empty($_SESSION['userID'])) {
    // Handle the request
    $handler->handleRequest();
} else {
    $handler->respond(new Response(Response::UNAUTHORIZED, 'You do not have permission to access this resource'));
}
