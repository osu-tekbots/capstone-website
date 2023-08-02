<?php

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);
//ini_set('log_errors', 1);

// setting error logging to be active 
ini_set("log_errors", TRUE); 
ini_set('error_log', '/nfs/cs/info/eecs_www/capstone/submission/don.log');
*/



require 'http.php';
require 'oauth_client.php';

if (!isset($_SESSION)) {
    session_start();
}

/**
 * Authenticate a user using OAuth2.0 authentication with the provided servers.
 * 
 * If a there are query string parameters present in the URL of the page making the authentication request, 
 * they may not be properly handled by OAuth server. Instead, it is recommended that you save any required query string 
 * paramters in a session variable during authentication and read them back out once it is successful.
 * 
 * This function will, on successfuly authentication, set the `$_SESSION['auth']` variable to an associative array with
 * the following keys:
 * - `method`: `'oauth2'`
 * - `id`: the ID provided by Google for the user
 * - `firstName`: the first name of the user
 * - `lastName`: the last name of the user
 * - `email`: the email address of the user
 *
 * @param string $name the name of the OAuth provider
 * @param string $clientId the ID of the client making the authentication request. This ID is provided when registering
 * with the provider to use OAuth2.
 * @param string $secret the key used to authenticate the client request
 * @param string[] $scope the scope of the request provided as an array of URI strings
 * @param string $serverUrl the URL to contact for user information after successful authentication and a token is
 * received
 * @return string|bool the ID provided by the OAuth server for the user on success, false otherwise
 */
function authenticateWithOAuth2($name, $clientId, $secret, $scope, $serverUrl) {
	global $dbConn, $logger, $configManager;

	$logger->info("In OAuth");
	
    // If we have already authenticated, we don't want to waste bandwidth doing it again
    if(isset($_SESSION['auth']['id'])) {
        return $_SESSION['auth']['id'];
    }

    $client = new oauth_client_class;
    $client->server = $name;

    // set the offline access only if you need to call an API
    // when the user is not present and the token may expire
    $client->offline = true;

    $client->debug = true;
    $client->debug_http = true;

    // Construct a redirect URL that will return this the page calling this function after the authentication is
    // complete
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

	$logger->info("pageURL = " . $pageURL);

    // Set the configuration and keys
    
	if ($name == 'Microsoft' || $name == 'github' )
		$client->redirect_uri = "https://eecs.engineering.oregonstate.edu/capstone/submission";
	else
		$client->redirect_uri = $pageURL;

    $client->client_id = $clientId;
    $client->client_secret = $secret;

    // Set the API permissions
    $client->scope = implode(' ', $scope);

    // Begin authentication
    if (($success = $client->Initialize())) {
        if (($success = $client->Process())) {
			$_SESSION['error'] =  "Process() was successful";
            if (strlen($client->authorization_error)) {
                // Authentication failed. 
                $_SESSION['error'] =  "Authentication failed";
				$client->error = $client->authorization_error;
                $success = false;
            } elseif (strlen($client->access_token)) {
                // We got a token. Use it to request information from the authentication server
                $_SESSION['error'] =  "We got a token. Use it to request information from the authentication server";
				$success = $client->CallAPI($serverUrl, 'GET', array(), array('FailOnAccessError' => true), $user);
            } else {
				//Unknown error. Login worked but no access token?
				$logger->info("client->authorization_error: " . $client->authorization_error);
				$logger->info("client->access_token: " . $client->access_token);
				$logger->info("client->request_token_url: " . $client->request_token_url);
				$logger->info("client->access_token_url: " . $client->access_token_url);
				$logger->info("client->dialog_url: " . $client->dialog_url);
				$logger->info("client->oauth_version: " . $client->oauth_version);
				$logger->info("client->debug_output: " . $client->debug_output);
			}
        } else {
			$_SESSION['error'] = "Can not Process()";
		}
        $success = $client->Finalize($success);
    } else {
		$_SESSION['error'] =  "Can not Initialize()";
	}
	
//	$_SESSION['error'] =  ($success == true ? "Success is true" : "Success is false" );
	
    if ($client->exit) {
        return false;
    }
	
    if ($success) {
        // Authentication was successful. Set the necessary SESSION variables and return the ID provided by
        // the authentication server
        $nameSegments = explode(' ', $user->name);
        $nameSegmentsCount = count($nameSegments);

        $_SESSION['auth'] = array(
            'method' => 'oauth2',
            'id' => $user->id,
            'firstName' => $nameSegments[0],
            'lastName' => $nameSegments[$nameSegmentsCount - 1],
            'email' => $user->email,
            'serverName' => $name
        );

        return $_SESSION['auth']['id'];
    } else {
        // Authentication failed
        return false;
    }
}
