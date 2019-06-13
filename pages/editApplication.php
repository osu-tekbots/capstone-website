<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneApplicationsDao;
use DataAccess\CapstoneProjectsDao;
use Model\CapstoneApplicationStatus;
use Util\Security;

session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$applicationId = $_GET['id'];

$isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);

// Redirect the user if they are not logged in or no ID is provided in the query string
allowIf($applicationId != '' && $isLoggedIn);

$userId = $_SESSION['userID'];

$isAdmin = $_SESSION['accessLevel'] == 'Admin';

$applicationsDao = new CapstoneApplicationsDao($dbConn, $logger);
$application = $applicationsDao->getApplication($applicationId);

// We also need to get the project because the application does not retrieve the proposer information
$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$project = $projectsDao->getCapstoneProject($application->getCapstoneProject()->getId());

// Redirect the user if the application is not found or if the user does not own the application
allowIf($application && ($application->getStudent()->getId() == $userId || $isAdmin));

// Get application information
$justification = $application->getJustification();
$time_available = $application->getTimeAvailable();
$skill_set = $application->getSkillSet();
$external_link = $application->getPortfolioLink();
$applicationStatusId = $application->getStatus()->getId();
$submitted = $applicationStatusId == CapstoneApplicationStatus::SUBMITTED;
$readOnly = $submitted ? 'readonly' : '';

// Get Project Information
$projectTitle = Security::HtmlEntitiesEncode($project->getTitle());
$description = Security::HtmlEntitiesEncode($project->getDescription());
$motivation = Security::HtmlEntitiesEncode($project->getMotivation());
$objectives = Security::HtmlEntitiesEncode($project->getObjectives());
$minQualifications = Security::HtmlEntitiesEncode($project->getMinQualifications());
$prefQualifications = Security::HtmlEntitiesEncode($project->getPreferredQualifications());

$buttonsHtml = $submitted ? "
    <div class='alert alert-success'>
        Submitted
    </div>
" : "
    <button class='btn btn-light mr-3' type='button' id='btnSaveApplicationDraft'>
        Save Draft
    </button>
    <button class='btn btn-outline-primary' type='submit'>
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
        </div>
    </div>
    <form id="formApplication">
        <input type="hidden" name="applicationId" value="<?php echo $applicationId; ?>" />
        <div class="form-group row">
            <div class="col-12">
                <label>Justification</label>
                <textarea required <?php echo $readOnly; ?> name="justification" class="form-control" rows="4"><?php 
                    echo $justification; ?></textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label>Skill Set</label>
                <textarea required <?php echo $readOnly; ?> name="skillSet" class="form-control" rows="5"><?php 
                    echo $skill_set; ?></textarea>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label>Time Available</label>
                    <input required <?php echo $readOnly; ?> name="timeAvailable" class="form-control" max="256" 
                        value="<?php echo $time_available; ?>">
                </div>
                <div class="form-group ">
                    <label>Portfolio Link</label>
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