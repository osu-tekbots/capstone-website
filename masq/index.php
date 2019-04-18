<?php
use DataAccess\UsersDao;

/**
 * This file is password protected on the Apache Web Server. It allows for local development of an authenticated
 * test user without the need for CAS or other OAuth authentication services, since these services do not permit
 * the use of localhost URLs.
 * 
 * Essentially, we are masquerading as another user while we do development offline.
 */
$dao = new UsersDao($dbConn, $logger);

$redirect = "<script>location.replace('./pages/index.php')</script>";

$action = $_POST['action'];

switch ($action) {
    case 'start':
        $onid = $_POST['onid'];
        if ($onid . '' != '') {
            $user = $dao->getUserByOnid($onid);
            if ($user) {
                $_SESSION['userId'] = $user->getId();
                $_SESSION['accessLevel'] = $user->getType()->getName();
                $_SESSION['newUser'] = false;
                echo $redirect;
                die();
            }
        }
        break;
        
    case 'stop':
        unset($_SESSION['userId']);
        unset($_SESSION['accessLevel']);
        unset($_SESSION['newUser']);
        echo $redirect;
        break;

    default:
        break;
}
?>

<h1>Masquerade as Another User</h1>

<h3>Masquerade as Existing</h3>
<form method="post">
    <input type="hidden" name="action" value="start" />
    <label for="onid">ONID></label>
    <input required type="text" id="eonid" name="onid" autocomplete="off" />
    <button type="submit">Start Masquerading</button>
</form>

<h3>Stop Masquerading</h3>
<form method="post">
    <input type="hidden" name="action" value="stop" />
    <button type="submit">Stop</button>
</form>



