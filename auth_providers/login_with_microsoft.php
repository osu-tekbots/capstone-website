<?php
use DataAccess\UsersDao;
use Model\User;
use Model\UserAuthProvider;

session_start();

require('http.php');
require('oauth_client.php');

$authProviders = $configManager->getAuthProviderConfig();

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


$client->client_id = $authProviders['microsoft']['client_id']; 
$application_line = __LINE__;
$client->client_secret = $authProviders['microsoft']['secret'];

$client->scope = 'wl.basic wl.emails';
if (($success = $client->Initialize())) {
    if (($success = $client->Process())) {
        if (strlen($client->authorization_error)) {
            $client->error = $client->authorization_error;
            $success = false;
        } elseif (strlen($client->access_token)) {
            $success = $client->CallAPI(
				'https://apis.live.net/v5.0/me',
				'GET', array(), array('FailOnAccessError'=>true), $user);
        }
    }
    $success = $client->Finalize($success);
}
if ($client->exit) {
    exit;
}
if ($success) {
    $authProviderProvidedId = $user->id;

    $dao = new UsersDao($dbConn, $logger);

    $u = $dao->getUserByAuthProviderProvidedId($authProviderProvidedId);
    if ($u) {
        $_SESSION['userID'] = $u->getId();
        $_SESSION['accessLevel'] = $u->getType()->getName();
        $_SESSION['newUser'] = false;
        // Redirect to the projects page
        echo "<script>window.location.replace('../pages/myProjects.php')</script>";
        die();
    } else {
        $nameSegments = explode(' ', $user->name);
        $nameSegmentsCount = count($nameSegments);
        $u = new User();
        $u->setAuthProvider(new UserAuthProvider(UserAuthProvider::MICROSOFT, 'Microsoft'))
			->setAuthProviderId($authProviderProvidedId)
			->setFirstName($nameSegments[0])
			->setLastName($nameSegments[$nameSegmentsCount - 1])
			->setEmail($user->email);
        $ok = $dao->addNewUser($u);
        // TODO: handle error

        $_SESSION['userID'] = $u->getId();
        $_SESSION['accessLevel'] = $u->getType()->getName();
        $_SESSION['newUser'] = true;

        // Redirect to login page, which will now have a new user portal.
        echo "<script>window.location.replace('../pages/login.php')</script>";
        die();
    }
} else {
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