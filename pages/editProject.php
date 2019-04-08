<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('../includes/header.php') ?>
<title>Edit Project</title>
</head>

<?php require_once('../db/dbManager.php'); ?>

<?php
	$projectID;
	$validUserCredentials = false;
	if (isset($_GET['id'])) {
		$projectID = $_GET['id'];
		$result = getSingleProject($projectID); 
		$row = $result->fetch_assoc();
		$proposer_id = $row['proposer_id'];

		//Validate User.
		if(($row['proposer_id'] == $_SESSION['userID']) || (array_key_exists("accessLevel", $_SESSION) && $_SESSION['accessLevel'] == "Admin")){
			$validUserCredentials = true;
		}
		else{
			//Redirect if user is not allowed to visit this page.
			header("Location: ./index.php");
			exit();
		}

	}else {
		//Redirect if user is not allowed to visit this page.
		header("Location: ./index.php");
		exit();
	}
?>

	<?php
		/*********************************************************************************
		* Function Name: buildEditProject()
		* Input: Project ID which is passed as URL argument.
		* Output: Generates Edit Project interface dynamically.
		*********************************************************************************/
		function buildEditProject($projectID){

			$result = getSingleProject($projectID);
			$row = $result->fetch_assoc();
			$title = $row['title'];
			$description = $row['description'];
			$motivation = $row['motivation'];
			$objectives = $row['objectives'];
			$minQualifications = $row['minimum_qualifications'];
			$prefQualifications = $row['preferred_qualifications'];
			$nda = $row['NDA/IP'];
			$website = $row['website'];
			$video = $row['video'];
			$additionalEmails = $row['additional_emails'];
			$startBy = $row['start_by'];
			$completeBy = $row['complete_by'];
			$type = $row['type'];
			$focus = $row['focus'];
			$compensation = $row['compensation'];
			$number_groups = $row['number_groups'];
			$comments = $row['comments'];

			$status = $row['status'];
			$category = $row['category'];
			$is_hidden = $row['is_hidden'];

			$image = $row['image'] != NULL ? $row['image'] : "capstone.jpg";

			//Keywords are in the database as comma separated values.
			//EX: "Android Development, C++, Computer Vision, "
			$keywords = explode(",", $row['keywords']);


			/*********************************************************************************
			* Tool Tip Descriptions
			*********************************************************************************/
			$projectTitleInputToolTip = "";
			$saveProjectDraftBtnToolTip = "";
			$submitForApprovalBtnToolTip = "Submit your project for approval. Once your project has been approved, you will receive a confirmation email indicating that it is available for public viewing. ";
			$imgBtnToolTip = "Upload an image to be accompanied with your project when browsing. ";
			$projectTypeSelectToolTip = "Capstone is the default category option and should be used for all projects related to Senior Design Capstone.";
			$projectFocusSelectToolTip = "";
			$compensationSelectToolTip = "";
			$projectDescriptionTextToolTip = "";
			$motivationTextToolTip = "";
			$objectivesTextToolTip = "";
			$keywordsToolTip = "Type keywords and hit 'Enter' between keywords.";
			$minQualificationsTextToolTip = "Please enter a list of skills or prior experience needed to complete this project.";
			$prefQualificationsTextToolTip = "";
			$ndaSelectToolTip = "";
			$websiteTextToolTip = "Enter a URL to link to relevant information.";
			$videoTextToolTip = "Enter a URL to link to relevant information.";
			$additionalEmailsTextToolTip = "Enter additional emails (separated by a semi-colon) from your organization that should also receive updates on this project.";
			$startByTextToolTip = "";
			$completeByTextToolTip = "";
			$numberGroupsDesiredTextToolTip = "How many student teams would you like to work on your project? ";
			$commentsTextToolTip = "Enter any comments you would like the admins to see.";

			//Genereate dynamic client-side HTML.
			echo '
			<br>
			<div class="container-fluid">
			<div class="row">
				<div class="col-sm-8">
					Project Title:<font size="2" style="color:red;">*required</font>
					<input id="projectTitleInput" value="' . $title . '" class="form-control form-control-lg" type="text" placeholder="Project Title" data-toggle="tooltip" data-placement="auto" title="' . $projectTitleInputToolTip . '">
				</div>
				<div class="col-sm-4">
					<div class="row">
						<div class="col-sm-1"></div>
						<div id="cssloader" class="col-sm-2">
						</div>
						<div class="col-sm-9">';
						if(array_key_exists("accessLevel", $_SESSION) && $_SESSION['accessLevel'] != "Admin"){
							//This section appears when the proposer of the project is viewing the project.
							echo '
								<button id="saveProjectDraftBtn" class="btn btn-success capstone-nav-btn" type="button" data-toggle="tooltip" data-placement="bottom" title="' . $saveProjectDraftBtnToolTip . '">Save Project Draft</button>
								<button name="submitButtonPressed" id="submitForApprovalBtn" class="btn btn-primary capstone-nav-btn" type="button" data-toggle="tooltip" data-placement="bottom" title="' . $submitForApprovalBtnToolTip . '">Submit for Approval</button>
								<div id="errorTextDiv" style="color:red;"></div>
								<div id="successText" class="successText" style="display:none;">Successfully submitted project!</div>
								';
						}
						else{
							//This section appears when an Admin is reviewing the project.
							echo'
							<button id="saveProjectDraftBtn" class="btn btn-success capstone-nav-btn" type="button" data-toggle="tooltip" data-placement="bottom" title="' . $saveProjectDraftBtnToolTip . '">Save Project Draft</button>
							<div id="errorTextDiv" style="color:red;"></div>
							';
						}
						
						echo'
						</div>
					</div>
				</div>
			</div>
			<br>
			';

			echo '<div class="row">

			<div class="col-sm-3">
				<h3 id="proposerNameHeader style="display:none;">' . $proposerName . '</h3>
				<h3 id="proposerIDHeader" style="display:none;">' . $userID . '</h3>
				<h3 id="projectIDHeader" style="display:none;">' . $id . '</h3>
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-btn">
							<span class="btn btn-outline-secondary btn-file" data-toggle="tooltip" data-placement="bottom" title="' . $imgBtnToolTip . '">
								Browse… <input type="file" id="imgInp">
							</span>
						</span>
						<input type="text" class="form-control" id="nameOfImageInput" value="' . $image . '" readonly>
					</div>
				<br>
				<style>
					.data-img{
						background-color:#D1D1D1;
						width:40px;
						height:40px;
					}
				</style>
				<div id="indexDiv" style="width:100%;max-height:200px;display:grid;">
					<select id="defaultImageSelect" class="image-picker show-html">
					  <option data-img-src="../images/' . $image . '" data-img-class="first data-img" value="x' . $image . '">  Page 1  </option>';
					  
					  //$defaultImageNum = 1;
					  function createDefaultImageOption($imageName, $defaultImageNum){
						  //global $defaultImageNum;
						  echo '<option data-img-src="../images/' . $imageName . '" data-img-class="data-img" value="' . $defaultImageNum . $imageName . '">' . $imageName . '</option>';
						  //$defaultImageNum += 1;
					  }
					   
					   createDefaultImageOption("capstone.jpg", 1);
					   createDefaultImageOption("loginImage.jpg", 2);
					   createDefaultImageOption("light.jpg", 3);
					   createDefaultImageOption("loginImage.jpg", 4);
					echo '
					</select>

					<script type="text/javascript">
					$("select").imagepicker();
					</script>
				</div>
					<img id="img-upload" max-width="400px;" max-height="200px;" src="../images/' . $image . '"/>
				</div>
				
				<div id="helpDiv">
					<a href="mailto:heer@oregonstate.edu" target="_blank" class="btn btn-help">Questions?</a>
				</div>';

			echo '</div>
			';

			echo '<div class="col-sm-9 scroll formSection rounded">';
			//Generate Admin interface if the user is an admin.
			if(array_key_exists("accessLevel", $_SESSION) && $_SESSION['accessLevel'] == "Admin"){
				echo '<br>';
				echo '<div class="row">';
				echo '<div class="col-sm-3"></div>';
				echo '<div class="col-sm-6 border rounded border-dark" id="adminProjectStatusDiv">';
					echo "<center><h4><p style='color:black'>-- Admin Project Status Review -- </p></h6></center>";
					if ($status == 'Pending' && $category == ""){
						echo "<h6><p style='color:red'>Action Required: Project Review and Project Category Placement</p></h6>";
					}
					else if ($status == 'Pending'){
						echo "<h6><p style='color:red'>Action Required: Project Review</p></h6>";
					}
					else if ($category == ""){
						echo "<h6><p style='color:red'>Action Required: Project Category Placement</p></h6>";
					}
					else {
						echo "<h6><p style='color:red'>No action required at this time</p></h6>";
					}
					if ($is_hidden == '1')
					{
						echo "<h6><p style='color:red'>Private Project (Not viewable on Browse Project)</p></h6>";
					}
					else {
						echo "<h6><p style='color:black'>Public Project</p></h6>";
					}
					if ($comments != ""){
						echo "<h6><p style='color:red'>Proposer Comments: $comments</p></h6>";
					}
					echo "<h6><p style='color:black'>Current Project Status: $status</p></h6>";
					echo "<h6><p style='color:black'>Major Category: $category</p></h6>";
					echo '
					<select class="form-control" id="projectCategorySelect" data-toggle="tooltip" data-placement="top" title="' . $adminProjectCategorySelectToolTip . '">';

					echo '<option>'. $category . '</option>';
						if ($category != "Computer Science"){
							echo'<option>Computer Science</option>';
						}
						if ($category != "Electrical Computer Engineering"){
							echo'<option>Electrical Computer Engineering</option>';
						}
						if ($category != "Computer Science / Electrical Computer Engineering"){
							echo'<option>Computer Science / Electrical Computer Engineering</option>';
						}
						if ($category != "Other"){
							echo'<option>Other</option>';
						}

					echo '</select>';

					echo '<center>
					  <button class="btn btn-lg btn-primary admin-btn" type="button" id="adminViewProjectBtn">View Project &raquo</button>
						<button class="btn btn-lg btn-success admin-btn" type="button" id="adminApproveProjectBtn">Approve Project</button>
						<button class="btn btn-lg btn-danger admin-btn" type="button" id="adminUnapproveProjectBtn">Reject/Unapprove Project</button>
						<button class="btn btn-lg btn-outline-danger admin-btn" type="button" id="adminMakeProjectPrivateBtn">Make Project Private</button>
						<button class="btn btn-lg btn-outline-info admin-btn" type="button" id="adminMakeProjectNotPrivateBtn">Make Project Public</button>
						<button class="btn btn-lg btn-primary admin-btn" type="button" id="adminReturnBtn">Return &raquo</button>
					</center>
					<div id="approvedText" class="adminText" style="color: green;">Project Approved!</div>
					<div id="rejectedText" class="adminText" style="color: red;">Project Rejected!</div>
					<div id="privateText" class="adminText" style="color: red;">Project Now Private! (Will NOT show up in Browse Projects)</div>
					<div id="publicText" class="adminText" style="color: blue;">Project Now Public! (WILL show up in Browse Projects)</div>
					<div id="categoryText" class="adminText" style="color: green;">Category Changed!</div>
				</div>
				<div class="col-sm-3"></div>

				</div>';
			}

			//------------------------- END ADMIN HANDLING ------------ //

			echo '<br>
				<form>
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label for="projectTypeSelect">Project Category</label>
								<select class="form-control" id="projectTypeSelect" data-toggle="tooltip" data-placement="bottom" title="' . $projectTypeSelectToolTip . '">';
								echo '<option>'. $type . '</option>';
									//Implements logic to only display one of each item and
									//the chosen item for the project as the first item.
									if ($type != "Capstone"){
										echo'<option>Capstone</option>';
									}
									if ($type != "Student Club Project"){
										echo'<option>Student Club Project</option>';
									}
									if ($type != "Individual Project"){
										echo'<option>Individual Project</option>';
									}
					echo'
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="projectFocusSelect">Project Focus</label>
								<select class="form-control" id="projectFocusSelect" data-toggle="tooltip" data-placement="bottom" title="' . $projectFocusSelectToolTip . '">';
								echo '<option>'. $focus . '</option>';
									//Implements logic to only display one of each item and
									//the chosen item for the project as the first item.
									if ($focus != "Research"){
										echo'<option>Research</option>';
									}
									if ($focus != "Design"){
										echo'<option>Design</option>';
									}
									if ($focus != "Development"){
										echo'<option>Development</option>';
									}
						echo'
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group" id="compensationDiv">
								<label for="compensationSelect">Compensation</label>
								<select class="form-control" id="compensationSelect" data-toggle="tooltip" data-placement="bottom" title="' . $compensationSelectToolTip . '">';
								echo '<option>'. $compensation . '</option>';
									//Implements logic to only display one of each item and
									//the chosen item for the project as the first item.
									if ($compensation != "None"){
										echo'<option>None</option>';
									}
									if ($compensation != "Hourly"){
										echo'<option>Hourly</option>';
									}
									if ($compensation != "Stipend"){
										echo'<option>Stipend</option>';
									}
									if ($compensation != "Completion-dependent"){
										echo'<option>Completion-dependent</option>';
									}
									if ($compensation != "Other"){
										echo'<option>Other</option>';
									}
		echo'
								</select>
							</div>
						</div>
					</div>';


			echo '<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label for="projectDescriptionText">Project Description <font size="2" style="color:red;">*required</font></label>

						<textarea class="form-control" id="projectDescriptionText" rows="3" data-toggle="tooltip" data-placement="top" title="' . $projectDescriptionTextToolTip . '">' . $description . '</textarea>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<label for="motivationText">Motivation <font size="2" style="color:red;">*required</font></label>
						<textarea class="form-control" id="motivationText" rows="4" data-toggle="tooltip" data-placement="top" title="' . $motivationTextToolTip . '">' . $motivation . '</textarea>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label for="objectivesText">Objectives/Deliverables <font size="2" style="color:red;">*required</font></label>
						<textarea class="form-control" id="objectivesText" rows="4" data-toggle="tooltip" data-placement="top" title="' . $objectivesTextToolTip . '">' . $objectives . '</textarea>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">';

				echo '<div class="ui-widget">
					  <label for="keywordsInput">Add Keywords to Project: <font size="2" style="color:red;">*required</font><br><font size="2">Press Enter after each keyword.</font></label>
					  <input id="keywordsInput" class="form-control">
					</div>
					<div id="keywordsDiv">';

				//Keywords has a buffer character that will cause an additional blank key to be added
				//for projects without any keywords. FUTURE IMPLEMENTATION: Fix this bug. 2/28/19.
				if(sizeof($keywords) > 1){
					foreach($keywords as $key){
						if($key != ' '){
							echo '<span class="badge badge-light keywordBadge">' . $key . ' <i class="fas fa-times-circle"></i></span>';
						}
					}
				}

				echo '</div>
					';

					echo '</div>
				</div>
			</div>';


			echo '<div class="row">
				<div class="col-sm-6">';
			echo'
					<div class="form-group" id="numberGroupsDesiredDiv">
						<label for="numberGroupsDesiredText">Number of Groups</label>
						<textarea class="form-control" id="numberGroupsDesiredText" rows="1" data-toggle="tooltip" data-placement="top" title="' . $numberGroupsDesiredTextToolTip . '">' . $number_groups . '</textarea>
					</div>';
			//Create NDA disclaimer.
			echo'
					<div class="form-group" id="ndaDiv">
						<div id="ndaDisclaimerDiv" class="ndaDisclaimer border rounded border-secondary">
						<b>If your project requires an NDA and/or IP agreement, it must be indicated at the time the students select the projects.
						</b>
						<br><br>
						If your company intends to provide proprietary materials or confidential information requiring an NDA, OSU can arrange for a written agreement to reviewed and signed amongst the students, your company, and OSU.
						<br><br>
						Such an agreement will authorize the students to use and discuss the provided materials or information with each other and their instructor in confidence.
						<br><br>
						<b>The university will not participate in any agreement that requires students to transfer intellectual property rights ownership to your company or puts overly burdensome confidentiality obligations on the students.
						</b>
						<br><br>Though OSU certainly appreciates your company’s sponsorship, we strongly discourage any agreements that could deter students from sharing the results of their academic work at OSU with fellow students, parents or future employers.
						<br><br>This does not prevent a separate arrangement between you each student individually.
						<br>
						</div>
						<label id="ndaSelectLabel" for="ndaSelect" data-toggle="tooltip" data-placement="right" title="' . $ndaSelectToolTip . '">NDA/IP <font size="2" style="color:red;">*required</font></label>
						<select class="form-control" id="ndaSelect">';
						echo '<option>'. $nda . '</option>';
							if ($nda != "No Agreement Required"){
								echo'<option>No Agreement Required</option>';
							}
							if ($nda != "NDA Required"){
								echo'<option>NDA Required</option>';
							}
							if ($nda != "NDA/IP Required"){
								echo'<option>NDA/IP Required</option>';
							}
						echo '
						</select>
					</div>';

				echo'</div>
				<div class="col-sm-6">
					<div class="row" id="dateDiv">
						<div class="col-sm-7">';

						echo'
							<div class="form-group">
							Start By
								<div class="input-group date" id="startbydate" data-target-input="nearest">
									<input type="text" id="startByText" class="form-control datetimepicker-input" value="' . $startBy .'" data-target="#startbydate" data-toggle="tooltip" data-placement="top" title="' . $startByTextToolTip . '"/>
									<div class="input-group-append" data-target="#startbydate" data-toggle="datetimepicker">
											<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
								</div>
							</div>
						</div>
							<div class="form-group">
							Complete By
								<div class="input-group date" id="endbydate" data-target-input="nearest">
									<input type="text" id="completeByText" class="form-control datetimepicker-input" value="' . $completeBy . '" data-target="#endbydate" data-toggle="tooltip" data-placement="top" title="' . $completeByTextToolTip . '"/>
									<div class="input-group-append" data-target="#endbydate" data-toggle="datetimepicker">
											<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
									</div>
								</div>
							</div>
						</div>';
						echo'
						<div class="col-sm-5">
							<br>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="commentsText">Special Comments</label>
								<textarea class="form-control" id="commentsText" rows="3" data-toggle="tooltip" data-placement="top" title=' . $commentsTextToolTip . '>' . $comments . '</textarea>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="websiteText">Website</label>
								<textarea class="form-control" id="websiteText" rows="1" data-toggle="tooltip" data-placement="top" title="' . $websiteTextToolTip . '">' . $website . '</textarea>
							</div>
						</div>
						<div class="col-sm-12"> 
							<div class="form-group">
								<label for="videoText">Video</label>
								<textarea class="form-control" id="videoText" rows="1" data-toggle="tooltip" data-placement="top" title="' . $videoTextToolTip . '">' . $video . '</textarea>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="additionalEmailsText">Additional Emails</label>
								<textarea class="form-control" id="additionalEmailsText" rows="1" data-toggle="tooltip" data-placement="top" title="' . $additionalEmailsTextToolTip . '">' . $additionalEmails . '</textarea>
							</div>
						</div>
					</div>

				</div>';

			echo '</div>
			</form>
			';
						echo'
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label for="minQualificationsText">Minimum Qualifications</label>
						<textarea class="form-control" id="minQualificationsText" rows="3" data-toggle="tooltip" data-placement="top" title="' . $minQualificationsTextToolTip . '">' . $minQualifications . '</textarea>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="prefQualificationsText">Preferred Qualifications</label>
						<textarea class="form-control" id="prefQualificationsText" rows="3" data-toggle="tooltip" data-placement="top" title=' . $prefQualificationsTextToolTip . '>' . $prefQualifications . '</textarea>
					</div>
				</div>

			</div>';

			echo'
			</div>
			</div>
			</div>
			';

		}


	?>
