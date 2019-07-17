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

    $authProviders = $configManager->get("auth_google");

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

    if (!$authProvidedId) {
        return false;
    }

    $dao = new UsersDao($dbConn, $logger);

    $u = $dao->getUserByAuthProviderProvidedId($authProvidedId);
    if ($u) {
        $_SESSION['userID'] = $u->getId();
        $_SESSION['accessLevel'] = $u->getType()->getName();
        $_SESSION['newUser'] = false;
    } else {
        $u = new User();
        $u->setAuthProvider(new UserAuthProvider(UserAuthProvider::GOOGLE, 'Google'))
            ->setType(new UserType(UserType::PROPOSER, 'Proposer'))
            ->setAuthProviderId($authProvidedId)
            ->setFirstName($_SESSION['auth']['firstName'])
            ->setLastName($_SESSION['auth']['lastName'])
            ->setEmail($_SESSION['auth']['email']);
        $ok = $dao->addNewUser($u);
        // TODO: handle error

        $_SESSION['userID'] = $u->getId();
        $_SESSION['accessLevel'] = $u->getType()->getName();
        $_SESSION['newUser'] = true;
    }
    return true;
}
