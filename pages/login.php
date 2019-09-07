<?php
include_once '../bootstrap.php';

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

$title = 'Login';
include_once PUBLIC_FILES . '/modules/header.php';

?>

<br><br><br>
<div class="container">
<div class="row">
    <div class="col-sm-4">
        <br>
        <hr class="my-4">
        <h4 class="text-center">Student Login</h4>
        <p class="text-center">*Logging in this method does not allow you to create projects*</p>
        <a class="login" href="auth/index.php?provider=onid" style="text-decoration:none;">
            <button id="onidBtn" class="btn btn-lg btn-warning btn-block text-uppercase" type="submit">
                <i class="fas fa-book mr-2"></i> Student Login (ONID)
            </button>
        </a>
        <hr class="my-4">
        <h4 class="text-center">Project Proposer Login</h4>
        <a class="login" href="auth/index.php?provider=google" style="text-decoration:none;">
            <button id="googleBtn" class="btn btn-lg btn-danger btn-block text-uppercase" type="submit">
                <i class="fab fa-google mr-2"></i> Proposer Login (Google)
            </button>
        </a>
        <br/>
        <!--
        <a class="login" href="auth/index.php?provider=microsoft" style="text-decoration:none;">
            <button id="microsoftBtn" class="btn btn-lg btn-success btn-block text-uppercase" type="submit">
                <i class="fab fa-microsoft mr-2"></i> Login with Microsoft
            </button>
        </a>
        <br/>
        -->
		<!--
        <a class="login" href="auth/index.php?provider=github" style="text-decoration:none;">
            <button id="microsoftBtn" class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">
                <i class="fab fa-github mr-2"></i> Login with GitHub
            </button>
        </a>
		-->
        <hr class="my-4">
    </div>
	<div class="col-sm-8">
        <center>
            <br>
            <img src="assets/img/loginImage.jpg" alt="icon" />
            <br/>
        </center>
	</div>
</div>
</div>

<?php
include_once PUBLIC_FILES . '/modules/footer.php';
?>
