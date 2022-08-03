<?php
include_once '../bootstrap.php';

use DataAccess\UsersDao;
use DataAccess\CapstoneProjectsDao;

if(!isset($_SESSION)) {
    session_start();
}

$isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);
if ($isLoggedIn) {
    // Redirect to their profile page
    $redirect = $configManager->getBaseUrl() . 'pages/myProfile.php';
    echo "<script>window.location.replace('$redirect');</script>";
    die();
}


$title = 'Create Account';

include_once PUBLIC_FILES . '/modules/header.php';

$usersDao = new UsersDao($dbConn, $logger);

//$user = $usersDao->getUser($_SESSION['userID']);

// TODO: handle the case where we aren't able to fetch the user
$salutations = $usersDao->getUserSalutations();

$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
/*$projects = $projectsDao->getCapstoneProjectsForUser($user->getId());

$uId = $user->getId();
$uFirstName = $user->getFirstName();
$uLastName = $user->getLastName();
$uAffiliation = $user->getAffiliation();
$uSalutationId = $user->getSalutation()->getId();
$uSalutationName = $user->getSalutation()->getName();
$uMajor = $user->getMajor();
$uPhone = $user->getPhone();
$uEmail = $user->getEmail();
$uTypeName = $user->getType()->getName();*/


include_once PUBLIC_FILES . '/modules/header.php';

?>

<section class="vh-100" style="background-color: #D73F09;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow-2-strong" style="border-radius: 1rem;">
          <div class="px-5 text-center">

            <h3 class="mb-5">Create Account</h3>
			<div class="form-outline mb-4">
              <input type="text" id="userFirstName" class="form-control form-control-lg" required/>
              <label class="form-label" for="userFirstName">First Name</label>
            </div>
			<div class="form-outline mb-4">
              <input type="text" id="userLastName" class="form-control form-control-lg" required/>
              <label class="form-label" for="userLastName">Last Name</label>
            </div>
            <div class="form-outline mb-4">
              <input type="email" id="userEmail" class="form-control form-control-lg" required/>
              <label class="form-label" for="adminEmail">Email</label>
            </div>
            <div class="form-outline mb-4">
              <input type="password" id="adminPassword" class="form-control form-control-lg" required />
              <label class="form-label" for="adminPassword">Password</label>
            </div>
			<div class="form-outline mb-4">
              <input type="password" id="adminPasswordConfirm" class="form-control form-control-lg" required />
              <label class="form-label" for="adminPasswordConfirm">Confirm Password</label>
            </div>
			<div class="form-outline mb-4">
            	<button class="btn btn-dark btn-lg btn-block" type="submit">Login</button>
			</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
include_once PUBLIC_FILES . '/modules/footer.php';
?>

<script defer type="text/javascript">
/**
 * Event handler for a click event on the 'Save' button for user profiles.
 */
function onCreateAccountClick() {

	let data = new FormData(document.getElementById('formCreateUserProfile'));

	let body = {
		action: 'createAccount'
	};
	for(const [key, value] of data.entries()) {
		body[key] = value;
	}

	api.post('/users.php', body).then(res => {
		snackbar(res.message, 'success');
	}).catch(err => {
		snackbar(err.message, 'error');
	});

}
$('#createAccountBtn').on('click', onCreateAccountClick);
</script>


<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>