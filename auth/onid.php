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

    $logger->trace('Including /lib/shared/auth/onid.php');
    include_once PUBLIC_FILES . '/lib/shared/auth/onid.php';
    $logger->trace('Calling authenticateWithONID()');
    $onid = authenticateWithONID();
    $logger->trace('Authenticated with ONID');

    $dao = new UsersDao($dbConn, $logger);
    $logger->trace('Created DAO');
    $u = $dao->getUserByOnid($onid);
    $logger->trace('Completed DAO getUser()');
    if ($u) {
        $logger->trace('Found user: '.$u->getOnid());
        $_SESSION['site'] = 'capstoneSubmission';
        $logger->trace('Set $_SESSION["site"]');
        $_SESSION['userID'] = $u->getId();
        $logger->trace('Set $_SESSION["userID"]');
        $_SESSION['accessLevel'] = $u->getType()->getName();
        $logger->trace('Set $_SESSION["accessLevel"]');
        $_SESSION['newUser'] = false;
        $logger->trace('Set $_SESSION["newUser"]');

        $u->setDateLastLogin(new DateTime());
        $logger->trace('Set date last logged in');
        $dao->updateUser($u);
        $logger->trace('Updated user');
        $logger->info('Just logged in '.$_SESSION['userID']);
    } else {
        $logger->trace('User not found; creating new user');
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
    $logger->trace('Returning true from authenticateStudent()');
    return true;
}
