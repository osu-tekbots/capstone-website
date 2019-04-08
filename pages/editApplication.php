<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('../includes/header.php') ?>
<title>Edit Application</title>
</head>

<?php require_once('../db/dbManager.php'); ?>
<?php require_once('../modules/redirect.php'); ?>

<?php
	$applicationID;
	if (isset($_GET['id'])) {
		$applicationID = $_GET['id'];
		$result = getApplication($applicationID);
		$row = $result->fetch_assoc();

		//Validate User.
		if(($row['user_id'] == $_SESSION['userID']) || (array_key_exists("accessLevel", $_SESSION) && $_SESSION['accessLevel'] == "Admin")){
			$validUserCredentials = true;
		}
		else{
			//Redirect if user is not allowed to visit this page.
			header("Location: ./index.php");
			exit();
		}

	}else {
		header("Location: ../"); /* Redirect browser */
		exit();
	}
?>

	<?php
		function buildEditApplication($applicationID){

			$result = getApplication($applicationID);
			$row = $result->fetch_assoc();
			
			$justification = $row['justification'];
			$time_available = $row['time_available'];
			$skill_set = $row['skill_set'];
			$external_link = $row['external_link'];
			
			$title = $row['title'];
			$description = $row['description'];
			$motivation = $row['motivation'];
			$objectives = $row['objectives'];
			$minQualifications = $row['minQualifications'];
			$prefQualifications = $row['prefQualifications'];
			$deliverables = $row['deliverables'];
			$website = $row['website'];
			$video = $row['video'];
			$startBy = $row['startBy'];
			$completeBy = $row['completeBy'];
			$image = $row['image'] != NULL ? $row['image'] : "capstone.jpg";
			
			$firstName = $row['first_name'];
			$lastName = $row['last_name'];
			
			echo '
			<br>
			<div class="container-fluid">
				<div class="row">
					<div class="col-sm-8 jumbotron scroll">
						<div class="row">
							<div class="col-sm-7">
								<h2>Application ' . $applicationID . '</h2>
								<h4>For: ' . $title . '</h4>
								<h5>By: ' . $firstName . ' ' . $lastName . '</h5>
							</div>
							<div id="cssloader" class="col-sm-1">
							</div>
							<div class="col-sm-4">
								<button id="saveApplicationDraftBtn" class="btn btn-success capstone-nav-btn" type="button" >Save Application Draft</button>
								<button name="submitButtonPressed" id="submitBtn" class="btn btn-primary capstone-nav-btn" type="button">Submit</button>
								<div id="successText" class="successText" style="display:none;">Successfully submitted application!</div>
								<div id="errorTextDiv" style="color:red;"></div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6"> 
								<div class="form-group">
									<label for="justificationText">Justification <font size="2" style="color:red;">*required</font></label>
									<textarea class="form-control" id="justificationText" rows="6">' . $justification . '</textarea>
								</div>
								<div class="form-group">
									<label for="externalLinkText">External Link </label>
									<textarea class="form-control" id="externalLinkText" rows="1">' . $external_link . '</textarea>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="skillSetText">Skill Set <font size="2" style="color:red;">*required</font></label>
									<textarea class="form-control" id="skillSetText" rows="5">' . $skill_set . '</textarea>
								</div>
								<div class="form-group">
									<label for="timeAvailableText">Time Available <font size="2" style="color:red;">*required</font></label>
									<textarea class="form-control" id="timeAvailableText" rows="3">' . $time_available . '</textarea>
								</div>

							</div>
						</div>
					</div>
					<div class="col-sm-4 scroll">
						<br>
						<h2>'. $title .'</h2> 
						<p>'. $description .'</p>
						<br><br>
						<h5>Motivation:</h5>
						<p>'. $motivation .'</p>
						<h5>Objectives:</h5>
						<p>'. $objectives .'</p>
						<h5>Minimum Qualifications:</h5>
						<p>'. $minQualifications .'</p>
						<h5>Preferred Qualifications:</h5>
						<p>'. $prefQualifications .'</p>
						<h5>Deliverables:</h5>
						<p>'. $deliverables .'</p>
						<h5>Website:</h5>
						<p>'. $website .'</p>
						<h5>Video:</h5>
						<p>'. $video .'</p>
					</div>
				</div>
			</div>
			';
		}

	?>
<body>
	<?php include_once('../modules/navbar.php'); ?>
	<?php buildEditApplication($applicationID); ?>
	<?php include_once("../modules/footer.php"); ?>

 </body>

<script type="text/javascript">


class Application {
  constructor() {
    this.justification = $('#justificationText').val();
	this.skill_set = $('#skillSetText').val();
	this.external_link = $('#externalLinkText').val();
	this.time_available = $('#timeAvailableText').val();
	this.id = <?php echo $applicationID?>;
  }
}

//Generates the save icon animation.
function createSaveIcon(){
	loaderDivText = '<div class="loaderdiv"><span class="save-icon"><span class="loader"></span><span class="loader"></span><span class="loader"></span></span></div>';
	$('#cssloader').html(loaderDivText);
}

function createSaveText(){
	document.getElementById('successText').style.display = "block";
	setTimeout(fade_out, 2000);
}

function fade_out(){
	$("#successText").fadeOut();
}

function displayErrorText(idName, errorText){
	if($(idName).val() == ""){
		$('#errorTextDiv').text(errorText);
		return true;
	}
	else{
		$('#errorTextDiv').text("");
		return false;
	}
}

$('#saveApplicationDraftBtn').on('click', function (e) {
	if(displayErrorText('#justificationText', "Please provide justification.")){
		return;
	}
	if(displayErrorText('#skillSetText', "Please provide your skillset.")){
		return;
	}
	if(displayErrorText('#timeAvailableText', "Please provide your availability.")){
		return;
	}
	
	A = new Application();

	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				A: A,
				action: 'saveApplicationDraft'},
				success: function(result)
				{
					createSaveIcon();
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(xhr.responseText);
					alert(thrownError);
				}
	});
	
});


$('#submitBtn').on('click', function (e) {
	if(displayErrorText('#justificationText', "Please provide justification.")){
		return;
	}
	if(displayErrorText('#skillSetText', "Please provide your skillset.")){
		return;
	}
	if(displayErrorText('#timeAvailableText', "Please provide your availability.")){
		return;
	}
	
	A = new Application();
	
	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				A: A,
				action: 'submitApplication'},
				success: function(result)
				{
					createSaveText();
					createSaveIcon();
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
