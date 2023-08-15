<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL); 

include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use DataAccess\CapstoneApplicationsDao;
use DataAccess\UsersDao;
use DataAccess\KeywordsDao;
use DataAccess\CategoriesDao;
//use DataAccess\PreferredCoursesDao;
use Util\Security;

include PUBLIC_FILES . '/lib/shared/authorize.php';
include_once PUBLIC_FILES . '/modules/button.php';

$title = 'Single Project';
$js = array(
    array(
        'defer' => 'true',
        'src' => 'assets/js/admin-review.js'
    )
);
include_once PUBLIC_FILES . '/modules/header.php';

$pid = $_GET['id'];
allowIf($pid . '' != '');

$isLoggedIn = isset($_SESSION['userID']) && $_SESSION['userID'] . ''  != '';
if($isLoggedIn) {
	$userId = $_SESSION['userID'];
	$isAdmin = $_SESSION['accessLevel'] == 'Admin';
} else {
	$userId = null;
	$isAdmin = false;
}
include_once PUBLIC_FILES . '/modules/admin-review.php';

$dao = new CapstoneProjectsDao($dbConn, $logger);
$usersDao = new UsersDao($dbConn, $logger);
$applicationsDao = new CapstoneApplicationsDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);
$categoriesDao = new CategoriesDao($dbConn, $logger);
//$preferredCoursesDao = new PreferredCoursesDao($dbConn, $logger);
$project = $dao->getCapstoneProject($pid);
$proposer = $project->getProposerId();

// Check if project is hidden or not approved or if the project is the project is the proposers

/* Removed while I resolve the multiple editor ability
$authorizedEditor = False;
$authorizedEditors = $dao->getCapstoneProjectEditors($project->getId());
if ($authorizedEditors) {
	foreach ($authorizedEditors as $editor) {
		if ($editor->getId() == $userId) {
			$authorizedEditor = True;
		}
	}
}
*/
allowIf( ($project && !($project->getIsHidden() && !$isAdmin)) || ($project && ($proposer == $userId)) || $authorizedEditor);

$title = Security::HtmlEntitiesEncode($project->getTitle());
$status = $project->getStatus()->getName();
$type = $project->getType()->getName();
$year = $project->getDateCreated()->format('Y');
$website = Security::HtmlEntitiesEncode($project->getWebsiteLink());
$video = Security::HtmlEntitiesEncode($project->getVideoLink());
$start_by = $project->getDateStart()->format('F j, Y');
$complete_by = $project->getDateEnd()->format('F j, Y');

$pref_qualifications = Security::HtmlEntitiesEncode($project->getPreferredQualifications());
if ($pref_qualifications == '')
	$pref_qualifications = "None Listed";
// decode rich html saved from rich text
$pref_qualifications = htmlspecialchars_decode($pref_qualifications);

$min_qualifications = Security::HtmlEntitiesEncode($project->getMinQualifications());
if ($min_qualifications == '')
	$min_qualifications = "None Listed";
// decode rich html saved from rich text
$min_qualifications = htmlspecialchars_decode($min_qualifications);

$motivation = Security::HtmlEntitiesEncode($project->getMotivation());
// decode rich html saved from rich text
$motivation = htmlspecialchars_decode($motivation);

$description = Security::HtmlEntitiesEncode($project->getDescription());
// decode rich html saved from rich text
$description = htmlspecialchars_decode($description);

$objectives = Security::HtmlEntitiesEncode($project->getObjectives());
// decode rich html saved from rich text
$objectives = htmlspecialchars_decode($objectives);

$nda = $project->getNdaIp()->getName();
$compensation = $project->getCompensation()->getName();
$images = $project->getImages();
$is_hidden = $project->getIsHidden();

$comments = Security::HtmlEntitiesEncode($project->getProposerComments());
// decode rich html saved from rich text
$comments = htmlspecialchars_decode($comments);