<body>
	<?php include_once('../modules/navbar.php'); ?>

	<?php
	if($validUserCredentials){
		buildEditProject($projectID);
	}
	else{
		header("Location: ./index.php");
		exit();
	}
	?>

	<?php include_once("../modules/footer.php"); ?>


 </body>

<script type="text/javascript">

//datetimepicker is a function from the TempusDominus library and is the GUI
//that allows users to select the date time of the StartBy/EndBy inputs.
//Link to documentation: https://tempusdominus.github.io/bootstrap-4/
$(function () {
                $('#startbydate').datetimepicker({
                    format: 'L'
                });
});
$(function () {
                $('#endbydate').datetimepicker({
                    format: 'L'
                });
});


$('#projectCategorySelect').change(function(){
	projectCategorySelect = $('#projectCategorySelect').val();
	projectID = "<?php echo $projectID; ?>";
	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				projectCategorySelect: projectCategorySelect,
				projectID: projectID,
				action: 'adminChooseProjectCategory'},
		success: function(result){
			createCategoryText();
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status);
			alert(xhr.responseText);
			alert(thrownError);
		}
	});
});


$(document).ready(function(){
  //Instantiates all tool tips.
  $('[data-toggle="tooltip"]').tooltip();

  updateEditProjectLayout();

  $('#keywordsInput').on('change', function() {
	  key = $('#keywordsInput').val();
	  //Add user-generated keyword into the keywordsDiv.
	  $('#keywordsDiv').append('<span class="badge badge-light keywordBadge">' + key + ' <i class="fas fa-times-circle"></i></span>');
	  $('#keywordsInput').val("");
  });

  //Remove keywords when clicked.
  $('body').on('click', '.keywordBadge', function(e) {
		this.remove();
  });

});

