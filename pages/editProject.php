<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use DataAccess\UsersDao;
use DataAccess\KeywordsDao;
use DataAccess\CategoriesDao;
use DataAccess\PreferredCoursesDao;
use Model\CapstoneProjectStatus;
use Util\Security;

if (!session_id()) {
    session_start();
}

// Make sure the user is logged in and allowed to be on this page
include PUBLIC_FILES . '/lib/shared/authorize.php';

$isAdmin = $_SESSION['accessLevel'] == 'Admin';
$isProposer = $_SESSION['accessLevel'] == 'Proposer';

$pId = $_GET['id'];

$authorizedToProceed = $pId . '' != '' && $_SESSION['userID'] . '' != '';

allowIf($authorizedToProceed, '../pages/login.php');

$dao = new CapstoneProjectsDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);
$categoriesDao = new CategoriesDao($dbConn, $logger);
$preferredCoursesDao = new PreferredCoursesDao($dbConn, $logger);
$usersDao = new UsersDao($dbConn, $logger);

// Get the project and store properly formatted values into local variables
$project = $dao->getCapstoneProject($pId);
if ($project) {
    $pTitle = Security::HtmlEntitiesEncode($project->getTitle());
    $pMotivation = Security::HtmlEntitiesEncode($project->getMotivation());
    $pDescription = Security::HtmlEntitiesEncode($project->getDescription());
    $pObjectives = Security::HtmlEntitiesEncode($project->getObjectives());
    $pDateStart = $project->getDateStart() != null ? $project->getDateStart()->format('m/d/Y') : '';
    $pDateEnd = $project->getDateEnd() != null ? $project->getDateEnd()->format('m/d/Y') : '';
    $pMinQual = Security::HtmlEntitiesEncode($project->getMinQualifications());
    $pPreferredQual = Security::HtmlEntitiesEncode($project->getPreferredQualifications());
    $pCompensationId = $project->getCompensation()->getId();
    $pAdditionalEmails = Security::HtmlEntitiesEncode($project->getAdditionalEmails());
    $pTypeId = $project->getType()->getId();
    $pFocusId = $project->getFocus()->getId();
    $pCopId = $project->getCop()->getId();
    $pNdaIpId = $project->getNdaIp()->getId();
    $pWebsiteLink = Security::HtmlEntitiesEncode($project->getWebsiteLink());
    $pImages = $project->getImages();
    $pVideoLink = Security::HtmlEntitiesEncode($project->getVideoLink());
    $pIsHidden = $project->getIsHidden();
    $pIsSponsored = $project->getIsSponsored();
    $pComments = Security::HtmlEntitiesEncode($project->getProposerComments());
    $pStatusId = $project->getStatus()->getId();
	$pStatusName = $project->getStatus()->getName();
	$pNumberGroups = $project->getNumberGroups();
}



// If the user is not the creator of the project or an admin, redirect them to the home page (unauthorized)
//Workaround here
$authorizedToProceed = $isAdmin;
/*
if (!$authorizedToProceed) {
	$authorizedEditors = $dao->getCapstoneProjectEditors($project->getId());
	if ($authorizedEditors) {
		foreach ($authorizedEditors as $editor) {
			if ($editor->getId() == $_SESSION['userID']) {
				$authorizedToProceed = True;
			}
		}
	}
}
*/
if (!$authorizedToProceed) {
	$authorizedToProceed = $project->getProposer()->getId() == $_SESSION['userID'];
}
//allowIf($authorizedToProceed);

// Get all the various enumerations from the database
$users = $usersDao->getActiveUsers();
$types = $dao->getCapstoneProjectTypes();
$focuses = $dao->getCapstoneProjectFocuses();
$compensations = $dao->getCapstoneProjectCompensations();
$ndaips = $dao->getCapstoneProjectNdaIps();
//$preferredCourses = $preferredCoursesDao->getAllPreferredCourses();

// Status

if ($pStatusId == 2){
	$submitted = TRUE;
	$approved = FALSE;
} else if ($pStatusId == 3){
	$approved = FALSE;
	$submitted = FALSE;
} else if ($pStatusId >= 4){
	$submitted = FALSE;
	$approved = TRUE;
}
else {
	$submitted = $pStatusId >= CapstoneProjectStatus::PENDING_APPROVAL && $pStatusId != CapstoneProjectStatus::REJECTED;
	$approved = $pStatusId >= CapstoneProjectStatus::ACCEPTING_APPLICANTS && $pStatusId != CapstoneProjectStatus::REJECTED;
}


