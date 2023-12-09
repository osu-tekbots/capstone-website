<?php
include_once '../bootstrap.php';

use DataAccess\UsersDao;
use Model\User;
use Model\UserAuthProvider;
use Model\UserType;

/**
 * Uses ONID to authenticate the user. 
 * 
 * When the function returns, the user will have been authenticated and the SESSION variable will have been set
 * accordingly.
 *
 * @return void
 */
function authenticateStudent() {
    global $dbConn, $logger;

    include_once PUBLIC_FILES . '/lib/shared/auth/onid.php';
    $onid = authenticateWithONID();

    $dao = new UsersDao($dbConn, $logger);

    $u = $dao->getUserByOnid($onid);
    if ($u) {
        $_SESSION['site'] = 'capstoneSubmission';
        $_SESSION['userID'] = $u->getId();
        $_SESSION['accessLevel'] = $u->getType()->getName();
        $_SESSION['newUser'] = false;

        $u->setDateLastLogin(new DateTime());
        $dao->updateUser($u);
    } else {
        $u = new User();
        $u->setAuthProvider(new UserAuthProvider(UserAuthProvider::ONID, 'ONID'))
            ->setType(new UserType(UserType::PROPOSER, 'Proposer'))
            ->setOnid($onid)
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
    return true;
}