//Update the layout of the page if the project type is selected. This is
//because certain text boxes appear for certain types and not others.
$('#projectTypeSelect').change(function(){
	updateEditProjectLayout();
});

function updateEditProjectLayout(){
	if($('#projectTypeSelect').val() == "Capstone"){
		$('#dateDiv').hide();
		$('#ndaDiv').show();
		$('#numberGroupsDesiredDiv').show();
		$('#compensationDiv').hide();
	}
	else{
		$('#dateDiv').show();
		$('#ndaDiv').hide();
		$('#numberGroupsDesiredDiv').hide();
		$('#compensationDiv').show();
	}
}

var uploadedImage = 0;

//Uploads image to ./images/ folder.
function Upload() {
	id = <?php echo $projectID?>;
	var file_data = $('#imgInp').prop('files')[0]
	var form_data = new FormData();

	//Append form data to be manipulated in the script the ajax will run.
	form_data.append('file', file_data);
	form_data.append('action','upload');

	if(uploadedImage == 1){
		form_data.append('id', 'project_' + id + "_");
	}
	$.ajax({
		url: '../db/upload.php',
		type: 'POST',
		contentType: false,
		processData: false,
		data: form_data,
		success: function(result)
		{
		},
		error: function(result)
		{
			alert("issues!");
		}
	});
}