$name = Security::HtmlEntitiesEncode($project->getProposer()->getFirstName()) 
	. ' ' 
	. Security::HtmlEntitiesEncode($project->getProposer()->getLastName());
$numberGroups = $project->getNumberGroups();
$preexistingKeywords = $keywordsDao->getKeywordsForEntity($pid);
$preexistingCategories = $categoriesDao->getCategoriesForEntity($pid);
//$preexistingPreferredCourses = $preferredCoursesDao->getPreferredCoursesForEntity($pid);
global $image_dir;
$image = false;
$images = $project->getImages();
if($images) {
	foreach($images as $i) {
		if($i->getIsDefault()){
			$image = $i->getId();
			break;
		}
	}
}
if (!$image) {
	$image = 'assets/img/capstone_test.jpg';
//	$image = $image_dir . 'assets/img/capstone_test.jpg';
} else {
	$image = "images/$image";
//	$image = $image_dir . "images/$image";
}

/*
if(!@getimagesize($image)){
	$image = $image_dir . 'assets/img/capstone_test.jpg';
}
*/

?>
<div class="viewSingleProject">
    <input type="hidden" id="projectId" value="<?php echo $project->getId(); ?>" />
    <input type="hidden" id="userId" value="<?php echo $userId; ?>" />

	  <!-- Header -->
	  <div class="bg-primary py-5 mb-5">
	    <div class="container h-100">
	      <div class="row h-100 align-items-center">
	        <div class="col-lg-12">
	          <h1 class="display-4 text-white mt-5 mb-2"><?php echo($title);?></h1>
	          <p class="lead mb-5"><?php echo nl2br($description);?></p>
	        </div>
	      </div>
	    </div>
</div>

	<!-- Page Content -->
	<div class="container">
	    <div class="row">
	      <div class="col-md-8 mb-5">
	        <h2>Objectives</h2>
	        <hr>
	        <p><?php echo nl2br($objectives);?></p>
					<h2>Motivations</h2>
				 	<hr>
				 	<p><?php echo nl2br($motivation);?></p>
					<h2>Qualifications</h2>
				 	<hr>
					<strong>Minimum Qualifications:</strong>
 				 	<br><?php echo nl2br($min_qualifications);?>
 				 	<p></p>
					<strong>Preferred Qualifications:</strong>
					<br><?php echo nl2br($pref_qualifications);?>
					<p></p>
                    <br>

					<?php
/*					if (count($preexistingPreferredCourses) >= 1){		
						echo"
						<address>
							<h2>Preferred Courses Completed:</h2>
							<br>		
							";
							foreach ($preexistingPreferredCourses as $p) {
								if (trim(Security::HtmlEntitiesEncode($p->getName())) != '') {
									echo '' . Security::HtmlEntitiesEncode($p->getCode()) . ' ' . Security::HtmlEntitiesEncode($p->getName()) . '<br>';
								}
							}
						echo"	
							<br>
						</address>";
					}
*/					?>

					
                    
					<?php 
