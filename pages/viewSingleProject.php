<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once('../includes/header.php'); ?>
	<title>Single Project</title>
</head>

<?php require_once('../db/dbManager.php'); ?>
<?php require_once('../modules/createCards.php'); ?>
<?php require_once('../modules/navbar.php'); ?> 


<?php
	$projectID;
	if (isset($_GET['id'])) {
		$projectID = $_GET['id'];
		$result = getSingleProject($projectID);
		$row = $result->fetch_assoc();
	}else {
		//Handle case where no id is passed in parameter.
		header("Location: ../");
		exit();
	}

	//Pull data from the project with the id passed as URL argument.
	$title = $row['title'];
	$type = $row['type'];
	$status = $row['status'];
	$type = $row['type'];
	$year = $row['year'];
	$website = $row['website'];
	$video = $row['video'];
	$start_by = $row['start_by'];
	$complete_by = $row['complete_by'];
	$pref_qualifications = $row['preferred_qualifications'];
	$min_qualifications = $row['minimum_qualifications'];
	$motivation = $row['motivation'];
	$description = $row['description'];
	$objectives = $row['objectives'];
	$nda = $row['NDA/IP'];
	$compensation = $row['compensation'];
	$image = $row['image'];
	$keywords = explode(",", $row['keywords']);

	$status = $row['status'];
	$is_hidden = $row['is_hidden'];
	$category = $row['category'];
	$comments = $row['comments'];
	
	$adminProjectCategorySelectToolTip = "Changes will automatically be saved.";
	
	$result = getNameFromProjectId($projectID);
	$row = $result->fetch_assoc();
	$name = $row["CONCAT(first_name, ' ', last_name)"];
?>
<body class="viewSingleProject">

	  <!-- Header -->
	  <header class="bg-primary py-5 mb-5">
	    <div class="container h-100">
	      <div class="row h-100 align-items-center">
	        <div class="col-lg-12">
	          <h1 class="display-4 text-white mt-5 mb-2"><?php echo($title);?></h1>
	          <p class="lead mb-5"><?php echo($description);?></p>
	        </div>
	      </div>
	    </div>
	  </header>

	  <!-- Page Content -->
	  <div class="container">

	    <div class="row">
	      <div class="col-md-8 mb-5">
	        <h2>Objectives</h2>
	        <hr>
	        <p><?php echo($objectives);?></p>
					<h2>Motivations</h2>
				 	<hr>
				 	<p><?php echo($motivation);?></p>
					<h2>Qualifications</h2>
				 	<hr>
					<strong>Minimum Qualifications:</strong>
 				 	<br><?php echo($min_qualifications);?>
 				 	<p></p>
					<strong>Preferred Qualifications:</strong>
					<br><?php echo($pref_qualifications);?>
					<p></p>
					<br>

					<?php
						if(array_key_exists("userID",$_SESSION) && $_SESSION['userID'] != ''){
							//Future Implementation @3/19/19 Release
							//We will be implementing student application functionality for the next release.
							echo('<button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button" data-toggle="modal" data-target="#newApplicationModal" id="openNewApplicationModalBtn">Apply For This Project &raquo</button>');
						}
						//Generate admin interface for admins.
						if(array_key_exists("userID",$_SESSION) && $_SESSION['accessLevel'] == 'Admin'){
								echo "<br><br><h4><p style='color:black'>-- Admin Project Status Review -- </p></h6>";
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

echo'
								</select>';

							echo('<button class="btn btn-lg btn-primary admin-btn" type="button" id="adminEditProjectBtn">Edit Project &raquo</button>');
							echo('<button class="btn btn-lg btn-success admin-btn" type="button" id="adminApproveProjectBtn">Approve Project</button>');
							echo('<button class="btn btn-lg btn-danger admin-btn" type="button" id="adminUnapproveProjectBtn">Reject/Unapprove Project</button>');
							echo('<button class="btn btn-lg btn-outline-danger admin-btn" type="button" id="adminMakeProjectPrivateBtn">Make Project Private</button>');
							echo('<button class="btn btn-lg btn-outline-info admin-btn" type="button" id="adminMakeProjectNotPrivateBtn">Make Project Public</button>');
							echo('<button class="btn btn-lg btn-primary admin-btn" type="button" id="adminReturnBtn">Return &raquo</button>');

					}

					?>
					<div id="approvedText" class="adminText" style="color: green;">Project Approved!</div>
					<div id="rejectedText" class="adminText" style="color: red;">Project Rejected!</div>
					<div id="privateText" class="adminText" style="color: red;">Project Now Private! (Will NOT show up in Browse Projects)</div>
					<div id="publicText" class="adminText" style="color: blue;">Project Now Public! (WILL show up in Browse Projects)</div>
					<div id="categoryText" class="adminText" style="color: green;">Category Changed!</div>


	      </div>

	      <div class="col-md-4 mb-5">
	        <h2>Details</h2>
	        <hr>
					<address>
					<strong>Author:</strong>
					<p><?php echo($name);?></p>
			</address>
			<address>
					<strong>NDA/IPA:</strong>
					<p><?php echo($nda);?></p>
			</address>
	        <address>
	          <strong>Start Date:</strong>
	          <br><?php echo($start_by);?>
	          <br>
	        </address>
	        <address>
						<strong>End Date:</strong>
	          <br><?php echo($complete_by);?>
	          <br>
	        </address>
					<address>
						<strong>Website:</strong>
	          <br><a href="<?php echo($website);?>" target="_blank"><?php echo($website);?></a>
	          <br>
						<strong>Video:</strong>
	          <br><a href="<?php echo($video);?>" target="_blank"><?php echo($video);?></a>
	          <br>
	        </address>
					<address>
						<strong>Compensation:</strong>
	          <br><?php echo($compensation);?>
	          <br>
	        </address>
					<address>
						<strong>Keywords:</strong>
						<br>		<?php
						$string=implode(",", $keywords);
						$string = trim($string,",");
						$string = rtrim($string,", ");
						echo($string);
								?>
						<br>
					</address>
					<address>
						<strong>Project Status:</strong>
	          <br><?php echo($status);?>
	          <br>
	        </address>
	      </div>
	    </div>
			<br>
			<h2>Related Projects</h2>

			<!-- related_cards is a class used in the javascript below to interface
			     this section with an open source library called slick, which allows
				 for a slideshow-like display.
			-->
			<div class="related_cards">
				<?php
				$numberOfRelatedProjects = 0;
				//Create Related Project section.
				foreach ($keywords as $key){
					$result = getRelatedProjects($key, $projectID);
					$rowcount = mysqli_num_rows($result);
					while ($row = $result->fetch_assoc()){

						$id = $row['project_id'];
						$title = $row['title'];

						//Limit length of title to XX characters for the cards.
						$title = strlen($title) > 24 ? substr($title,0,24)."..." : $title;

						$description = ($row['description'] != NULL ? $row['description'] : '');
						//Limit length of description to XX characters for the cards.
						$description = strlen($description) > 70 ? substr($description,0,70)."..." : $description;


						$status = $row['status'];
						$nda = $row['NDA/IP'];
						if($nda == "NDA Required" || $nda == "NDA/IP Required"){
							$nda = "NDA/IP Required";
						}
						else{
							$nda = "";
						}

						$extra = ($row['year'] != NULL ? $row['type'] . " " . $row['year'] : '');
						$extra .= '<br> Status: ' . $row['status'];
						$extra .= ' ' . '<h6>' . $nda . '</h6>';
						$image = $row['image'] != NULL ? $row['image'] : "capstone.jpg";

						$relatedProjectKeywords = explode(",", $row['keywords']);

						foreach($relatedProjectKeywords as $relatedProjectKey){
							if($relatedProjectKey != ' ' && strlen($extra) < 400){
								$extra .= '<span class="badge badge-light keywordBadge">' . $relatedProjectKey . '</span>';
							}
						}

						//Generate the Project Cards in ./modules/createCards.php.
						createRelatedProjectCard($id, $title, $description, $extra, $image);
						$numberOfRelatedProjects++;
					}
					//Set the maximum number of related projects to be displayed in this section.
					if ($numberOfRelatedProjects == 12){
						break;
					}
				}
				?>
			</div>


			</div>

