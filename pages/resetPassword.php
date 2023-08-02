<?php
include_once '../bootstrap.php';


if(!isset($_SESSION)) {
    session_start();
}

use DataAccess\UsersDao;
use Model\User;
use Model\UserAuthProvider;

$dao = new UsersDao($dbConn, $logger);
if (!isset($_REQUEST['resetCode']) || !isset($_REQUEST['email'])){
	$redirect = $configManager->getBaseUrl() . 'pages/forgotPassword.php';
	echo "<script>alert('Invalid attempt, missing info.');location.replace('" . $redirect . "');</script>";
	die();
}

if (!$dao->checkLocalUserResetAttempt($_REQUEST['email'], $_REQUEST['resetCode'])){ //Check if request is valid
	$redirect = $configManager->getBaseUrl() . 'pages/forgotPassword.php';
	echo "<script>alert('Invalid attempt, reset request not present');location.replace('" . $redirect . "');</script>";
	die();
} 

if (isset($_REQUEST['newUserPassword'])){ //Request was valid and a password is supplied so we should update it
	//Update password
	$dao->setLocalUserPassword($_REQUEST['email'], $_REQUEST['newUserPassword']);
	//Clear the reset requests for the supplied email
	$dao->deleteLocalUserResetAttempts($_REQUEST['email']);
	
	$redirect = $configManager->getBaseUrl() . 'auth/index.php?provider=local';
	echo "<script>alert('Password Updated! Log in.');location.replace('" . $redirect . "');</script>";
	die();
} 

$title = 'Reset Password';
include_once PUBLIC_FILES . '/modules/header.php';

?>

<section class="vh-100" style="background-color: #D73F09;">
    <form action="./pages/resetPassword.php" method="POST">
        <input type="hidden" name="email" value="<?php echo $_REQUEST['email']; ?>">
		<input type="hidden" name="resetCode" value="<?php echo $_REQUEST['resetCode']; ?>">
		<div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center" style="background-color: #D1D1D1">
                            <h3 class="mb-5">Please enter a new password:</h3>
                            <div class="form-outline mb-4">
                                <label class="form-label" for="newUserPassword"></label>
                                <input type="password" name="newUserPassword" id="newUserPassword" class="form-control form-control-lg" />
                                <br>
                                <input type="checkbox" onclick="togglePasswordVisibility()"> Show Password
                            </div>
                            <button class="btn btn-dark btn-lg btn-block" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    function togglePasswordVisibility() {
        var x = document.getElementById("newUserPassword");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

</script>

<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>