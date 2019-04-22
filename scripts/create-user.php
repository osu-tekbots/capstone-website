<?php
/**
 * This script creates a new user in the database. It is to be used only from the command line (terminal) and when
 * setting up development/testing environments. It prompts the user for a few important pieces of information about
 * the user to insert, then it generates a new user using the User model class and inserts it into the configured
 * database (from the same INI files used on the site, in the private directory).
 */

/**
 * Prompt the user for input from the command line (terminal).
 *
 * @param string $message the message to prompt with
 * @param string $default and optional default value to supply if the user doesn't supply an input
 * @return string the value input by the user, or the default value if the user didn't supply one with a default set
 */
function prompt($message, $default = null) {
    $input = '';
    $msg = $default != null ? $message . " [$default]: " : $message . ': ';
    while ($input == '') {
        echo $msg;
        $input = trim(fgets(fopen('php://stdin', 'r')));
        if ($input == '' && $default != null) {
            $input = $default;
        }
    }
    return $input;
}

// Display ANY errors (don't want to miss anything!)
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('PUBLIC_FILES', '..');

// Setup a quick autoloader for simplicity's sake
include '../lib/shared/autoload.php';

// Load configuration
$configManager = new Util\ConfigManager('../config' );

// Setup the database connection and instantiate a DAO
$dbConn = DataAccess\DatabaseConnection::FromConfig($configManager->getDatabaseConfig());
$dao = new DataAccess\UsersDao($dbConn);

$onid = prompt('Enter ONID for user');
$fname = prompt('Enter first name for user', 'John');
$lname = prompt('Enter last name for user', 'Smith');

$types = $dao->getUserTypes();
$values = ' ';
foreach ($types as $type) {
    $values .= $type->getId() . ' - ' . $type->getName() . ' ';
}

$typeId = prompt("Enter type ($values)", 1);
$type;
foreach ($types as $t) {
    if ($t->getId() == $typeId) {
        $type = $t;
        break;
    }
}

// Automatically set the auth provider to 'None', since it is created from the command line
$authProviders = $dao->getUserAuthProviders();
$authProvider;
foreach ($authProviders as $provider) {
    if ($provider->getName() == 'None') {
        $authProvider = $provider;
        break;
    }
}

// Automatically set the salutation to 'None', since it probably isn't important
$salutations = $dao->getUserSalutations();
$salutation;
foreach ($salutations as $s) {
    if ($s->getName() == 'None') {
        $salutation = $s;
        break;
    }
}

// Create the new user and add it to the database
$user = (new Model\User())
    ->setType($type)
    ->setFirstName($fname)
    ->setLastName($lname)
    ->setOnid($onid)
    ->setAuthProvider($authProvider)
    ->setSalutation($salutation)
    ->setDateCreated(new DateTime());

$ok = $dao->addNewUser($user);

if (!$ok) {
    echo "Failed to add new user\n";
} else {
    echo "Successfully created new user\n";
    print_r($user);
}
