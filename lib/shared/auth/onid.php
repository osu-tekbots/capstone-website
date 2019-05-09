<?php

if (!isset($_SESSION)) {
    session_start();
}

/**
 * Authenticate a user using Oregon State University's CAS server, requiring the user's ONID username and password.
 *
 * @return void
 */
function authenticateWithONID() {
    if (isset($_SESSION['auth']['id'])) {
        return $_SESSION['auth']['id'];
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

        $_SESSION['auth'] = array(
            'method' => 'onid',
            'id' => strtolower(extractFromXml('cas:user', $html)),
            'firstName' => extractFromXml('cas:firstname', $html),
            'lastName' => extractFromXml('cas:lastname', $html),
            'email' => extractFromXml('cas:email', $html)
        );

        return $_SESSION['auth']['id'];
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
