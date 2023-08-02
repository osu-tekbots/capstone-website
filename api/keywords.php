<?php
/**
 * This page handles client requests to modify or remove project keywords. All requests made to this page should
 * be a POST request with a corresponding `action` field in the request body.
 */
include_once '../bootstrap.php';

use Api\Response;
use Api\KeywordsActionHandler;
use DataAccess\KeywordsDao;

session_start();

// Setup our data access and handler classes
$keywordsDao = new KeywordsDao($dbConn, $logger);

$handler = new KeywordsActionHandler($keywordsDao, $configManager, $logger);

// Authorize the request
if (isset($_SESSION['userID']) && !empty($_SESSION['userID']) && isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin') {
    // Handle the request
    $handler->handleRequest();
} else {
    $handler->respond(new Response(Response::UNAUTHORIZED, 'You do not have permission to access this resource. Do you need to log in again?'));
}
?>