//Generates the save icon animation.
function createSaveIcon(){
	loaderDivText = '<div class="loaderdiv"><span class="save-icon"><span class="loader"></span><span class="loader"></span><span class="loader"></span></span></div>';
	$('#cssloader').html(loaderDivText);
}

//This Project encapsulates all page data to be parsed within ./db/dbManager.php
class Project {
  constructor() {
    this.title = $('#projectTitleInput').val();
	this.proposerName = $('#proposerNameHeader').val();
    this.id = <?php echo $projectID?>;
	if(uploadedImage == 1){
		this.image = "project_" + this.id + "_" + $('#nameOfImageInput').val();
	}
	else{
		this.image = $('#nameOfImageInput').val();
	}
	this.type = $('#projectTypeSelect').val();
	this.focus = $('#projectFocusSelect').val();
	this.compensation = $('#compensationSelect').val();
	this.description = $('#projectDescriptionText').val();
	this.motivation = $('#motivationText').val();
	this.objectives = $('#objectivesText').val();
	this.minQualifications = $('#minQualificationsText').val();
	this.prefQualifications = $('#prefQualificationsText').val();
	this.nda = $('#ndaSelect').val();
	this.website = $('#websiteText').val();
	this.video = $('#videoText').val();
	this.additional_emails = $('#additionalEmailsText').val();
	this.startBy = $('#startByText').val();
	this.completeBy = $('#completeByText').val();
	this.number_groups = $('#numberGroupsDesiredText').val();
	this.comments = $('#commentsText').val();
	this.keywords = $('#keywordsDiv').html().replace(",", "");
	//Parse keywordsDiv and create a comma separated keywords value to insert into DB.
	this.keywords = this.keywords
					.replace(/<span class="badge badge-light keywordBadge">/g, "")
					.replace(/ <i class="fas fa-times-circle"><\/i><\/span>/g, ", ");
  }
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

function createSaveText(){
	document.getElementById('successText').style.display = "block";
	setTimeout(fade_out, 2000);
}

function fade_out(){
	$("#successText").fadeOut();
}


$('#saveProjectDraftBtn').on('click', function (e) {
	if(displayErrorText('#projectTitleInput', "Please provide a project title.")){
		return;
	}

	P = new Project();
	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				P : P,
				action: 'saveProjectDraft'},
				success: function(result)
				{
					Upload();
					createSaveIcon();
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(xhr.responseText);
					alert(thrownError);
				}
	});
});



