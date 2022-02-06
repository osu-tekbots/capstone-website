<?php
/**
 * This page handles client requests to modify or fetch user-related data. All requests made to this page should be a 
 * POST request with a corresponding `action` field in the request body.
 */
include_once '../bootstrap.php';

use DataAccess\UsersDao;
use Api\UsersActionHandler;
use Api\Response;

if(!session_id()) {
    session_start();
}

// Setup our data access and handler classes
$dao = new UsersDao($dbConn, $logger);
$handler = new UsersActionHandler($dao, $logger);

// Authorize the request
if (isset($_SESSION['userID']) && !empty($_SESSION['userID'])) {
    // Handle the request
    $handler->handleRequest();
} else {
    $handler->respond(new Response(Response::UNAUTHORIZED, 'You do not have permission to access this resource'));
}
