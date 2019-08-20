<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use DataAccess\KeywordsDao;
use Util\Security;

include PUBLIC_FILES . '/lib/shared/authorize.php';

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
$keywordsDao = new KeywordsDao($dbConn, $logger);
$project = $dao->getCapstoneProject($pid);
$proposer = $project->getProposerId();

// Check if project is hidden or not approved or if the project is the project is the proposers
allowIf( ($project && !($project->getIsHidden() && !$isAdmin)) || ($project && ($proposer == $userId) ));

$title = Security::HtmlEntitiesEncode($project->getTitle());
$status = $project->getStatus()->getName();
$type = $project->getType()->getName();
$year = $project->getDateCreated()->format('Y');
$website = $project->getWebsiteLink();
$video = $project->getVideoLink();
$start_by = $project->getDateStart()->format('F j, Y');
$complete_by = $project->getDateEnd()->format('F j, Y');
$pref_qualifications = Security::HtmlEntitiesEncode($project->getPreferredQualifications());
if ($pref_qualifications == '')
	$pref_qualifications = "None Listed";
$min_qualifications = Security::HtmlEntitiesEncode($project->getMinQualifications());
if ($min_qualifications == '')
	$min_qualifications = "None Listed";
$motivation = Security::HtmlEntitiesEncode($project->getMotivation());
$description = Security::HtmlEntitiesEncode($project->getDescription());
$objectives = Security::HtmlEntitiesEncode($project->getObjectives());
$nda = $project->getNdaIp()->getName();
$compensation = $project->getCompensation()->getName();
$images = $project->getImages();
$is_hidden = $project->getIsHidden();
$category = $project->getCategory()->getName();
$comments = Security::HtmlEntitiesEncode($project->getProposerComments());
$name = Security::HtmlEntitiesEncode($project->getProposer()->getFirstName()) 
	. ' ' 
	. Security::HtmlEntitiesEncode($project->getProposer()->getLastName());

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
	          <p class="lead mb-5"><?php echo($description);?></p>
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
                    if ($isLoggedIn && ($proposer != $userId)): 
                    ?>

                    <button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button" data-toggle="modal" 
                        data-target="#newApplicationModal" id="openNewApplicationModalBtn">
                        Apply For This Project &raquo
                    </button>

                    <?php
                    endif;
                    ?>

					<?php
                    //Generate admin interface for admins.
                    if ($isAdmin) {
                        $categories = $dao->getCapstoneProjectCategories();
                        renderAdminReviewPanel($project, $categories);
                    }
					?>
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
	        <?php 
			if ($type != 'Capstone')
			echo "<address>
	          <strong>Start Date:</strong>
	          <br>$start_by<br>
	        </address>";
			?>
	        <?php 
			if ($type != 'Capstone')
			echo "<address>
						<strong>End Date:</strong>
	          <br>$complete_by
	          <br>
	        </address>";
			?>
					<address>
						<strong>Website:</strong>
	          <br><a href="<?php echo($website);?>" target="_blank"><?php echo($website);?></a>
	          <br>
						<strong>Video:</strong>
	          <br><a href="<?php echo($video);?>" target="_blank"><?php echo($video);?></a>
	          <br>
	        </address>
			<?php 
			if ($type != 'Capstone')
			echo "<address>
			<strong>Compensation:</strong>
	          <br>$compensation)
	          <br>
	        </address>";
			?>
					<address>
						<strong>Keywords:</strong>
						<br>		
						<?php
							$preexistingKeywords = $keywordsDao->getKeywordsForEntity($pid);
							if($preexistingKeywords){
								foreach ($preexistingKeywords as $k) {
									if (trim($k->getName()) != '') {
										echo '<span class="badge badge-light keywordBadge">' . $k->getName() . '</span>';
									}
								}
							}
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
			
			<?php 
			/*
			<h2>Related Projects</h2>

			<!-- related_cards is a class used in the javascript below to interface
			     this section with an open source library called slick, which allows
				 for a slideshow-like display.
			-->
			<div class="related_cards">
				<?php
				$numberOfRelatedProjects = 0;
				//Create Related Project section.
				foreach ($keywords as $key) {
				    $result = getRelatedProjects($key, $projectID);
				    $rowcount = mysqli_num_rows($result);
				    while ($row = $result->fetch_assoc()) {
				        $id = $row['project_id'];
				        $title = $row['title'];

				        //Limit length of title to XX characters for the cards.
				        $title = strlen($title) > 24 ? substr($title,0,24) . '...' : $title;

				        $description = ($row['description'] != NULL ? $row['description'] : '');
				        //Limit length of description to XX characters for the cards.
				        $description = strlen($description) > 70 ? substr($description,0,70) . '...' : $description;


				        $status = $row['status'];
				        $nda = $row['NDA/IP'];
				        if ($nda == 'NDA Required' || $nda == 'NDA/IP Required') {
				            $nda = 'NDA/IP Required';
				        } else {
				            $nda = '';
				        }

				        $extra = ($row['year'] != NULL ? $row['type'] . ' ' . $row['year'] : '');
				        $extra .= '<br> Status: ' . $row['status'];
				        $extra .= ' ' . '<h6>' . $nda . '</h6>';
				        $image = $row['image'] != NULL ? $row['image'] : 'capstone.jpg';

				        $relatedProjectKeywords = explode(',', $row['keywords']);

				        foreach ($relatedProjectKeywords as $relatedProjectKey) {
				            if ($relatedProjectKey != ' ' && strlen($extra) < 400) {
				                $extra .= '<span class="badge badge-light keywordBadge">' . $relatedProjectKey . '</span>';
				            }
				        }

				        //Generate the Project Cards in ./modules/createCards.php.
				        createRelatedProjectCard($id, $title, $description, $extra, $image);
				        $numberOfRelatedProjects++;
				    }
				    //Set the maximum number of related projects to be displayed in this section.
				    if ($numberOfRelatedProjects == 12) {
				        break;
				    }
				}
				?>
			</div>
			 */?>


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
