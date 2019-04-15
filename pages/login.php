<?php
//$queryDict is populated from URL arguments.
parse_str($_SERVER['QUERY_STRING'],$queryDict);

if(isset($queryDict['provider'])){
  switch($queryDict['provider']){
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
	  if(getcwd() == '/nfs/ca/info/eecs_www/education/capstone/newcapstone'){
		header('Location: ./pages/logout.php');
	  }
	  else{
	    header('Location: ./logout.php');
	  }
      break;
    default:
      header('Location: ./');
      break;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

	<?php include_once('../includes/header.php'); ?>
	<title>Login</title>
</head>

<?php require_once('../db/dbManager.php'); ?>

<body>
  <?php include_once("../modules/navbar.php"); ?>
  <br><br><br>
  <div class="container">
    <div class="row">
      <div class="col-sm-4">
		<?php
		//Only show buttons if the user is not logged in.
		if(!array_key_exists("userID",$_SESSION) || $_SESSION['userID'] == '' ){
			echo '<br>
      <hr class="my-4">
      <a class="login" href="./login.php?provider=google" style="text-decoration:none;"><button id="onidBtn" class="btn btn-lg btn-onid btn-block text-uppercase" type="submit"><i class="fas fa-book mr-2"></i> Login with ONID</button></a>
			<hr class="my-4">
			<a class="login" href="./login.php?provider=google" style="text-decoration:none;"><button id="googleBtn" class="btn btn-lg btn-google btn-block text-uppercase" type="submit"><i class="fab fa-google mr-2"></i> Login with Google</button></a>
			<hr class="my-4">
			';

			echo '
			<a class="login" href="./login.php?provider=microsoft" style="text-decoration:none;"><button id="microsoftBtn" class="btn btn-lg btn-microsoft btn-block text-uppercase" type="submit"><i class="fab fa-microsoft mr-2"></i> Login with Microsoft</button></a>
			<hr class="my-4">';

      echo '
      <a class="login" href="./login.php?provider=microsoft" style="text-decoration:none;"><button id="outlookBtn" class="btn btn-lg btn-outlook btn-block text-uppercase" type="submit"><i class="fas fa-envelope mr-2"></i> Login with Outlook</button></a>
      <hr class="my-4">';

		}
		?>
	  </div>
	  <div class="col-sm-8">
	  <?php
	    //Initial session variables are instantiated in ./auth_providers/login_with_google.php
	  	//Renders a new user portal. Only shown after first-time authentication.
		if(array_key_exists("userID", $_SESSION) && $_SESSION['newUser']){
			echo '
				<h2>Welcome to Senior Design Capstone!</h2>
				<hr class="my-4">
				<p>Specify below whether you are a student or a proposer.</p>
				<div class="form-group">
					<label for="accessLevelSelect">I am a...</label>
					<select class="form-control" id="accessLevelSelect">
						<option>Student</option>
						<option>Proposer</option>
					</select>
				</div>
				<button id="accessLevelSaveBtn" type="button" style="float:right;" class="btn btn-success">Save</button>
				<br>
				<hr class="my-4">
				<h2>Below are some quick links to get started!</h2>
				<a href="./info.php">Click here</a> to <b>learn more about this application</b>.
				<br>
				<a href="./myProfile.php">Click here</a> to <b>edit your profile</b>.
				<br>
				<a href="./myProjects.php">Click here</a> to <b>create a new project</b>.
				<br>
				<a href="./myProjects.php">Click here</a> to <b>browse for projects</b>.
			';
		}
    else {
      echo '
      <center>
      <br>
        <img src="../images/loginImage.jpg" alt="icon" />
      </center>

			';
    }
	  ?>
	  </div>

	<?php echo '<input id="userIDHeader" style="display:none;" value="' . $_SESSION['userID'] . '"></input>'; ?>
    </div>
  </div>
  <?php include_once("../modules/footer.php"); ?>
</body>


<script type="text/javascript">
$('#accessLevelSaveBtn').on('click', function (e) {
	accessLevelSelected = $('#accessLevelSelect').val();
	userID = $('#userIDHeader').val();

	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				accessLevelSelected : accessLevelSelected,
				userID: userID,
				action: 'editAccessLevel'},
				success: function(result)
				{
					window.location.href = "./myProfile.php";
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(xhr.responseText);
					alert(thrownError);
				}
	});
});
</script>

</html>