allowIf($authorizedToProceed, 'pages/index.php');

include_once PUBLIC_FILES . '/modules/admin-review.php';

$title = 'Edit Project';
$js = array(
    array(
        'defer' => 'true',
        'src' => 'assets/js/edit-project.js'
    ),
    array(
        'defer' => 'true',
        'src' => 'assets/js/admin-review.js'
    )
);
include_once PUBLIC_FILES . '/modules/header.php';

// Set Tooltip Texts
$tooltipProjectTitleInput = 'What is the title of this proposal? Please choose a short title.';
$tooltipSaveProjectDraftBtn = 'Allows you to save your progress on the project draft';
$tooltipUpdateProjectDraftBtn = 'Allows you to update your entries on the project';
$tooltipSubmitForApprovalBtn = 'Submit your project for approval. Once your project has been approved, you will receive a confirmation email indicating that it is available for public viewing. ';
$tooltipImgBtn = 'Upload an image to be accompanied with your project when browsing.  You will need to refresh the page to see the image after uploading.';
$tooltipProjectTypeSelect = 'Capstone is the default category option and should be used for all projects related to Senior Design Capstone.';
$tooltipProjectFocusSelect = 'For the options listed, which best describes the majority of this project?';
$tooltipCompensationSelect = 'Is there compensation offered? For capstone projects, compensation is normally none.';
$tooltipProjectDescriptionText = 'Enter your project description here. It is important to create a compelling story to ensure students want to bid on your project.';
$tooltipMotivationText = 'Use this field to describe why you are offering this project. Does this project impact people? Is it helpful? What real life problems does this project solve?';
$tooltipObjectivesText = 'List the items that you expect this project to achieve. This can include tangible deliverables as well as steps to be completed before the final completion. ';
$tooltipKeywords = "Type keywords and hit 'Enter' between keywords.";
$tooltipPreferredCourses = "Select courses that you think relate to this project or would help a student succeed with this project";
$tooltipMinQualificationsText = 'Please enter a list of skills or prior experience needed to complete this project.';
$tooltipPrefQualificationsText = 'Please enter a list of skills or prior experience helpful to complete this project';
$tooltipNdaSelect = "Select this project's NDA settings.";
$tooltipWebsiteText = 'Enter a URL to link to relevant information.';
$tooltipVideoText = 'Enter a URL to link to relevant information.';
$tooltipAdditionalEmailsText = 'Enter additional emails (separated by a semi-colon) from your organization that should also receive updates on this project.';
$tooltipStartByText = 'If this project is not a capstone project, when does it need to start by?';
$tooltipCompleteByText = 'If this project is not a capstone project, when does it need to be completed by?';
$tooltipNumberGroupsDesiredText = 'How many student teams would you like to work on your project? ';
$tooltipCommentsText = "Enter any comments you would like only the admins to see. This is a good place to request specific students by name, request a call back, ask general questions, and tell us if you don't want this project publicly displayed.If you have not included your phone number on your profile, please do so here as we will likely call you.";
$tooltipSponsored = "Thank you for your interest in supporting our students. Sponsorship for Capstone projects is vital for our program. Our sponsorship levels vary depending on the size of your company. Please contact Tina Batten <tina.batten@oregonstate.edu> for details.";


/**
 * Renders the HTML for an option that will render an image to select as the default image.
 */
function renderDefaultImageOption($imageId, $imageName, $selected) {
    $selectedAttr = $selected ? 'selected' : '';
    echo "
	<option 
		$selectedAttr
		class='image-option'
        data-img-src='images/$imageId' 
		data-img-class='data-img'
		id='$imageId'
        value='$imageId'>
    $imageName
    </option>";
}

?>

<script type="text/javascript">
var availableTags = [
<?php
	$availableKeywords = $keywordsDao->getAllKeywords();
	foreach ($availableKeywords as $k) {
		echo '"' . $k->getName() . '",';
	}
?>
];
</script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