/* Removed application display information
                    if ($isLoggedIn){
						$applications = $applicationsDao->getAllApplicationsForUserAndProject($userId, $pid);
						if (count($applications) > 0){
							foreach ($applications as $app) {
								$appID = $app->getId();
								$status = $app->getStatus()->getName();
							}
							if ($status !== 'Started'){
							echo"
								<a href='pages/editApplication.php?id=$appID'>
								<button class='btn btn-lg btn-outline-primary capstone-nav-btn' type='button'>
									View Submitted Application
								</button>
								</a>
							
							";
							}
							else {
								echo"
								<a href='pages/editApplication.php?id=$appID'>
								<button class='btn btn-lg btn-outline-primary capstone-nav-btn' type='button'>
									Edit Application
								</button>
								</a>
							
							";
							}
						}
						else {
							echo'
							<button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button" data-toggle="modal" 
								data-target="#newApplicationModal" id="openNewApplicationModalBtn">
								Apply For This Project &raquo
							</button>';
						}
                  
					}
*/
                	?>

					<?php
                    //Generate admin interface for admins.
                    if ($isAdmin) {
						echo'<br><br>';
						$users = $usersDao->getActiveUsers();
						$logs = $dao->getCapstoneProjectLogs($project->getId());
						$editors = $dao->getCapstoneProjectEditors($project->getId());
						renderAdminReviewPanel($project, $logs, $editors, $categoriesDao, $users, true);
                    }
					?>
	      </div>

	      <div class="col-md-4 mb-5">
	        <h2>Details</h2>
	        <hr>
					<address>
					<strong>Project Partner:</strong>
					<p><?php echo($name);?></p>
			</address>
			<?php
			if ($type == 'Class Projects'){
			echo"
			<address>
				<strong>NDA/IPA:</strong>
				<p>$nda</p>
			</address>
			<address>
				<strong>Number Groups:</strong>
				<p>$numberGroups</p>
			</address>
			<address>
				<strong>Project Status:</strong>
				<p>$status</p>
	        </address>
			";
			}
			?>
	        <?php 
			if ($type != 'Class Projects')
			echo "<address>
	          <strong>Start Date:</strong>
	          <br>$start_by<br>
	        </address>";
	
			if ($type != 'Class Projects')
			echo "<address>
						<strong>End Date:</strong>
	          <br>$complete_by
	          <br>
	        </address>";

			if ($website !== ''){
			echo"		<address>
						<strong>Website:</strong>
	          <br><a href='$website' target='_blank'>$website</a>
	          <br>
			  </address>";
			}
			if ($video !== ''){
			echo"  <address>
						<strong>Video:</strong>
	          <br><a href='$video' target='_blank'>$video</a>
	          <br>
			</address>";
			}
			if ($type != 'Class Projects') {
			echo "<address>
			<strong>Compensation:</strong>
	          <br>$compensation
	          <br>
			</address>";
			}

/*Removed while integrating
			if (count($preexistingCategories) > 1){		
				echo"
				<address>
					<strong>Course Types:</strong>
					<br>		
					";
							foreach ($preexistingCategories as $c) {
								if (trim(Security::HtmlEntitiesEncode($c->getName())) != '') {
									echo '' . Security::HtmlEntitiesEncode($c->getName()) . '<br>';
								}
							}
				echo"	
					<br>
				</address>";
			}
*/			
			if (count($preexistingKeywords) > 1){		
			echo"
			<address>
				<strong>Keywords:</strong>
				<br>		
				";
					
				
						foreach ($preexistingKeywords as $k) {
							if (trim(Security::HtmlEntitiesEncode($k->getName())) != '') {
								echo '<span class="badge badge-light keywordBadge">' . Security::HtmlEntitiesEncode($k->getName()) . '</span>';
							}
						}
					
			echo"	
				<br>
			</address>";
			}
			?>
			<address>
				<img class='card-img-top' id='projectImg' src='<?php echo $image; ?>' alt='Card Image Capstone' />
				<br>
			</address>




	      </div>
	    </div>
	</div>
<?php 
// Create Application Functionality
include_once PUBLIC_FILES . '/modules/newApplicationModal.php';
renderNewApplicationModal($project);
?>

</div>

<script type="text/javascript">
/**
 * Event handler for creating a new application based on user input into the modal
 */
function onCreateApplicationClick() {
    let body = {
        action: 'createApplication',
        projectId: $('#projectId').val(),
        uid: $('#userId').val()
    };

    api.post('/./applications.php', body).then(res => {
        window.location.replace('pages/editApplication.php?id=' + res.content.id);
    }).catch( err=> {
        snackbar(err.message, 'error');
    });
}
$('#createApplicationBtn').on('click', onCreateApplicationClick);


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

<?
include_once PUBLIC_FILES . '/modules/footer.php'; 
?>
