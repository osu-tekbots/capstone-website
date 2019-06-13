<?php

// Uncomment the lines below to display errors before configuration has been loaded
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('PUBLIC_FILES', __DIR__);

include PUBLIC_FILES . '/lib/shared/autoload.php';

// Load configuration
$configManager = new Util\ConfigManager(PUBLIC_FILES);

$dbConn = DataAccess\DatabaseConnection::FromConfig($configManager->getDatabaseConfig());

try {
    $logger = new Util\Logger($configManager->getLogFilePath(), $configManager->getLogLevel());
} catch (\Exception $e) {
    $logger = null;
}