<div id="projectIDDiv" style="display:none;"><?php echo $projectID; ?></div>
<div id="userIDDiv" style="display:none;"><?php echo $_SESSION['userID']; ?></div>

<!-- Create Application Functionality -->
<?php include "../modules/newApplicationModal.php"; ?>
<!-------------------------------------->

<?php include_once("../modules/navbar.php"); ?>
<?php include_once("../modules/footer.php"); ?>

</body>

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


$(document).ready(function(){
  //Start tooltip functionality.
  $('[data-toggle="tooltip"]').tooltip();
});


function pop(){
	var reason = prompt("Reason for rejection", "Text");
	if (reason == null){
		return; // Break out of function early if cancel button is pressed
	}
	return (reason);

}

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

//Future Implementation @3/19/19 Release
//We will be implementing student application functionality for the next release.

$('#createApplicationBtn').on('click', function(){
	projectID = "<?php echo $projectID; ?>";
	//Bug Fix 4/1/19: An invalid userID was being returned when attempting 
	//to echo out the SESSION variable for the userID within the Javascript 
	//code here. The fix I found was to create a hidden div on the page itself 
	//and echo out the SESSION variable there and reference it here.
	
	//This is because Google Authentication provides user IDs that are larger 
	//than the 64 bit character columns for user IDs in the database and thus 
	//truncate a part of the the ID. 
	userID = $('#userIDDiv').text();
	
	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				userID: userID,
				projectID: projectID,
				action: 'createApplication'},
		success: function(result){
			//result will return the id of the newly created project.
			url = "./editApplication.php?id=" + result;
			window.location.replace(url);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status);
			alert(xhr.responseText);
			alert(thrownError);
		}
	});
	
});


$('#adminEditProjectBtn').on('click', function(){
	projectID = "<?php echo $projectID; ?>";

	url = "./editProject.php?id=" + projectID;
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
			createPrivateText();

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
			createPublicText();
			//result will return the id of the newly created project.
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status);
			alert(xhr.responseText);
			alert(thrownError);
		}
	});
});

/***************************************************************************************
* Related Projects
***************************************************************************************/
$(document).ready(function(){
	$('.related_cards').slick({
		dots: true,
		centerPadding: '15px',
		infinite: true,
		slidesToShow: 3,
		slidesToScroll: 3
	});
});


</script>

</html>
