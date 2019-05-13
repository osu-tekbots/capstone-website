<?php

if (!isset($_SESSION)) {
    session_start();
}

/**
 * Authenticate a user using Oregon State University's CAS server.
 * 
 * This will require the user's ONID username and password. If a there are query string parameters present in the URL
 * of the page making the authentication request, they will not be properly handled by the CAS server. Instead, it
 * is recommended that you save any required query string paramters in a session variable during authentication and
 * read them back out once it is successful.
 * 
 * This function will, on successfuly authentication, set the `$_SESSION['auth']` variable to an associative array with
 * the following keys:
 * - `method`: `'onid'`
 * - `id`: the ONID of the user
 * - `firstName`: the first name of the user
 * - `lastName`: the last name of the user
 * - `email`: the email address of the user
 *
 * @return string the ONID for the user
 */
function authenticateWithONID() {
    if (isset($_SESSION['auth']['id'])) {
        return $_SESSION['auth']['id'];
    }

    $pageURL = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $pageURL .= 's';
    }
    $pageURL .= '://';

    if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
        $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['SCRIPT_NAME'];
    } else {
        $pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
    }

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

/**
 * Fetches the value of the key from the XML structure.
 * 
 * Text-based search, not tree based.
 *
 * @param string $key the XML tag name to search for
 * @param string $xml the XML to search
 * @return string the contents of the XML tag with the provided key name
 */
function extractFromXml($key, $xml) {
    $pattern = '/\\<' . $key . '\\>([a-zA-Z0-9@\.]+)\\<\\/' . $key . '\\>/';
    preg_match($pattern, $xml, $matches);
    if ($matches && count($matches) > 1) {
        return $matches[1];
    }
    return false;
}
