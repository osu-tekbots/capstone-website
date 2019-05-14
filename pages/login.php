<?php

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
<<<<<<< HEAD
  <br><br>
  <div class="container">
    <div class="row">
      <div class="col-sm-4">
		<?php
		//Only show buttons if the user is not logged in.
		if (!$isLoggedIn) {
		    echo '
			<h3>Student Logins:</h3>
			<hr class="my-4">
			<a class="login" href="pages/login.php?provider=google" style="text-decoration:none;">
				<button id="onidBtn" class="btn btn-lg btn-onid btn-block text-uppercase" type="submit">
					<i class="fas fa-book mr-2"></i> Login with ONID
				</button>
			</a>
			<hr class="my-4">
			<h3>Proposer Logins:</h3>
			<hr class="my-4">
			<a class="login" href="pages/login.php?provider=google" style="text-decoration:none;">
				<button id="googleBtn" class="btn btn-lg btn-google btn-block text-uppercase" type="submit">
					<i class="fab fa-google mr-2"></i> Login with Google
				</button>
			</a>
			<hr class="my-4">
			<a class="login" href="pages/login.php?provider=microsoft" style="text-decoration:none;">
				<button id="microsoftBtn" class="btn btn-lg btn-microsoft btn-block text-uppercase" type="submit">
					<i class="fab fa-microsoft mr-2"></i> Login with Microsoft
				</button>
			</a>
			<hr class="my-4">
			<a class="login" href="pages/login.php?provider=microsoft" style="text-decoration:none;">
				<button id="outlookBtn" class="btn btn-lg btn-outlook btn-block text-uppercase" type="submit">
					<i class="fas fa-envelope mr-2"></i> Login with Outlook
				</button>
			</a>
            <hr class="my-4">';
		}
		?>
	  </div>
	  <div class="col-sm-8">
	  <?php
        //Renders a new user portal. Only shown after first-time authentication.
        $isNewUser = $isLoggedIn && isset($_SESSION['newUser']) && $_SESSION['newUser'];
		if ($isNewUser) {
            $studentId = UserType::STUDENT;
            $proposerId = UserType::PROPOSER;
		    echo "
            <h2>Welcome to Senior Design Capstone!</h2>
            <hr class='my-4'>
            <p>Specify below whether you are a student or a proposer.</p>
            <div class='form-group'>
                <label for='accessLevelSelect'>I am a...</label>
                <select class='form-control' id='accessLevelSelect'>
                    <option value='$studentId'>Student</option>
                    <option value='$proposerId'>Proposer</option>
                </select>
				<br>
				<div id='onidInputDiv'>
					<h6>Note: Students must provide their ONID even if they use a third party authenticator.</h6>
					ONID Username: <input class='form-control' id='onidInput'>
				</div>
            </div>
            <button id='accessLevelSaveBtn' type='button' style='float:right;' class='btn btn-success'>Save</button>
            <br>
            <hr class='my-4'>
            <h2>Below are some quick links to get started!</h2>
            <a href='pages/info.php'>Click here</a> to <b>learn more about this application</b>.
            <br>
            <a href='pages/myProfile.php'>Click here</a> to <b>edit your profile</b>.
            <br>
            <a href='pages/myProjects.php'>Click here</a> to <b>create a new project</b>.
            <br>
            <a href='pages/myProjects.php'>Click here</a> to <b>browse for projects</b>.
			";
		} else {
		    echo '
            <center>
                <br>
                <img src="assets/img/loginImage.jpg" alt="icon" />
                <br/>
            </center>
			';
		}
	  ?>
	  </div>
=======
>>>>>>> e80d70ffa5c7d3dec787acf76299f5086a6a382e

<br><br><br>
<div class="container">
<div class="row">
    <div class="col-sm-4">
        <br>
        <hr class="my-4">
        <h4 class="text-center">Student Login</h4>
        <a class="login" href="auth/index.php?provider=onid" style="text-decoration:none;">
            <button id="onidBtn" class="btn btn-lg btn-warning btn-block text-uppercase" type="submit">
                <i class="fas fa-book mr-2"></i> Login with ONID
            </button>
        </a>
        <hr class="my-4">
        <h4 class="text-center">Project Proposer Login</h4>
        <a class="login" href="auth/index.php?provider=google" style="text-decoration:none;">
            <button id="googleBtn" class="btn btn-lg btn-danger btn-block text-uppercase" type="submit">
                <i class="fab fa-google mr-2"></i> Login with Google
            </button>
        </a>
        <br/>
        <a class="login" href="auth/index.php?provider=microsoft" style="text-decoration:none;">
            <button id="microsoftBtn" class="btn btn-lg btn-success btn-block text-uppercase" type="submit">
                <i class="fab fa-microsoft mr-2"></i> Login with Microsoft
            </button>
        </a>
        <br/>
        <a class="login" href="auth/index.php?provider=github" style="text-decoration:none;">
            <button id="microsoftBtn" class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">
                <i class="fab fa-github mr-2"></i> Login with GitHub
            </button>
        </a>
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
