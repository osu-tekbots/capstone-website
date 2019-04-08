<?php

    require('http.php');
    require('oauth_client.php');
	require_once('../includes/config.php');
	require_once('../db/dbManager.php');

    $client = new oauth_client_class;
    $client->server = 'Google';

    // set the offline access only if you need to call an API
    // when the user is not present and the token may expire
    $client->offline = true;

    $client->debug = false;
    $client->debug_http = true;
	
	//The redirect_ui, client_id, and client_secret are all required
	//to authenticate with a service. 
	
    $client->redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . dirname(strtok($_SERVER['REQUEST_URI'],'?')) . '/login_with_google.php';

    $client->client_id = $google_client_id; 
	$application_line = __LINE__;
    $client->client_secret = $google_client_secret;
	
    if(strlen($client->client_id) == 0
    || strlen($client->client_secret) == 0)
        die('Please go to Google APIs console page '.
            'http://code.google.com/apis/console in the API access tab, '.
            'create a new client ID, and in the line '.$application_line.
            ' set the client_id to Client ID and client_secret with Client Secret. '.
            'The callback URL must be '.$client->redirect_uri.' but make sure '.
            'the domain is valid and can be resolved by a public DNS.');

    /* API permissions
     */
    $client->scope = 'https://www.googleapis.com/auth/userinfo.email ' . 'https://www.googleapis.com/auth/userinfo.profile';
    
	if(($success = $client->Initialize()))
    {
        if(($success = $client->Process()))
        {
            if(strlen($client->authorization_error))
            {
                $client->error = $client->authorization_error;
                $success = false;
            }
            elseif(strlen($client->access_token))
            {
                $success = $client->CallAPI(
                    'https://www.googleapis.com/oauth2/v1/userinfo',
                    'GET', array(), array('FailOnAccessError'=>true), $user);
            }	
        }
        $success = $client->Finalize($success);
    }
    if($client->exit){
        exit;
	}
    if($success){
		$_SESSION['userID'] = $user->id;
		
		if(userExists($user->id)){
			$_SESSION['newUser'] = false;
			$result = getUserInfo($user->id);
			$row = $result->fetch_assoc();
			$_SESSION['accessLevel'] = $row['type'];
			$_SESSION['userID'] = $row['user_id'];
			
			header('Location: ../pages/myProjects.php');
		}
		else{
			//Create a new user in the database.
			$userID = $user->id;
			$nameSegments = explode(" ", $user->name);
			$nameSegmentsCount = count($nameSegments);
			$firstName = $nameSegments[0];
			$lastName = $nameSegments[$nameSegmentsCount-1];
			$userEmail = $user->email;
			$authProvider = "Google";

			createUser($userID, $firstName, $lastName, $userEmail, $authProvider);
			$_SESSION['newUser'] = true;
			$_SESSION['accessLevel'] = "Student";
			
			//Redirect to login page, which will now have a new user portal.
			header('Location: ../pages/login.php');
		}
		

    }
    else{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>OAuth client error</title>
</head>
<body>
<h1>OAuth client error</h1>
<pre>Error: <?php echo HtmlSpecialChars($client->error); ?></pre>
</body>
</html>
<?php
    }

?> 