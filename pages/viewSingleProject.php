<?php
use DataAccess\CapstoneProjectsDao;

include PUBLIC_FILES . '/lib/shared/authorize.php';

$title = 'Single Project';
include_once PUBLIC_FILES . '/modules/header.php';

$pid = $_GET['id'];
allowIf($pid . '' != '');

$isAdmin = $_SESSION['accessLevel'] == 'Admin';
include_once PUBLIC_FILES . '/modules/admin-review.php';

$dao = new CapstoneProjectsDao($dbConn, $logger);
$project = $dao->getCapstoneProject($pid);

allowIf($project);

$title = $project->getTitle();
$type = $project->getType()->getName();
$status = $project->getStatus()->getName();
$type = $project->getType()->getName();
$year = $project->getDateCreated()->format('Y');
$website = $project->getWebsiteLink();
$video = $project->getVideoLink();
$start_by = $project->getDateStart()->format('F j, Y');
$complete_by = $project->getDateEnd()->format('F j, Y');
$pref_qualifications = $project->getPreferredQualifications();
$min_qualifications = $project->getMinQualifications();
$motivation = $project->getMotivation();
$description = $project->getDescription();
$objectives = $project->getObjectives();
$nda = $project->getNdaIp()->getName();
$compensation = $project->getCompensation()->getName();
$images = $project->getImages();
$is_hidden = $project->getIsHidden();
$category = $project->getCategory()->getName();
$comments = $project->getProposerComments();
$name = $project->getProposer()->getFirstName() . ' ' . $project->getProposer()->getLastName();

// TODO: keywords
//$keywords = explode(',', $row['keywords']);
$keywords = array();

?>
<div class="viewSingleProject">
	<input type="hidden" id="id" value="<?php echo $project->getId(); ?>" />

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
						if (array_key_exists('userID',$_SESSION) && $_SESSION['userID'] != '') {
						    //Future Implementation @3/19/19 Release
						    //We will be implementing student application functionality for the next release.
						    echo('<button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button" data-toggle="modal" data-target="#newApplicationModal" id="openNewApplicationModalBtn">Apply For This Project &raquo</button>');
						}
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
						$string=implode(',', $keywords);
						$string = trim($string,',');
						$string = rtrim($string,', ');
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
?>

</div>

<script type="text/javascript">
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
