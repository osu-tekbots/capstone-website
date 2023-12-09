<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneApplicationsDao;
use DataAccess\CapstoneProjectsDao;
use Model\CapstoneApplicationStatus;
use Util\Security;

if (!session_id()) session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$applicationId = $_GET['id'];

$isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);

// Redirect the user if they are not logged in or no ID is provided in the query string
//allowIf($authorizedToProceed, 'index.php');

$userId = $_SESSION['userID'];

$isAdmin = $_SESSION['accessLevel'] == 'Admin';

$authorizedToProceed = $applicationId . '' != '' && $userId . '' != '';
allowIf($authorizedToProceed);

$applicationsDao = new CapstoneApplicationsDao($dbConn, $logger);
$application = $applicationsDao->getApplication($applicationId);

$authorizedToProceed = ($application->getStudent()->getId() == $_SESSION['userID']) || $isAdmin;

allowIf($authorizedToProceed);

// We also need to get the project because the application does not retrieve the proposer information
$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$project = $projectsDao->getCapstoneProject($application->getCapstoneProject()->getId());

// Redirect the user if the application is not found or if the user does not own the application
allowIf($application && ($application->getStudent()->getId() == $userId || $isAdmin));

// Get application information
$justification = Security::HtmlEntitiesEncode($application->getJustification());
$time_available = $application->getTimeAvailable();
$skill_set = $application->getSkillSet();
$external_link = $application->getPortfolioLink();
$applicationStatusId = $application->getStatus()->getId();
$submitted = $applicationStatusId == CapstoneApplicationStatus::SUBMITTED;
$readOnly = $submitted ? 'readonly' : '';

// Get Project Information
$projectTitle = Security::HtmlEntitiesEncode($project->getTitle());

$description = Security::HtmlEntitiesEncode($project->getDescription());
// decode rich html saved from rich text
$description = htmlspecialchars_decode($description);

$motivation = Security::HtmlEntitiesEncode($project->getMotivation());
// decode rich html saved from rich text
$motivation = htmlspecialchars_decode($motivation);

$objectives = Security::HtmlEntitiesEncode($project->getObjectives());
// decode rich html saved from rich text
$objectives = htmlspecialchars_decode($objectives);

$minQualifications = Security::HtmlEntitiesEncode($project->getMinQualifications());
// decode rich html saved from rich text
$minQualifications = htmlspecialchars_decode($minQualifications);

$prefQualifications = Security::HtmlEntitiesEncode($project->getPreferredQualifications());
// decode rich html saved from rich text
$prefQualifications = htmlspecialchars_decode($prefQualifications);

// Set tooltip texts
$createApplicationInfo = 'When creating an application to a project it is important to present yourself in a clear and professional manner. The project partner, after getting your application, can indicate a preference to the course instructors but course instructors have the final word in assembling groups. You are only allowed to apply once to each project so plan accordingly. If you would like to communicate back and forth, be sure to include your email address in the application so that the project partner has it.';
$tooltipJustificationInput = 'Use this field to explain why you are a good candidate for this project. You might want to include your motivations in addition to your experience.';
$tooltipSkillSetInput = 'List your applicable skills here that make you an asset to this project.';
$tooltipTimeAvailableInput = 'List the number of hours you can devote to this project a week or any time circumstances that will affect your time involved in the project.';
$tooltipPortfolioLinkInput = 'Include a link to a publicly visible portfolio if you have one to show previous work.';
$tooltipSaveDraftBtn = 'Save this application for later submission.';
$tooltipSubmitBtn = 'Submit this application. You can not modify an application after submission.';


$buttonsHtml = $submitted ? "
    <div class='alert alert-success'>
        Submitted
    </div>
" : "
    <button class='btn btn-light mr-3' type='button' id='btnSaveApplicationDraft' 	data-toggle='tooltip' data-placement='bottom' 
    title='$tooltipSaveDraftBtn'>
        Save Draft
    </button>
    <button class='btn btn-outline-primary' type='submit' data-toggle='tooltip' data-placement='bottom' 
    title='$tooltipSubmitBtn'>
        Submit Application
    </button>
";

$title = 'Edit Application';
include_once PUBLIC_FILES . '/modules/header.php';

?>

