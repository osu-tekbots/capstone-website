<?php
include_once '../bootstrap.php';

use DataAccess\UsersDao;
use Model\User;
use Model\UserAuthProvider;

if (!isset($_SESSION)) {
    session_start();
}

include_once PUBLIC_FILES . '/lib/shared/auth/oauth.php';

/**
 * Authenticates the user using Microsofts's OAuth servers.
 *
 * @return bool true on success, false otherwise
 */
function authenticateWithMicrosoft() {
    global $dbConn, $logger, $configManager;

    $authProviders = $configManager->get("auth_microsoft");

    $authProvidedId = authenticateWithOAuth2(
        'Microsoft',
        $authProviders['client_id'],
        $authProviders['secret'],
        array(
            'wl.basic',
            'wl.emails'
        ),
        'https://apis.live.net/v5.0/me'
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
        $u->setAuthProvider(new UserAuthProvider(UserAuthProvider::MICROSOFT, 'Microsoft'))
            ->setAuthProviderId($authProvidedId)
            ->setType(new UserType(UserType::PROPOSER, 'Proposer'))
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
