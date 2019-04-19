<!DOCTYPE html>
<html lang="en">
	<head>
	<?php
	include_once('../includes/header.php')
	?>

  <?php
  	include_once('../db/dbManager.php');
  ?>
	<?php require_once('../modules/redirect.php'); ?>

  <?php
		$result = getUserInfo($_SESSION['userID']);
		$row = $result->fetch_assoc();
		$userID = $row['user_id'];
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$salutation = $row['salutation'];
		$student_id = $row['student_id'];
		$email = $row['email'];
		$phone = $row['phone'];
		$major = $row['major'];
		$affiliation = $row['affiliation'];
		$project_assigned = $row['project_assigned'];
		$type = $row['type'];
?>

		<title> My Profile </title>
	</head>
	<body>
		<?php include_once("../modules/navbar.php"); ?>

	<form>
	<div class="jumbotron jumbotron-fluid">
		<h1 class="display-4">My Profile</h1>
		<hr class="my-4">
		<div class="container bootstrap snippets">
		<div class="row">
			<div class="col-sm-6">
		    <div class="panel-heading">
		      <h4 class="panel-title">User Info</h4>
		    </div>
		      <div class="panel-body">
		        <div class="form-group">
		          <label class="col control-label" for="firstNameText">First Name</label>
		          <div class="col-sm-11">
		            <textarea class="form-control" id="firstNameText" rows="1" required><?php echo($first_name) ?></textarea>
		            </div>
		          </div>
		          <div class="form-group">
		            <label class="col control-label" for="lastNameText">Last Name</label>
		            <div class="col-sm-11">
		              <textarea class="form-control" id="lastNameText" rows="1"><?php echo($last_name) ?></textarea>
		            </div>
		          </div>
							<div class="container">
    						<div class="row">

        					<div class="col-sm-6">
										 <label for="affiliationText">Affiliation</label>
            				<textarea class="form-control" id="affiliationText" rows="1"><?php echo($affiliation) ?></textarea>
        					</div>
        					<div class="col-sm-5">
										 <label for="salutationSelect">Salutation</label>
										 <select class="form-control" id="salutationSelect">
 												<?php
 												echo '<option>' . $salutation . '</option>';
 												if ($salutation != "Mr."){
 													echo '<option>Mr.</option>';
 												}
 												if ($salutation != "Mrs."){
 													echo '<option>Mrs.</option>';
 												}
 												if ($salutation != "Dr."){
 													echo '<option>Dr.</option>';
 												}
 												if ($salutation != "Ms."){
 													echo '<option>Ms.</option>';
 												}
 												if ($salutation != "Miss"){
 													echo '<option>Miss</option>';
 												}
 												if ($salutation != "Prof."){
 													echo '<option>Prof.</option>';
 												}


 												?>
 										</select>
        					</div>
    						</div>
							</div>
							<br>
							<div class="student">
								<label class="col control-label" for="majorText">Major</label>
								<div class="col-sm-11">
									<textarea class="form-control" id="majorText" rows="1"><?php echo($major) ?></textarea>
								</div>
							</div>
							<div class="panel-body">
								<br>
								<div class="col-sm-11">
									<button class="btn btn-large btn-block btn-primary" id="saveProfileBtn" type="button">Save</button>
									<div id="successText" class="successText" style="display:none;">Success!</div>
								</div>
							</div>
		        </div>
		      </div>

		      <div class="col-sm-6">
		        <div class="panel-heading">
		        <h4 class="panel-title">Contact Info</h4>
		        </div>
		        <div class="panel-body">
		          <div class="form-group">
		            <label class="col control-label" for="phoneText">Phone Number</label>
		            <div class="col">
		              <textarea class="form-control" id="phoneText" rows="1" required><?php echo($phone) ?></textarea>
		            </div>
		          </div>
		          <div class="form-group">
		            <label class="col control-label" for="emailText">Email Address</label>
		            <div class="col">
		              <textarea class="form-control" id="emailText" rows="1" required><?php echo($email) ?></textarea>
		            </div>
		          </div>
							<br>
							<div class="panel-heading">
					      <h4 class="panel-title">Account info</h4>
					    </div>
							<hr class="my-4">
		          <div class="form-group">
								<p class="form-control-static">User Type: <?php echo($type) ?> </p>
		            <div class="col">

		            </div>
		          </div>
		          <div class="form-group">
								<?php
								if ($project_assigned != ""){
									echo '<p class="form-control-static">Assigned Project: '. $project_assigned . '</p>';
								}
								?>
		            <div class="col-sm-11">

		            </div>
		          </div>
		        </div>
		      </div>
				</div>

		    </form>
		</div>

<?php include_once("../modules/footer.php"); ?>

</body>
<script type="text/javascript">

	$('#saveProfileBtn').on('click', function (e) {
		profileUserID = "<?php echo $userID; ?>";
		profileFirstName = $('#firstNameText').val(); 
		profileLastName = $('#lastNameText').val();
		profileSalutation = $('#salutationSelect').val();
		profileEmail = $('#emailText').val();
		profilePhone = $('#phoneText').val();
		profileAffiliation = $('#affiliationText').val();
		profileProjectAssigned = $('#projectAssignedText').val();
		profileMajor = $('#majorText').val();
		$.ajax({
			type: 'POST',
			url: '../db/dbManager.php',
			dataType: 'html',
			data: {
					profileUserID: profileUserID,
					profileFirstName: profileFirstName,
					profileLastName: profileLastName,
					profileSalutation: profileSalutation,
					profileEmail: profileEmail,
					profilePhone: profilePhone,
					profileAffiliation: profileAffiliation,
					profileProjectAssigned: profileProjectAssigned,
					profileMajor: profileMajor,
					action: 'saveProfile'},
					success: function(result)
					{
						createSaveText();
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(xhr.responseText);
						alert(thrownError);
					}
		});
	});

 var user_level = "<?php echo $type; ?>";
	if (user_level == "Student"){
		$("div.student").show();
	}

function createSaveText(){
	document.getElementById('successText').style.display = "block";
	setTimeout(fade_out, 2000);
}

function fade_out(){
	$("#successText").fadeOut();
}


</script>


  </html>
