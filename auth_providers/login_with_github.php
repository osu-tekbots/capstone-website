<?php
/* FIXME PLEASE IMPLEMENT */
/*
	require_once dirname(__FILE__).'/../db/dbm.php';
    require 'http.php';
    require 'oauth_client.php';
    require_once dirname(__FILE__).'/../auth.php';

	$client = new oauth_client_class;
	$client->debug = false;
	$client->debug_http = true;
	$client->server = 'github';
	$client->redirect_uri = $local_addr.'/auth_providers/login_with_github.php';

	$client->client_id = $github_client_id; $application_line = __LINE__;
	$client->client_secret = $github_client_secret;

	$client->scope = 'user:email';
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
					'https://api.github.com/user',
					'GET', array(), array('FailOnAccessError'=>true), $user);
			}
		}
		$success = $client->Finalize($success);
	}
	
	if($client->exit)
		exit;	
	

	if($success)
	{
		// Add user to db.
		$utab = new Users();
		if(!$utab->exists($user->id))
		{
			$r = $utab->create_user($user->id, 
				               $user->name,
				               '',
				               $user->email,
				               '',
				               $user->company,
				               $client->server);
			$_SESSION['new'] = True;
			$_SESSION['success'] = $r;
		}
		else
		{
			$_SESSION['new'] = False;
			$_SESSION['success'] = True;
		}
		$_SESSION['uid'] = &$user->id;
		header('Location: '. $local_addr);
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
	}*/

?>