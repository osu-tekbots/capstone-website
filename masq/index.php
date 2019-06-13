<?php
/**
 * This file is password protected on the Apache Web Server. It allows for local development of an authenticated
 * test user without the need for CAS or other OAuth authentication services, since these services do not permit
 * the use of localhost URLs.
 * 
 * Essentially, we are masquerading as another user while we do development offline.
 */
include_once '../bootstrap.php';

use DataAccess\UsersDao;

session_start();

$dao = new UsersDao($dbConn, $logger);

$redirect = "<script>location.replace('../pages/index.php')</script>";

$masqerading = isset($_SESSION['masq']);
if ($masqerading) {
    $user = $dao->getUser($_SESSION['userID']);
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'start':
        $onid = $_POST['onid'];
        if ($onid . '' != '') {
            $user = $dao->getUserByOnid($onid);
            if ($user) {
                stopMasquerade();
                startMasquerade($user);
                echo $redirect;
                die();
            }
            $message = 'User with the provided ONID not found';
        }
        break;
        
    case 'stop':
        stopMasquerade();
        echo $redirect;
        die();

    default:
        break;
}

/**
 * Stops the current masquerade (if there is one) and restores the original user session variables.
 *
 * @return void
 */
function stopMasquerade() {
    if (isset($_SESSION['masq'])) {
        unset($_SESSION['userID']);
        unset($_SESSION['accessLevel']);
        unset($_SESSION['newUser']);
        if (isset($_SESSION['masq']['savedPreviousUser'])) {
            $_SESSION['userID'] = $_SESSION['masq']['userID'];
            $_SESSION['accessLevel'] = $_SESSION['masq']['accessLevel'];
            $_SESSION['newUser'] = $_SESSION['masq']['newUser'];
        }
        unset($_SESSION['masq']);
    }
}

/**
 * Starts to masquerade as the provided user
 *
 * @param \Model\User $user the user to masquerade as
 * @return void
 */
function startMasquerade($user) {
    $_SESSION['masq'] = array('active' => true);
    if (isset($_SESSION['userID'])) {
        $_SESSION['masq']['savedPreviousUser'] = true;
        $_SESSION['masq']['userID'] = $_SESSION['userID'];
        $_SESSION['masq']['accessLevel'] = $_SESSION['accessLevel'];
        $_SESSION['masq']['newUser'] = $_SESSION['newUser'];
    }
    $_SESSION['userID'] = $user->getId();
    $_SESSION['accessLevel'] = $user->getType()->getName();
    $_SESSION['newUser'] = false;
}
?>

<h1>Senior Design Capstone: Masquerade as Another User</h1>

<?php if ($masqerading): ?>
    <p>Currently masqerading as <strong><?php echo $user->getFirstName() . ' ' . $user->getLastName(); ?></strong></p>
<?php endif; ?>

<?php if (isset($message)): ?>
    <p><?php echo $message ?></p>
<?php endif; ?>

<h3>Masquerade as Existing</h3>
<form method="post">
    <input type="hidden" name="action" value="start" />
    <label for="onid">ONID</label>
    <input required type="text" id="eonid" name="onid" autocomplete="off" />
    <button type="submit">Start Masquerading</button>
</form>

<h3>Stop Masquerading</h3>
<form method="post">
    <input type="hidden" name="action" value="stop" />
    <button type="submit">Stop</button>
</form>