$('#submitForApprovalBtn').on('click', function (e) {
	if($('#projectTypeSelect').val() == "Capstone"){
		if(displayErrorText('#ndaSelect', "Please select an NDA/IP option below.")){
			return;
		}
	}
	if(displayErrorText('#projectTitleInput', "Please provide a project title.")){
		return;
	}
	if(displayErrorText('#projectDescriptionText', "Please provide a description.")){
		return;
	}
	if(displayErrorText('#motivationText', "Please provide input for project motivation.")){
		return;
	}
	if(displayErrorText('#objectivesText', "Please provide input for objectives/deliverables.")){
		return;
	}

	P = new Project();
	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				P : P,
				action: 'submitForApproval'},
				success: function(result)
				{
					createSaveText();
					Upload();
					createSaveIcon();
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(xhr.responseText);
					alert(thrownError);
				}
	});
	
});


$(document).ready( function() {
	    $(document).on('change', '.image-picker', function() {
			
		//defaultImageSelect
		var selectedImageName = $('#defaultImageSelect').val().substring(1).replace(/\\/g, '/').replace(/.*\//, '');
		
		$('#nameOfImageInput').val(selectedImageName);
		
		$('#img-upload').attr('src', '../images/' + selectedImageName);
		});		
});
/***************************************************************************************
*    Title: Upload Image w Preview & Filename
*    Author: suketran
*    Code version: 1.0
*    Availability: https://bootsnipp.com/snippets/eNbOa
***************************************************************************************/

$(document).ready( function() {
	    $(document).on('change', '.image-picker', function() {
			
		//defaultImageSelect
		
		var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

		input.trigger('fileselect', [label]);
		});
		
		
    	$(document).on('change', '.btn-file :file', function() {
		var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

		input.trigger('fileselect', [label]);
		});

		$('.btn-file :file').on('fileselect', function(event, label) {

		    var input = $(this).parents('.input-group').find(':text'),
		        log = label;

			//3/3/19: Included this logic to ensure only valid files
			//are capable of being submitted.
			if(!log.includes(".jpeg") && !log.includes(".jpg") &&
			!log.includes(".png") && !log.includes(".bmp") &&
			!log.includes(".JPG") && !log.includes(".JPEG") &&
			!log.includes(".PNG") && !log.includes(".BMP") &&
			!log.includes(".gif") && !log.includes(".GIF")){
				return;
		    }

		    if( input.length ) {
		        input.val(log);
		    } else {
		        if( log ) alert(log);
		    }

		});

		function readURL(input) {
		    if (input.files && input.files[0]) {
		        var reader = new FileReader();

		        reader.onload = function (e) {
					uploadedImage = 1;
		            $('#img-upload').attr('src', e.target.result);
		        }

		        reader.readAsDataURL(input.files[0]);
		    }
		}

		$("#imgInp").change(function(){
		    readURL(this);
		});
	});


/***************************************************************************************
*    End of code from: Upload Image w Preview & Filename
*    Author: suketran
*    Code version: 1.0
*    Availability: https://bootsnipp.com/snippets/eNbOa
*
***************************************************************************************/

</script>


<script type="text/javascript">

/* ADMIN FUNCTIONS FOR MAKING TEXT SHOW UP WHEN BUTTONS ARE CLICKED */
function createApprovedText(){
	document.getElementById('approvedText').style.display = "block";
}

function createRejectedText(){
	document.getElementById('rejectedText').style.display = "block";
}

function createPrivateText(){
	document.getElementById('privateText').style.display = "block";
}

function createPublicText(){
	document.getElementById('publicText').style.display = "block";
}

function createCategoryText(){
	document.getElementById('categoryText').style.display = "block";
}


/* END ADMIN FUNCTIONS FOR MAKING TEXT SHOW UP WHEN BUTTONS ARE CLICKED*/

function pop(){
	var reason = prompt("Reason for rejection", "Text");
	if (reason == null){
		return; // Break out of function early
	}
	return (reason);
}

$( function() {
	var availableTags = [
	<?php
	$result = getKeywords();
	while($row = $result->fetch_assoc()){
		echo '"' . $row['name'] . '",';
	}
	?>
  ];
  $( "#keywordsInput" ).autocomplete({
    source: availableTags
  });
});

$('#adminViewProjectBtn').on('click', function(){
	projectID = "<?php echo $projectID; ?>";

	url = "./viewSingleProject.php?id=" + projectID;
	window.location.replace(url);
});

$('#adminReturnBtn').on('click', function(){
	url = "./adminProject.php";
	window.location.replace(url);
});

$('#adminApproveProjectBtn').on('click', function(){
	projectID = "<?php echo $projectID; ?>";
	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				projectID: projectID,
				action: 'adminApproveProject'},
		success: function(result){
			createApprovedText();

		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status);
			alert(xhr.responseText);
			alert(thrownError);
		}
	});
});

$('#adminUnapproveProjectBtn').on('click', function(){
	projectID = "<?php echo $projectID; ?>";
	reason = pop();
	if (reason == null)
		return; // return if reason is canceled
	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				reason: reason,
				projectID: projectID,
				action: 'adminUnapproveProject'},
		success: function(result){
			createRejectedText();

		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status);
			alert(xhr.responseText);
			alert(thrownError);
		}
	});
});

$('#adminMakeProjectNotPrivateBtn').on('click', function(){
	projectID = "<?php echo $projectID; ?>";
	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				projectID: projectID,
				action: 'adminMakeProjectNotPrivate'},
		success: function(result){
			createPublicText();
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status);
			alert(xhr.responseText);
			alert(thrownError);
		}
	});
});

$('#adminMakeProjectPrivateBtn').on('click', function(){
	projectID = "<?php echo $projectID; ?>";
	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				projectID: projectID,
				action: 'adminMakeProjectPrivate'},
		success: function(result){
			createPrivateText();
			//result will return the id of the newly created project.
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