<br/>
<br/>
<div class="container">
    <div class="row">
        <div class="col">
            <h1>Student Application for <?php echo $projectTitle; ?></h1>
            <div class="alert alert-info" role="alert">
                <?php echo ($createApplicationInfo);?>
            </div>
        </div>
    </div>
    <form id="formApplication">
        <input type="hidden" name="applicationId" value="<?php echo $applicationId; ?>" />
        <div class="form-group row">
            <div class="col-12">
                <label>Justification <?php displayInfoTooltip($tooltipJustificationInput); ?></label>
                <textarea required <?php echo $readOnly; ?> name="justification" class="form-control" rows="4" ><?php 
                echo $justification; ?></textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label>Skill Set <?php displayInfoTooltip($tooltipSkillSetInput); ?></label>
                <textarea required <?php echo $readOnly; ?> name="skillSet" class="form-control" rows="5"><?php 
                    echo $skill_set; ?></textarea>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label>Time Available <?php displayInfoTooltip($tooltipTimeAvailableInput); ?></label>
                    <input required <?php echo $readOnly; ?> name="timeAvailable" class="form-control" max="256" 
                        value="<?php echo $time_available; ?> ">
                </div>
                <div class="form-group ">
                    <label>Portfolio Link <?php displayInfoTooltip($tooltipPortfolioLinkInput); ?></label>
                    <input <?php echo $readOnly; ?> name="portfolioLink" class="form-control" max="512" 
                        value="<?php echo $external_link; ?>">
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col" id="formActions">
                <?php echo $buttonsHtml; ?>
            </div>
        </div>
    </form>
    <hr/>
	<div class="row project-summary">
		<div class="col">
			<h3>Project Summary</h3>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<h4>Description</h4>
			<p><?php echo $description; ?></p>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<h4>Motivation</h4>
			<p><?php echo $motivation; ?></p>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<h4>Objectives</h4>
			<p><?php echo $objectives; ?></p>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<h4>Preferred Qualifications</h4>
			<p><?php echo $prefQualifications; ?></p>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<h4>Minimum Qualifications</h4>
			<p><?php echo $minQualifications; ?></p>
		</div>
	</div>
</div>

<script type="text/javascript">

/**
 * Serializes the form element as a JSON object with the 'name' attribute of the inputs and
 * textareas as the keys in the object.
 * @return {object}
 */
function getApplicationAsJson() {
    // Get the form data
    let data = new FormData(document.getElementById('formApplication'));
    let json = {};
    for(const [key, value] of data.entries()) {
        json[key] = value;
    }
    return json;
}

/**
 * Validates the form, displaying an error messages and returning whether the form is valid
 */
function validateForm(form) {
    if(form.justification == '') {
        snackbar('Please provide a justification', 'error');
        return false;
    }
    if(form.skillSet == '') {
        snackbar('Please provide your skillset', 'error');
        return false;
    }
    if(form.timeAvailable == '') {
        snackbar('Please indicate when you are available', 'error');
        return false;
    }
    return true;
}

/**
 * Event handler for saving an application draft. The function must return false so that the default action
 * of refreshing the page on a form submit is aborted.
 */
function onSaveApplicationDraft() {

    // Get the form data
    let body = getApplicationAsJson();
    body.action = 'saveApplication';

    // Make the request
    api.post('/applications.php', body).then(res => {
        snackbar(res.message, 'success');
    }).catch(err => {
        snackbar(err.message, 'error');
    });

    return false;

}
$('#btnSaveApplicationDraft').click(onSaveApplicationDraft);

/**
 * Event handler for submitting an application
 */
function onSubmitApplication() {

    if(!confirm('You are about to submit your application. You will not be able to make changes to it after submission.'))
        return false;

    // Ensure the form is valid
    let form = getApplicationAsJson();
    if(!validateForm(form)) return;

    // Make the request to submit the application
    let body = getApplicationAsJson();
    body.action = 'submitApplication';

    api.post('/applications.php', body).then(res => {
        snackbar(res.message, 'success');
        onSubmitApplicationSuccess();
    }).catch(err => {
        snackbar(err.message, 'error');
    });

    return false;
}
$('#formApplication').submit(onSubmitApplication);

/**
 * Event handler for when the submission is successful. We need to change some of the HTML so that the user can't
 * resubmit or otherwise modify the review without the need to refresh the page
 */
function onSubmitApplicationSuccess() {
    $('#formApplication .form-control').attr('readonly', true);
    $('#formActions').html(`
        <div class='alert alert-success'>
            Submitted
        </div>
    `);

}

</script>

<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>