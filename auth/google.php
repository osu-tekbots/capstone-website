<?php
include_once '../bootstrap.php';

use DataAccess\UsersDao;
use Model\User;
use Model\UserAuthProvider;
use Model\UserType;

if (!isset($_SESSION)) {
    session_start();
}

include_once PUBLIC_FILES . '/lib/shared/auth/oauth.php';

/**
 * Authenticates the user using Google's OAuth servers.
 *
 * @return bool true on success, false otherwise
 */
function authenticateWithGoogle() {
    global $dbConn, $logger, $configManager;

    $logger->trace('Beginning Google authentication');

    $authProviders = $configManager->get("auth_google");

    $logger->trace('Got Google authProvider info from config.ini');

    $authProvidedId = authenticateWithOAuth2(
        'Google',
        $authProviders['client_id'],
        $authProviders['secret'],
        array(
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ),
        'https://www.googleapis.com/oauth2/v1/userinfo'
    );

    $logger->trace('Completed OAuth2 authentication with Google');

    if (!$authProvidedId) {
        $logger->info('Google OAuth2 did not return an authID; returning false');
        return false;
    }

    $dao = new UsersDao($dbConn, $logger);

    $logger->trace('Created user dao');

    $u = $dao->getUserByAuthProviderProvidedId($authProvidedId);
    if ($u) {
        $logger->trace('Found user: '.$u->getId());

        $_SESSION['site'] = 'capstoneSubmission';
        $_SESSION['userID'] = $u->getId();
        $_SESSION['accessLevel'] = $u->getType()->getName();
        $_SESSION['newUser'] = false;

        $logger->trace('Set $_SESSION variables for Google user');

        $u->setDateLastLogin(new DateTime());
        $logger->trace('Set dateLastLogin for Google user');
        $dao->updateUser($u);
        $logger->trace('Updated Google user for dateLastLogin');
    } else {
        $logger->trace('User not found; creating new user');
        $u = new User();
        $u->setAuthProvider(new UserAuthProvider(UserAuthProvider::GOOGLE, 'Google'))
            ->setType(new UserType(UserType::PROPOSER, 'Proposer'))
            ->setAuthProviderId($authProvidedId)
            ->setFirstName($_SESSION['auth']['firstName'])
            ->setLastName($_SESSION['auth']['lastName'])
            ->setEmail($_SESSION['auth']['email'])
            ->setDateLastLogin(new DateTime());
        $ok = $dao->addNewUser($u);
        // TODO: handle error

        $_SESSION['site'] = 'capstoneSubmission';
        $_SESSION['userID'] = $u->getId();
        $_SESSION['accessLevel'] = $u->getType()->getName();
        $_SESSION['newUser'] = true;
    }
    $logger->trace('Returning true from authenticateWithGoogle()');
    return true;
}
