<?php
use DataAccess\CapstoneApplicationsDao;
use DataAccess\CapstoneProjectsDao;
use Model\CapstoneApplicationStatus;

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

$justification = $application->getJustification();
$time_available = $application->getTimeAvailable();
$skill_set = $application->getSkillSet();
$external_link = $application->getPortfolioLink();

$projectTitle = $project->getTitle();
$description = $project->getDescription();
$motivation = $project->getMotivation();
$objectives = $project->getObjectives();
$minQualifications = $project->getMinQualifications();
$prefQualifications = $project->getPreferredQualifications();

$firstName = $project->getProposer()->getFirstName();
$lastName = $project->getProposer()->getLastName();

$submitted = $application->getStatus()->getId() == CapstoneApplicationStatus::SUBMITTED;

$title = 'Edit Application';
include_once PUBLIC_FILES . '/modules/header.php';

?>

<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-1">
        </div>
        <div class="col-sm-6 jumbotron scroll">
            <form id="formApplication" onsubmit="return onSaveApplicationDraft();">
                <input type="hidden" name="applicationId" value="<?php echo $applicationId; ?>" />
                <div class="row">
                    <div class="col-sm-7">
                        <h2>Application for <?php echo $projectTitle; ?></h4>
                        <h5>By: <?php echo $firstName . ' ' . $lastName; ?></h5>
                    </div>
                    <div id="cssloader" class="col-sm-1">
                    </div>
                    <div class="col-sm-4">
                    <?php
                        if(!$submitted): ?>
                        <button id="saveApplicationDraftBtn" class="btn btn-success capstone-nav-btn" 
                            type="submit" >Save Draft</button>
                            <button name="submitButtonPressed" id="submitBtn" class="btn btn-primary capstone-nav-btn"
                                type="button">Submit</button>
                        <?php
                        else: ?>

                            <h5>Submitted</h5>

                        <?php
                        endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6"> 
                        <div class="form-group">
                            <label for="justificationText">
                                Justification <font size="2" style="color:red;">*required</font>
                            </label>
                            <textarea <?php if($submitted) echo 'readonly'; ?> 
                                class="form-control" id="justificationText" name="justification" rows="6"><?php 
                                echo $justification; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="externalLinkText">External Link</label>
                            <textarea <?php if($submitted) echo 'readonly'; ?>  
                                class="form-control" id="externalLinkText" name="portfolioLink" rows="1"><?php 
                                echo $external_link; ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="skillSetText">
                                Skill Set <font size="2" style="color:red;">*required</font>
                            </label>
                            <textarea <?php if($submitted) echo 'readonly'; ?>  
                                class="form-control" id="skillSetText" name="skillSet" rows="5"><?php 
                                echo $skill_set; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="timeAvailableText">
                                Time Available <font size="2" style="color:red;">*required</font>
                            </label>
                            <textarea <?php if($submitted) echo 'readonly'; ?> c
                                class="form-control" id="timeAvailableText" name="timeAvailable" rows="3"><?php 
                                echo $time_available; ?></textarea>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <div class="col-sm-1">
        </div>
        <div class="col-sm-4 scroll jumbotron capstoneJumbotron">
            <?php
            echo "
                <br>
                <h2>$projectTitle</h2> 
                <p>$description</p>
                <br><br>
                <h5>Motivation:</h5>
                <p>$motivation</p>
                <h5>Objectives:</h5>
                <p>$objectives</p>
                <h5>Minimum Qualifications:</h5>
                <p>$minQualifications</p>
                <h5>Preferred Qualifications:</h5>
                <p>$prefQualifications</p>
            ";
            ?>
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

    // Validate
    if(!validateForm(body)) return false;;

    // Make the request
    api.post('/applications.php', body).then(res => {
        snackbar(res.message, 'success');
    }).catch(err => {
        snackbar(err.message, 'error');
    });

    return false;

}
$('#saveApplicationDraftBtn').on('click', onSaveApplicationDraft);

/**
 * Event handler for submitting an application
 */
function onSubmitApplication() {

    // Ensure the form is valid
    let form = getApplicationAsJson();
    if(!validateForm(form)) return;

    // Make the request to submit the application
    let body = {
        action: 'submitApplication',
        applicationId: form.applicationId
    };
    api.post('/applications.php', body).then(res => {
        snackbar(res.message, 'success');
    }).catch(err => {
        snackbar(err.message, 'error');
    });
}
$('#submitBtn').on('click', onSubmitApplication);

</script>

<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>