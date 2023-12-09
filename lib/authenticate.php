<?php

use DataAccess\UsersDao;
$usersDao = new UsersDao($dbConn, $logger);

if (!session_id()) session_start();

$user = NULL;

// Get user & set $_SESSION user variables for this site
if(isset($_SESSION['site']) && $_SESSION['site'] == 'capstoneSubmission') {
    // $_SESSION["site"] is this one! User info should be correct
} else {
    if(isset($_SESSION['auth']['method'])) {
        $logger->info('Auth method: '.$_SESSION['auth']['method']);
        switch($_SESSION['auth']['method']) {
            case 'onid':
                // Logged in with ONID on another site; storing this site's user info in $_SESSION...
                
                $logger->info('Updating $_SESSION credentials for this site using ONID: '.$_SESSION['auth']['id'].' (used to be '.$_SESSION['site'].')');
                $user = $usersDao->getUserByOnid($_SESSION['auth']['id']);
                
                $_SESSION['site'] = 'capstoneSubmission';
                $_SESSION['userID'] = $user->getId();
                $_SESSION['accessLevel'] = $user->getType()->getName();
                
                break;

            case 'oauth2':
                // Logged in with Google on another site; storing this site's user info in $_SESSION...

                $logger->info('Updating $_SESSION credentials for this site using Google: '.$_SESSION['auth']['id'].' (used to be '.$_SESSION['site'].')');
                $user = $usersDao->getUserByAuthProviderProvidedId($_SESSION['auth']['id']);
                
                $_SESSION['site'] = 'capstoneSubmission';
                $_SESSION['userID'] = $user->getId();
                $_SESSION['accessLevel'] = $user->getType()->getName();

                break;
            
            default:
                // Logged in with something not valid for this site; setting as not logged in
                $logger->info('Authentication provider is '.$_SESSION['auth']['method'].', not something this site recognizes');

                $_SESSION['site'] = NULL;
                $_SESSION['userID'] = NULL;
                $_SESSION['accessLevel'] = NULL;
        }
    } else {
        // Not logged in; still clear just to avoid the possibility of issues?
        $logger->info('Switched from another site, but not logged in');
        $_SESSION['site'] = NULL;
        unset($_SESSION['userID']);
        $_SESSION['userType'] = NULL;
    }
}

/**
 * Checks if the person who initiated the current request has one of the given access levels
 * 
 * @param string|string[] $allowedAccessLevels  The access level(s) that should be accepted. Options are:
 *      * "public"
 *      * "user"
 *      * "employee"
 * 
 * @return bool Whether the person who initiated the current request has one of the given access levels
 */
function verifyPermissions($allowedAccessLevels) {
    try {
        $isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);
        $isAdmin = $isLoggedIn && isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

        $allowPublic = (gettype($allowedAccessLevels)=='string') ? $allowedAccessLevels=='public' : in_array('public', $allowedAccessLevels);
        $allowUsers  = (gettype($allowedAccessLevels)=='string') ? $allowedAccessLevels=='user'   : in_array('user',   $allowedAccessLevels);
        $allowAdmin  = (gettype($allowedAccessLevels)=='string') ? $allowedAccessLevels=='admin'  : in_array('admin',  $allowedAccessLevels);
        
        if($allowPublic) {
            return true;
        }
        if($allowUsers && $isLoggedIn) {
            return true;
        }
        if($allowAdmin && $isAdmin) {
            return true;
        }
    } catch(\Exception $e) {
        $logger->error('Failure while verifying user permissions: '.$e->getMessage());
    } 
    
    return false;
}