<br/>
<br/>
<!-- Main Container -->
<div class="container-fluid">

	<br/>
	
	<!-- Main Content -->
    <div class="row" style="padding-bottom: 200px;">

		<!-- Form for editing -->
		<div class="col-sm-8 formSection rounded" style="margin: 0 auto; padding-top: 20px; padding-bottom: 50px;">
			
			<?php
			//
			// Generate the Admin interface if the user is an admin
			//
			if ($isAdmin) {
				$logs = $dao->getCapstoneProjectLogs($project->getId());
				$editors = $dao->getCapstoneProjectEditors($project->getId());
				renderAdminReviewPanel($project, $logs, $editors, $categoriesDao, $users, false);
			}
			?>

    
			<?php
			//
			// Following is the edit functionality available to proposers and admins
			//
			// Project type is temporarily not needed; the only project type used at this moment
			// is "Capstone".  The "Project Type", "Compensation", "Start by", and "Complete by" fields
			// have been set to display:none, making them hidden until they are needed in future releases.
			?>
			<br>
			<form id="formProject">
				<input type="hidden" id="projectId" name="id" value="<?php echo $pId; ?>" />
				<div class="col-sm-20">
					Project Title: <?php displayInfoTooltip($tooltipProjectTitleInput); ?> <font size="2" style="color: red;">*required</font>
					<input id="projectTitleInput" value="<?php echo $pTitle; ?>" 
						class="form-control form-control-lg" type="text" placeholder="Project Title"
						data-toggle="tooltip" data-placement="auto" title="<?php echo $tooltipProjectTitleInput; ?>" />
				</div>
				<br>
				<div class="row">
					<div class="col-sm-5">
						
						<div class="col-sm-20" style="display:none">
							<div class="form-group">
								<label for="projectTypeSelect">Project Type <?php displayInfoTooltip($tooltipProjectTypeSelect); ?></label>
								<select class="form-control input" id="projectTypeSelect" name="typeId" data-toggle="tooltip" 
									data-placement="bottom" title="<?php echo $tooltipProjectTypeSelect?>">
									<?php
									foreach ($types as $t) {
										$id = $t->getId();
										$name = $t->getName();
										$selected = $id == $pTypeId ? 'selected' : '';
										echo "<option $selected value='$id'>$name</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-20">
							<div class="form-group">
								<label for="projectFocusSelect">Project Focus <?php displayInfoTooltip($tooltipProjectFocusSelect); ?></label>
								<select class="form-control input" id="projectFocusSelect" name="focusId" data-toggle="tooltip" 
									data-placement="bottom"
									title="<?php echo $tooltipProjectFocusSelect; ?>">
									<?php
									foreach ($focuses as $f) {
										$id = $f->getId();
										$name = $f->getName();
										$selected = $id == $pFocusId ? 'selected' : '';
										echo "<option $selected value='$id'>$name</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-20" id="compensationDiv" style="display:none">
							<div class="form-group">
								<label for="compensationSelect">Compensation <?php displayInfoTooltip($tooltipCompensationSelect); ?></label>
								<select class="form-control input" id="compensationSelect" name="compensationId">
									<?php
									foreach ($compensations as $c) {
										$id = $c->getId();
										$name = $c->getName();
										$selected = $id == $pCompensationId ? 'selected' : '';
										echo "<option $selected value='$id'>$name</option>";
									}
									?>
								</select>
							</div>
						</div>	
							
						<div class="col-sm-20" id="numberGroupsDiv">
							<div class="form-group">
								<label for="numberGroupsSelect">Number of Groups <?php displayInfoTooltip($tooltipNumberGroupsDesiredText); ?></label>
								<select class="form-control input" id="numberGroupsSelect" name="numberGroupsId">
									<?php
										for ($n = 1; $n <= 8; $n++) {
											$selected = $n == $pNumberGroups ? 'selected' : '';
											echo "<option $selected value='$n'>$n</option>";
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-20">
							<label id="sponsoredSelectLabel" for="sponsoredSelect">
									Sponsored Project? <?php displayInfoTooltip($tooltipSponsored); ?>
								</label>
								<select class="form-control input" id="sponsoredSelect" name="isSponsored">
									<?php
									echo "<option value='1' ".($pIsSponsored == 1 ? 'selected' : '' ).">Yes</option>";
									echo "<option value='0' ".($pIsSponsored == 0 ? 'selected' : '' ).">No</option>";
									?>
								</select>
						</div>
					</div>
					<!-- Sidebar -->
					<div class="col-sm-3">
						<br>
						<div class="col-sm-20">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-btn">
										<span class="btn btn-outline-secondary btn-file" data-toggle="tooltip" 
											data-placement="bottom" title="<?php echo $tooltipImgBtn; ?>">
											Upload Image<input type="file" id="imgInp" accept=".jpg,.jpeg,.gif,.png">
										</span>
									</span>
									<input type="text" class="form-control" id="nameOfImageInput" value="" readonly>
								</div>
								<br/>
								<style>
									.data-img{
										background-color:#D1D1D1;
										width:40px;
										height:40px;
									}
								</style>
								<div id="indexDiv" style="width:100%;max-height:300px;display:grid;">
									<select id="defaultImageSelect" class="image-picker show-html">
										<?php
										$defaultImage = '';
										foreach ($pImages as $image) {

											$isDefault = $image->getIsDefault();
											$id = $image->getId();
											$name = $image->getName();
											renderDefaultImageOption($id, $name, $isDefault);
											if ($isDefault) {
												$defaultImage = "images/$id";
												$defaultImageName = $name;
											}
										}
										?>
									</select>
									<script type="text/javascript">
										$("#defaultImageSelect").imagepicker();
										$("#defaultImageSelect").data('picker').sync_picker_with_select();
										<?php if (isset($defaultImageName)) {
											echo "$('#nameOfImageInput').val('$defaultImageName');";
										} ?>
									</script>
								</div>
							<img id="img-upload" max-width="400px;" max-height="200px;" src="<?php echo $defaultImage; ?>"/>
							<!-- TODO: Need to put this icon on the image displayed and connect it to the delete functions in the DAO-->
							<i class="fas fa-trash-alt" style="color:red;" onclick="deleteImg();"></i>
							</div>
						</div>	
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-sm-5">
						<div class="col-sm-20">
							<div class="form-group">
								<div class="ui-widget">
									<label for="keywordsInput">
										Add Keywords to Project: <?php displayInfoTooltip($tooltipKeywords); ?><br>
										<font size="2">Press Enter after each keyword.</font>
									</label>
									<input id="keywordsInput" class="form-control input">
								</div>
								<div id="keywordsDiv" name="keywords">
									<?php
										$preexistingKeywords = $keywordsDao->getKeywordsForEntity($pId);
										if($preexistingKeywords){
											foreach ($preexistingKeywords as $k) {
												if (trim(Security::HtmlEntitiesEncode($k->getName())) != '') {
													echo '<span class="badge badge-light keywordBadge">' . Security::HtmlEntitiesEncode($k->getName()) . ' <i class="fas fa-times-circle"></i></span>';
												}
											}
										}
									?>
								</div>
							</div>
						</div>
					</div>
<!--					<div class="col-sm-5">
						<div class="col-sm-20">
							<div class="form-group">
								<div class="ui-widget">
									<label for="preferredCoursesInput">
										Add Preferred Courses Completed for Project: <?php displayInfoTooltip($tooltipPreferredCourses); ?><br>
									</label>
									<select id="preferredCoursesInput" class="form-control input">
										<option selected="selected"></option>
										<?php
/*										foreach ($preferredCourses as $p) {
											$id = $p->getId();
											$code = $p->getCode();
											$name = $p->getName();
											// $selected = $id == $pFocusId ? 'selected' : '';
											echo "<option value='$code'>$code $name</option>";
										}
*/										?>
									</select>
								</div>
								<div id="preferredCoursesDiv" name="preferredCourses">
									<?php
/*										$preexistingPreferredCourses = $preferredCoursesDao->getPreferredCoursesForEntity($pId);
										if($preexistingPreferredCourses){
											foreach ($preexistingPreferredCourses as $p) {
												if (trim(Security::HtmlEntitiesEncode($p->getCode())) != '') {
													echo '<span class="badge badge-light preferredCourseBadge">' . Security::HtmlEntitiesEncode($p->getCode()) . ' <i class="fas fa-times-circle"></i></span>';
												}
											}
										}
*/									?>
								</div>
							</div>
						</div>
					</div>
-->				</div>

				
				<hr>
				<br>

				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="projectDescriptionText">
								Project Description <?php displayInfoTooltip($tooltipProjectDescriptionText); ?> <font size="2" style="color:red;">*required</font>
							</label>
							<textarea class="form-control input" id="projectDescriptionText" name="description"
								rows="3" data-toggle="tooltip" 
								data-placement="top" 
								title="<?php echo $tooltipProjectDescriptionText?>"><?php 
									echo $pDescription; 
								?></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="motivationText">
								Motivation <?php displayInfoTooltip($tooltipMotivationText); ?> <font size="2" style="color:red;">*required</font>
							</label>
							<textarea class="form-control input" id="motivationText" name="motivation"
								rows="4"><?php
									echo $pMotivation;
								?></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="objectivesText">
								Objectives/Deliverables <?php displayInfoTooltip($tooltipObjectivesText); ?> <font size="2" style="color:red;">*required</font>
							</label>
							<textarea class="form-control input" id="objectivesText" name="objectives" 
								rows="4"><?php
									echo $pObjectives;
								?></textarea>
						</div>
					</div>
					
				</div>
				<div class="row" id="dateDiv" style="display:none">
					<div class="col-sm-5">
						<div class="form-group">
							Start By <?php displayInfoTooltip($tooltipStartByText); ?>
							<div class="input-group date" id="startbydate" data-target-input="nearest">
									<input type="text" id="startByText" name="dateStart" 
										class="form-control datetimepicker-input input" 
										value="<?php echo $pDateStart; ?>" data-target="#startbydate"/>
									<div class="input-group-append" data-target="#startbydate" 
										data-toggle="datetimepicker">
											<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
									</div>
							</div>
						</div>
						<div class="form-group">
							Complete By <?php displayInfoTooltip($tooltipCompleteByText); ?>
							<div class="input-group date" id="endbydate" data-target-input="nearest">
								<input type="text" id="completeByText"  name="dateEnd"
									class="form-control datetimepicker-input input" 
									value="<?php echo $pDateEnd; ?>" data-target="#endbydate"/>
								<div class="input-group-append" data-target="#endbydate" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
								</div>
							</div>
						</div>
					</div>
					
				</div>
				
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="minQualificationsText">
									Minimum Qualifications <?php displayInfoTooltip($tooltipMinQualificationsText); ?>
								</label>
								<textarea class="form-control input" id="minQualificationsText" name="minQualifications" 
									rows="9"><?php
										echo $pMinQual;
									?></textarea>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="preferredQualificationsText">
									Preferred Qualifications <?php displayInfoTooltip($tooltipPrefQualificationsText); ?>
								</label>
								<textarea class="form-control input" id="preferredQualificationsText" name="preferredQualifications" 
									rows="9"><?php
										echo $pPreferredQual;
									?></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="commentsText">Special Comments <?php displayInfoTooltip($tooltipCommentsText); ?></label>
							<textarea class="form-control input" id="commentsText" name="comments" rows="3"><?php
									echo $pComments; 
								?></textarea>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label for="websiteText">Website <?php displayInfoTooltip($tooltipWebsiteText); ?></label>
							<textarea class="form-control input" id="websiteText" name="websiteLink" 
								rows="1"><?php 
									echo $pWebsiteLink; 
								?></textarea>
						</div>
					</div>
					<div class="col-sm-12"> 
						<div class="form-group">
							<label for="videoText">Video <?php displayInfoTooltip($tooltipVideoText); ?></label>
							<textarea class="form-control input" id="videoText" name="videoLink" 
								rows="1"><?php 
									echo $pVideoLink; 
								?></textarea>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label for="additionalEmailsText">Additional Emails <?php displayInfoTooltip($tooltipAdditionalEmailsText); ?></label>
							<textarea class="form-control input" id="additionalEmailsText" name="additionalEmails"
								rows="1"><?php
									echo $pAdditionalEmails;
								?></textarea>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">
						<!-- Button trigger modal -->
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ndaModal" style="display: block; margin: auto;">
						NDA/IP Requirement
						</button>

						<!-- Modal -->
						<div class="modal fade" id="ndaModal" tabindex="-1" role="dialog" aria-labelledby="ndaModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="ndaModalLabel">NDA/IP Requirement</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
										</button>
									</div>
								<div class="modal-body">
								<p>
									<BR>
									If your company intends to provide proprietary materials or confidential information requiring an NDA, OSU 
									can arrange for a written agreement to reviewed and signed amongst the students, your company, and OSU.
									<br><br>
									Such an agreement will authorize the students to use and discuss the provided materials or information 
									with each other and their instructor in confidence.
									<br><br>
									<b>The university will not participate in any agreement that requires students to transfer intellectual 
									property rights ownership to your company or puts overly burdensome confidentiality obligations on 
									the students.</b>
									<br><br>Though OSU certainly appreciates your company’s sponsorship, we strongly discourage any agreements 
									that could deter students from sharing the results of their academic work at OSU with fellow students, 
									parents or future employers.
									<br><br>
									This does not prevent a separate arrangement between you each student individually.
									</p>
								</div>
								<div class="modal-footer">
									<h6><label id="ndaSelectLabel" for="ndaSelect">
										NDA/IP <?php displayInfoTooltip($tooltipNdaSelect); ?> <font size="2" style="color:red;">*required</font>
									</label></h6>
									<select class="form-control input" id="ndaSelect" name="ndaIpId">
										<?php
											foreach ($ndaips as $n) {
											$id = $n->getId();
											$name = $n->getName();
											$selected = $id == $pNdaIpId ? 'selected' : '';
											echo "<option $selected value='$id'>$name</option>";
										}
										?>
									</select>
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Save</button>
								</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<br>
				<hr>

				<!-- Action Buttons under form -->
				<div class="row">
					<!-- Help Button -->
					<div class="col-sm-5">
						<div id="helpDiv">
							<a href="mailto:eecs_capstone_staff@engr.oregonstate.edu" target="_blank" class="btn btn-help">Questions?</a>
						</div>
					</div>										
					<!-- Action Buttons -->
					<div class="col-sm-3" style="margin-left: auto; margin-right: 50px;">
						<div class="row" >
							
							<div class="row">
								<div class="col-sm-1"></div>
								<div id="cssloader" class="col-sm-2"></div>
								<div class="row" id="formActions">                   
									<?php 
									// Display the following only when the user is the proposer
									if ($isProposer || $isAdmin) {
										if ($approved){
											echo "
											<div class='alert alert-success'>
												Approved! Your project is now accepting applicants.  Changes can no longer be made to your project.  To make changes, please contact the administer with the information you'd like to change or have them unapprove your project so you can resubmit for approval.  
											</div>
											";
											if ($isAdmin) {
												echo("
												<button id='saveProjectDraftBtn' class='btn btn-success capstone-nav-btn' type='button' 
												data-toggle='tooltip' data-placement='bottom' 
												title='$tooltipUpdateProjectDraftBtn'>
												Update Project Information</button>
												");
											}
										}
										else if ($submitted) {
											echo "
											<div class='alert alert-success'>
												Submitted. Your project is pending approval. Changes cannot be made while project is pending approval.
											</div>
											";

											if ($isAdmin) {
												echo("
												<button id='saveProjectDraftBtn' class='btn btn-success capstone-nav-btn' type='button' 
												data-toggle='tooltip' data-placement='bottom' 
												title='$tooltipSaveProjectDraftBtn'>
												Update Project Information</button>
												");
											}
										} else {
											echo "
											<button id='saveProjectDraftBtn' class='btn btn-success capstone-nav-btn' type='button' 
												data-toggle='tooltip' data-placement='bottom' 
												title='$tooltipSaveProjectDraftBtn'>
												Save Project Draft</button>
											
											<button name='submitButtonPressed' id='submitForApprovalBtn' 
												class='btn btn-primary capstone-nav-btn' type='button' data-toggle='tooltip' 
												data-placement='bottom' title='$tooltipSubmitForApprovalBtn'>
												Submit for Approval</button>
											";
										}
									}?>
								</div>
							</div>
							
						</div>
					</div>
				</div>		
			</form>
		</div>
	</div>
</div>


<!-- include link for rich text editing and create editor objects for each field -->
<script src="https://cdn.ckeditor.com/ckeditor5/31.1.0/classic/ckeditor.js"></script>
<script>
		
	function getKeywords(text) {
		allowedChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_ "

		newText = ""
		for (let i = 0; i < text.length; i++) {
			if (allowedChars.includes(text[i])) {
				newText = newText + text[i];
			}
		}
		var textArr = newText.split(" ")
		for (let i = 0; i < textArr.length; i++) {
			if (! availableTags.includes(textArr[i])) {
				availableTags.push(textArr[i])
			}
		}
	}

	// https://www.geeksforgeeks.org/debouncing-in-javascript/
	const debounce = (func, delay) => {
		let debounceTimer;
		return function() {
			const context = this;
			const args = arguments;
			clearTimeout(debounceTimer)
			debounceTimer = setTimeout(() => func.apply(context, args), delay);
		}
	}

	document.addEventListener('readystatechange', event => {
		if (event.target.readyState === "complete") {
			text = document.getElementById("projectDescriptionText").innerHTML
			text = text.replace("&lt;p&gt;","").replace("&lt;/p&gt;","")
			getKeywords(text);

			document.getElementById("keywordsInput").autocomplete = "on";
		}
	})

	let descriptionEditor;
    ClassicEditor
        .create( document.querySelector( '#projectDescriptionText' ), {
			toolbar: [ 'heading', '|',
				'bold', 'italic', '|',
				'bulletedList', 'numberedList', 'blockQuote', '|',  
				'link', 'unlink', '|',
				'inserttable', '|', 
				'undo', 'redo' ]
		} )
		.then( newEditor => {
			descriptionEditor = newEditor;
			newEditor.model.document.on('change', debounce(function() {
				text = newEditor.getData();
				text = text.replace("<p>", "").replace("</p>","")
				getKeywords(text);
			}, 2000))
    	} )
        .catch( error => {
            console.error( error );
        } );
	let motivationEditor;
    ClassicEditor
        .create( document.querySelector( '#motivationText' ), {
			toolbar: [ 'heading', '|',
				'bold', 'italic', '|',
				'bulletedList', 'numberedList', 'blockQuote', '|',  
				'link', 'unlink', '|',
				'inserttable', '|', 
				'undo', 'redo', '|']
		} )
		.then( newEditor => {
			motivationEditor = newEditor;
    	} )
        .catch( error => {
            console.error( error );
        } );
	let objectivesEditor;
    ClassicEditor
        .create( document.querySelector( '#objectivesText' ), {
			toolbar: [ 'heading', '|',
				'bold', 'italic', '|',
				'bulletedList', 'numberedList', 'blockQuote', '|',  
				'link', 'unlink', '|',
				'inserttable', '|', 
				'undo', 'redo' ]
		} )
		.then( newEditor => {
			objectivesEditor = newEditor;
    	} )
        .catch( error => {
            console.error( error );
        } );
	let minQualEditor;
    ClassicEditor
        .create( document.querySelector( '#minQualificationsText' ), {
			toolbar: [ 'heading', '|',
				'bold', 'italic', '|',
				'bulletedList', 'numberedList', 'blockQuote', '|',  
				'link', 'unlink', '|',
				'inserttable', '|', 
				'undo', 'redo' ]
		} )
		.then( newEditor => {
			minQualEditor = newEditor;
    	} )
        .catch( error => {
            console.error( error );
        } );
	let prefQualEditor;
    ClassicEditor
        .create( document.querySelector( '#preferredQualificationsText' ), {
			toolbar: [ 'heading', '|',
				'bold', 'italic', '|',
				'bulletedList', 'numberedList', 'blockQuote', '|',  
				'link', 'unlink', '|',
				'inserttable', '|', 
				'undo', 'redo' ]		
		} )
		.then( newEditor => {
			prefQualEditor = newEditor;
    	} )
        .catch( error => {
            console.error( error );
        } );
	let commentsEditor;
    ClassicEditor
        .create( document.querySelector( '#commentsText' ), {
			toolbar: [ 'heading', '|',
				'bold', 'italic', '|',
				'bulletedList', 'numberedList', 'blockQuote', '|',  
				'link', 'unlink', '|',
				'inserttable', '|', 
				'undo', 'redo' ]		
		} )
		.then( newEditor => {
			commentsEditor = newEditor;
    	} )
        .catch( error => {
            console.error( error );
        } );




</script>

<?php 

if(($submitted || $approved) && !$isAdmin) {
	echo "<script>$('#formProject .input').attr('readonly', true);</script>";
}

include_once PUBLIC_FILES . '/modules/footer.php'; 

?>