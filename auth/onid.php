<?php
use DataAccess\UsersDao;
use Model\UserAuthProvider;

session_start();

/**
 * Authenticate a user using Oregon State University's CAS server, requiring the user's ONID username and password.
 *
 * @return void
 */
function authenticateWithONID() {
    if (isset($_SESSION['onid'])) {
        return;
    }

    $pageURL = 'http';
    if ($_SERVER['HTTPS'] == 'on') {
        $pageURL .= 's';
    }
    $pageURL .= '://';

    if ($_SERVER['SERVER_PORT'] != '80') {
        $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['SCRIPT_NAME'];
    } else {
        $pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
    }

    $pageURL .= '?provider=onid';

    $ticket = $_REQUEST['ticket'];

    if ($ticket . '' != '') {
        $url = 'https://login.oregonstate.edu/cas/serviceValidate?ticket=' . $ticket . '&service=' . $pageURL;
        $html = file_get_contents($url);

        $_SESSION['onid'] = strtolower(extractFromXml('cas:user', $html));

        $firstName = extractFromXml('cas:firstname', $html);
        $lastName = extractFromXml('cas:lastname', $html);
        $email = extractFromXml('cas:email', $html);

        // Check to see if the user already exists in the database. If they don't, create a new entry
        global $dbConn, $logger;
        $dao = new UsersDao($dbConn, $logger);
        $user = $dao->getUserByOnid($_SESSION['onid']);
        if (!$user) {
            $user = new Model\User();
            $user->setOnid($_SESSION['onid'])
                ->setAuthProvider(new UserAuthProvider(UserAuthProvider::ONID, 'ONID'))
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setDateLastLogin(new DateTime());
            $dao->addNewUser($user);
            // TODO: add failure check here
        } else {
            // Update their last login
            $user->setLastLogin(new DateTime());
            $dao->updateUser($user);
            // TODO: add failure check here
        }

        return;
    } else {
        $url = 'https://login.oregonstate.edu/cas/login?service=' . $pageURL;
        echo "<script>location.replace('" . $url . "');</script>";
        die();
    }
}

function extractFromXml($key, $xml) {
    $pattern = '/\\<' . $key . '\\>([a-zA-Z0-9@\.]+)\\<\\/' . $key . '\\>/';
    preg_match($pattern, $xml, $matches);
    if ($matches && count($matches) > 1) {
        return $matches[1];
    }
    return false;
}
