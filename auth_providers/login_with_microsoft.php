<?php
    require('http.php');
    require('oauth_client.php');
	require_once('../includes/config.php');
	require_once('../db/dbManager.php');

	$client = new oauth_client_class;
	$client->server = 'Microsoft';
	
	// set the offline access only if you need to call an API
    // when the user is not present and the token may expire
    $client->offline = true;
	
	$client->debug = false;
	$client->debug_http = true;
	
	//The redirect_ui, client_id, and client_secret are all required
	//to authenticate with a service. 
	
	$client->redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . dirname(strtok($_SERVER['REQUEST_URI'],'?')) . '/login_with_microsoft.php';
	echo $client->redirect_uri . '<br>';

	
	$client->client_id = $msft_client_id; 
	$application_line = __LINE__;
	$client->client_secret = $msft_client_secret;

	$client->scope = 'wl.basic wl.emails';
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
					'https://apis.live.net/v5.0/me',
					'GET', array(), array('FailOnAccessError'=>true), $user);
			}
		}
		$success = $client->Finalize($success);
	}
	if($client->exit)
		exit;
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
			$userEmail = $user->emails->preferred;
			$authProvider = "Microsoft";

			createUser($userID, $firstName, $lastName, $userEmail, $authProvider);
			$_SESSION['newUser'] = true;
			$_SESSION['accessLevel'] = "Student";
			
			//Redirect to login page, which will now have a new user portal.
			header('Location: ../pages/login.php');
		}
		

	}
	else
	{
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