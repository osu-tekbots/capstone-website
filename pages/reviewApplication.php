<?php
use DataAccess\CapstoneApplicationsDao;
use DataAccess\CapstoneApplicationReviewsDao;
use Model\CapstoneApplicationReviewInterestLevel;

if(!session_id()) {
	session_start();
}

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$aid = $_GET['id'];
$isLoggedIn = isset($_SESSION['userID']) && $_SESSION['userID'] . '' != '';
$isProposer = isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Proposer';
$isAdmin = isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

allowIf($aid != '' && $isLoggedIn && ($isProposer || $isAdmin));

$dao = new CapstoneApplicationsDao($dbConn, $logger);
$application = $dao->getApplication($aid);

$title = 'Review Application';
include_once PUBLIC_FILES . '/modules/header.php';

$justification = $application->getJustification();
$time_available = $application->getTimeAvailable();
$skill_set = $application->getSkillSet();
$external_link = $application->getPortfolioLink();
$user_id = $application->getStudent()->getId();
$project_id = $application->getCapstoneProject()->getId();
$title = $application->getCapstoneProject()->getTitle();
$description = $application->getCapstoneProject()->getDescription();
$motivation = $application->getCapstoneProject()->getMotivation();
$objectives = $application->getCapstoneProject()->getObjectives();
$minQualifications = $application->getCapstoneProject()->getMinQualifications();
$prefQualifications = $application->getCapstoneProject()->getPreferredQualifications();
$firstName = $application->getStudent()->getFirstName();
$lastName = $application->getStudent()->getLastName();
$reviewInterest = $application->getInterestLevel()->getId();
$comments = $application->getProposerComments();
switch($reviewInterest){
	case CapstoneApplicationReviewInterestLevel::DESIREABLE:
		$desirableBtnClass = "btn-success";
		$impartialBtnClass = "btn-outline-secondary";
		$undesirableBtnClass = "btn-outline-warning";
		break;
	case CapstoneApplicationReviewInterestLevel::IMPARTIAL:
		$desirableBtnClass = "btn-outline-success";
		$impartialBtnClass = "btn-secondary";
		$undesirableBtnClass = "btn-outline-warning";
		break;
	case CapstoneApplicationReviewInterestLevel::UNDESIREABLE:
		$desirableBtnClass = "btn-outline-success";
		$impartialBtnClass = "btn-outline-secondary";
		$undesirableBtnClass = "btn-warning";
		break;
	default: 
		$desirableBtnClass = "btn-outline-success";
		$impartialBtnClass = "btn-outline-secondary";
		$undesirableBtnClass = "btn-outline-warning";
		break;
}

if($isAdmin) {
	$otherProjects = $dao->getAllApplicationsForUser($user_id);
}

?>

<div class="container-fluid">
<br>
<div class="row">
	<div class="col-sm-1">	
		<br><br><br><br><br>
		<br><br><br><br><br>
		<br><br><br><br><br>
		<br><br><br><br>
		<a href="pages/myApplications.php"><button class="btn btn-lg btn-outline-primary">Back</button></a>
	</div>
	<div class="col-sm-6">
		<div class="scrollShorter jumbotron capstoneJumbotron">
			<!-- Display application data. -->
			<?php 
				echo '<h3>Student Application for ' . $title . '</h3>';
				echo '<h4>By ' . $firstName . ' ' . $lastName . '</h4>';
				echo '<br>';
				echo '<h4>Justification:</h4>' . $justification . '';
				echo '<br><br>';
				echo '<h4>Skill Set:</h4> ' . $skill_set . '';
				echo '<br><br>';
				echo '<h4>Time Available:</h4> ' . $time_available . '';
				echo '<br><br>';
				echo '<h4>External Link:</h4> <a href="' . $external_link . '" target="_blank">' . $external_link . '</a>';
			?>
	
		</div>
		<center>
			<?php
			if($isAdmin){

				if ($comments != ""){
					echo('<h6 style="float:left;">Proposer Comments About Applicant: '.$comments.'</h6>');
				}
				if (isset($otherProjects)){

					$projects = '';
					foreach($otherProjects as $op) {
						$opId = $op->getId();
						if($opId != $application->getId()) {
							$opTitle = $op->getCapstoneProject()->getTitle();
							$projects .= "
							<li>
								<a href='pages/reviewApplication.php?id=$opId'>$opTitle</a>
							</li>
							";
						}
					}

					echo "
					<div style='float: left;'>
						<h6>Other Projects Applicant has Applied For Additional Projects</h6>
						<ul>$projects</ul>
					</div>
					";
				}

				echo('
				<br>
				<br>
				<br>

				<!-- Each buttons class is dynamically generated so that the selected one will be filled whereas the other two will have "outline" property. -->
				<h6 style="float:center;">Assign Desirability To Applicant:</h6>
				<button class="btn btn-lg '.$desirableBtnClass.' id="desirableBtn">Desirable</button>
				<button class="btn btn-lg '.$impartialBtnClass.' id="impartialBtn">Impartial</button>
				<button class="btn btn-lg '.$undesirableBtnClass.' id="undesirableBtn">Undesirable</button>
				');
			}
			else {
				echo('
				<div id="successText" class="successText" style="display:none;">Successfully rated application!</div>
				<form>
					<div class="form-group">
						<h6 style="float:left;">Put Comments About Applicant Here (Not Required*):</h6>
						<textarea class="form-control" id="commentsText" rows="2"> '.$comments.' </textarea>
					</div>
				</form>
				<!-- Each buttons class is dynamically generated so that the selected one will be filled whereas the other two will have "outline" property. -->
				<h6 style="float:center;">Assign Desirability To Applicant:</h6>
				<h6 style="float:left;">*Assigning desirability in no way indicates that you will have an applicant chosen/not chosen.  All applicants will in the end be chosen by the admins*</h6>
				<button class="btn btn-lg '.$desirableBtnClass.' id="desirableBtn">Desirable</button>
				<button class="btn btn-lg '.$impartialBtnClass.' id="impartialBtn">Impartial</button>
				<button class="btn btn-lg '.$undesirableBtnClass.' id="undesirableBtn">Undesirable</button>
				');
			}?>
		</center>
	</div>
	<div class="col-sm-1">
	</div>
	<div class="col-sm-4">
		<div class="scroll jumbotron capstoneJumbotron">
			<!-- Display project data. -->
			<?php
			echo '
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
				';
			?>
		</div>
	</div>
</div>
</div>

<script type="text/javascript">
function createSaveText(){
	document.getElementById('successText').style.display = "block";
	setTimeout(fade_out, 2000);
}

function fade_out(){
	$("#successText").fadeOut();
}



function createApplicationReview(actionType){
	applicationID = <?php echo $row['application_id']; ?>;
	comments = $('#commentsText').val();

	$.ajax({
		type: 'POST',
		url: '../db/dbManager.php',
		dataType: 'html',
		data: {
				applicationID: applicationID,
				comments: comments,
				action: actionType},
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
}

$('#desirableBtn').on('click', function(e){
	createApplicationReview('desirableApplication');
});

$('#impartialBtn').on('click', function(e){
	createApplicationReview('impartialApplication');
});

$('#undesirableBtn').on('click', function(e){
	createApplicationReview('undesirableApplication');
});

</script>

<?php include_once PUBLIC_FILES . '/modules/footer.php' ?>