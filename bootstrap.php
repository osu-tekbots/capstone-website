<?php

// Uncomment the lines below to display errors before configuration has been loaded
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

define('PUBLIC_FILES', __DIR__);

include PUBLIC_FILES . '/lib/shared/autoload.php';

// Load configuration
$configManager = new Util\ConfigManager(PUBLIC_FILES);

try {
    $dbConn = DataAccess\DatabaseConnection::FromConfig($configManager->getDatabaseConfig());
} catch (\Exception $e) {
    echo 'There is an irresolvable issue with our database connection right now. Please try again later.';
    die();
}

try {
    $logFileName = $configManager->getLogFilePath() . date('MY') . ".log";
    $logger = new Util\Logger($logFileName, $configManager->getLogLevel());
} catch (\Exception $e) {
    $logger = null;
}

// Set $_SESSION variables to be for this site
include PUBLIC_FILES . '/lib/authenticate.php';
