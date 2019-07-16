<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneApplicationsDao;
use Model\CapstoneInterestLevel;
use Util\Security;

if (!session_id()) {
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

// Get application information
$applicationId = $application->getId();
$justification = Security::HtmlEntitiesEncode($application->getJustification());
$time_available = Security::HtmlEntitiesEncode($application->getTimeAvailable());
$skill_set = Security::HtmlEntitiesEncode($application->getSkillSet());
$external_link = $application->getPortfolioLink();
$user_id = $application->getStudent()->getId();
$firstName = Security::HtmlEntitiesEncode($application->getStudent()->getFirstName());
$lastName = Security::HtmlEntitiesEncode($application->getStudent()->getLastName());

$buttonPortfolioLink = !empty($external_link) ? "
	<a href='$external_link' target='_blank' class='btn btn-primary'>
		View Portfolio
	</a>
	<br/><br/>
" : '<br/>';

// Get project information
$project_id = $application->getCapstoneProject()->getId();
$title = Security::HtmlEntitiesEncode($application->getCapstoneProject()->getTitle());
$description = Security::HtmlEntitiesEncode($application->getCapstoneProject()->getDescription());
$motivation = Security::HtmlEntitiesEncode($application->getCapstoneProject()->getMotivation());
$objectives = Security::HtmlEntitiesEncode($application->getCapstoneProject()->getObjectives());
$minQualifications = Security::HtmlEntitiesEncode($application->getCapstoneProject()->getMinQualifications());
$prefQualifications = Security::HtmlEntitiesEncode($application->getCapstoneProject()->getPreferredQualifications());

// Get review information
$reviewInterest = $application->getReviewInterestLevel()->getId();
$comments = Security::HtmlEntitiesEncode($application->getReviewProposerComments());

// Generate a selection for the interest level. If it is equal to the currently selected interest level,
// mark it as selected.
$interestLevels = $dao->getApplicationReviewInterestLevels();
$interestLevelSelectHtml = "
<select class='form-control' name='interestLevelId'>
";

foreach ($interestLevels as $level) {
    $lId = $level->getId();
    $lName = $level->getName();

    $selected = $reviewInterest == $lId ? 'selected' : '';

    $interestLevelSelectHtml .= "
		<option $selected value='$lId'>$lName</option>
	";
}

$interestLevelSelectHtml .= '
</select>
';


?>
<br/>
<br/>
<div class="container">
	<div class="row">
		<div class="col-md-6 offset-md-3">
			<h1>Application Review</h1>
			<h4>for <?php echo $title; ?></h4>
			<form id="formReviewApplication">
				<input type="hidden" name="applicationId" value="<?php echo $applicationId; ?>" />
				<div class="form-group">
					<label>Comments</label>
					<textarea class="form-control" name="comments" rows="3"><?php echo $comments; ?></textarea>
				</div>
				<div class="form-group">
					<label>Interest Level</label>
					<?php echo $interestLevelSelectHtml; ?>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-outline-primary">
						Submit
					</button>
				</div>
			</form>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col">
			<h3>Student Application</h3>
			<h5>Submitted by: <?php echo $firstName . ' ' . $lastName; ?></h5>
			<?php echo $buttonPortfolioLink; ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h4>Justification</h4>
			<p><?php echo $justification; ?></p>
		</div>
		<div class="col-md-6">
			<h4>Skill Set</h4>
			<p><?php echo $skill_set; ?></p>
		</div>
		<div class="col-md-6">
			<h4>Time Available</h4>
			<p><?php echo $time_available; ?></p>
		</div>
	</div>
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
 * Retrieves the values associated with named inputs from the HTML form with the provided ID.
 * @param {string} formId the DOM ID of the form element
 * @returns {object} an object with form values keyed by their HTML input names
 */
function getFormValuesAsJson(formId) {
    let form = new FormData(document.getElementById(formId));

    let json = {};

    for(const [key, value] of form.entries()) {
        json[key] = value;
    }

    return json;
}

/**
 * Sends a request to save the application review information to the server when the user submits the review
 * form.
 */
function onReviewApplicationFormSubmit() {

	body = getFormValuesAsJson('formReviewApplication');

	body.action = 'reviewApplication';

	api.post('/applications.php', body).then(res => {
		snackbar(res.message, 'success');
	}).catch(err => {
		snackbar(err.message, 'error');
	});

	return false;
}
$('#formReviewApplication').submit(onReviewApplicationFormSubmit);

</script>

<?php include_once PUBLIC_FILES . '/modules/footer.php' ?>