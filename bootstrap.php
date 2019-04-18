<?php

// Uncomment the lines below to display errors before configuration has been loaded
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('PUBLIC_FILES', __DIR__);

// Define an autoloader for custom PHP classes so we don't have to
// include their files manually. The autoloader should check for the
// repository specific classes and shared classes.
spl_autoload_register(function ($className) {
    $phpFile = str_replace('\\', '/', $className) . '.php';
    $local = PUBLIC_FILES . '/lib/classes/' . $phpFile;
    $shared = PUBLIC_FILES . '/lib/shared/classes/' . $phpFile;
    if(file_exists($local)) {
        include $local;
        return true;
    }
    if(file_exists($shared)) {
        include $shared;
        return true;
    }
    return false;
});

// Load configuration
$configManager = new Util\ConfigManager(PUBLIC_FILES . '/config' );

$dbConn = DataAccess\DatabaseConnection::FromConfig($configManager->getDatabaseConfig());

try {
    $logger = new Util\Logger($configManager->getLogFilePath(), $configManager->getLogLevel());
} catch (\Exception $e) {
    $logger = null;
}
