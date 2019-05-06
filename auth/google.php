<?php
use DataAccess\UsersDao;
use Model\User;
use Model\UserAuthProvider;

session_start();

require 'http.php';
require 'oauth_client.php';

/**
 * Authenticates the user using OAuth2 Google Authentication 
 *
 * @return void
 */
function authenticateWithGoogle() {
    global $configManager, $dbConn, $logger;

    $authProviders = $configManager->getAuthProviderConfig();

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

    $client->client_id = $authProviders['google']['client_id']; 
    $application_line = __LINE__;
    $client->client_secret = $authProviders['google']['secret'];

    if (strlen($client->client_id) == 0 || strlen($client->client_secret) == 0) {
        die(
        'Please go to Google APIs console page http://code.google.com/apis/console in the API access tab, ' .
        'create a new client ID, and in the line ' . $application_line .
        ' set the client_id to Client ID and client_secret with Client Secret. ' .
        'The callback URL must be ' . $client->redirect_uri . ' but make sure ' .
        'the domain is valid and can be resolved by a public DNS.'
        );
    }

    /* API permissions
     */
    $client->scope = 'https://www.googleapis.com/auth/userinfo.email ' . 'https://www.googleapis.com/auth/userinfo.profile';

    if (($success = $client->Initialize())) {
        if (($success = $client->Process())) {
            if (strlen($client->authorization_error)) {
                $client->error = $client->authorization_error;
                $success = false;
            } elseif (strlen($client->access_token)) {
                $success = $client->CallAPI(
                'https://www.googleapis.com/oauth2/v1/userinfo',
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
            $u->setAuthProvider(new UserAuthProvider(UserAuthProvider::GOOGLE, 'Google'))
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
    }
}
