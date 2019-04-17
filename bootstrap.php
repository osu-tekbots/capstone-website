<?php

// Uncomment the lines below to display errors before configuration has been loaded
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('PUBLIC_FILES', __DIR__);

// Define an autoloader for custom PHP classes so we don't have to
// include their files manually
spl_autoload_register(function ($className) {
    include PUBLIC_FILES . '/lib/classes/' . str_replace('\\', '/', $className) . '.php';
});

// Load configuration
$configManager = new Util\ConfigManager(PUBLIC_FILES . '/config' );

$dbConn = DataAccess\DatabaseConnection::FromConfig($configManager->getDatabaseConfig());

try {
    $logger = new Util\Logger($configManager->getLogFilePath(), $configManager->getLogLevel());
} catch (\Exception $e) {
    $logger = null;
}
