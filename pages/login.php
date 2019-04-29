<?php
use Model\UserType;

session_start();

//$queryDict is populated from URL arguments.
parse_str($_SERVER['QUERY_STRING'],$queryDict);

if (isset($queryDict['provider'])) {
    switch ($queryDict['provider']) {
    case 'google':
	  //Perform O_AUTH client authentication. Reference the README in the ./ directory
	  //for more information.
      header('Location: ../auth_providers/login_with_google.php');
      break;
    case 'microsoft':
      header('Location: ../auth_providers/login_with_microsoft.php');
      break;
    case 'github':
	  //Future Implementation Required here to handle github authentication.
      header('Location: ../auth_providers/login_with_github.php');
      break;
	case 'logout':
	  //Handle edge case of logout outside of pages directory.
	  if (getcwd() == '/nfs/ca/info/eecs_www/education/capstone/newcapstone') {
	      header('Location: ./pages/logout.php');
	  } else {
	      header('Location: ./logout.php');
	  }
      break;
    default:
      header('Location: ./');
      break;
  }
}

// The user is already authenticated or none of the arguments are provided, proceed with the login page display

$title = 'Login';
include_once PUBLIC_FILES . '/modules/header.php';

$isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);

?>
  <br><br><br>
  <div class="container">
    <div class="row">
      <div class="col-sm-4">
		<?php
		//Only show buttons if the user is not logged in.
		if (!$isLoggedIn) {
		    echo '
			<br>
            <hr class="my-4">
			<a class="login" href="pages/login.php?provider=google" style="text-decoration:none;">
				<button id="onidBtn" class="btn btn-lg btn-onid btn-block text-uppercase" type="submit">
					<i class="fas fa-book mr-2"></i> Login with ONID
				</button>
			</a>
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
                <a href="pages/myProfile.php">Edit your profile</a>.
            </center>
			';
		}
	  ?>
	  </div>

	<?php echo '<input id="userIDHeader" style="display:none;" value="' . $_SESSION['userID'] . '"></input>'; ?>
    </div>
  </div>


<script type="text/javascript">
/**
 * Event handler for after a user selects their type from the login page. They are able to select their type the
 * first time they log in.
 */
function onUpdateUserType() {

    let body = {
        action: 'updateUserType',
        userId: $('#userIDHeader').val(),
        typeId: $('#accessLevelSelect').val()
    };

    api.post('/users.php', body).then(res => {
        window.location.replace('pages/myProfile.php');
    }).catch(err => {
        snackbar(err.message, 'error');
    });

}
$('#accessLevelSaveBtn').on('click', onUpdateUserType);
</script>

<?php
include_once PUBLIC_FILES . '/modules/footer.php';
?>
