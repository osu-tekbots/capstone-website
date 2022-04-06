<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneApplicationsDao;
use DataAccess\CapstoneProjectsDao;
use DataAccess\UsersDao;
use Util\Security;

session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$isAdmin = isset($_SESSION['userID']) && !empty($_SESSION['userID']) 
	&& isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

allowIf($isAdmin);

$uId = $_SESSION['userID'];

$applicationsDao = new CapstoneApplicationsDao($dbConn, $logger);
$projectsDao = new CapstoneProjectsDao($dbConn, $logger);

$userApplications = array();
$submittedApplications = array();

$tooltipSendApplicationReminders = "This button will send out an email to all proposers who have pending unreviewed applications.  This process will take a while due to all the emails being sent out so please only press this once.";

$projects = $projectsDao->getCapstoneProjectsForAdmin($uId);
foreach ($projects as $p) {
    $pid = $p->getId();
    $projectApplications = $applicationsDao->getAllApplicationsForProject($pid, true);
    $submittedApplications[$pid] = $projectApplications;
}
$userApplications = $applicationsDao->getAllApplicationsForUser($uId);

$title = 'Admin Application Control';
$css = array(
    'assets/css/sb-admin.css'
);
include_once PUBLIC_FILES . '/modules/header.php';
include_once PUBLIC_FILES . '/modules/applications.php';

?>
<br/>
<div id="wrapper">
<!-- Sidebar -->
<ul class="sidebar navbar-nav">
	<li class="nav-item">
		<a class="nav-link" href="pages/adminInterface.php">
			<i class="fas fa-fw fa-tachometer-alt"></i>
			<span>Dashboard</span>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="pages/adminProject.php">
			<i class="fas fa-fw fa-chart-area"></i>
			<span>Active Projects</span></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="pages/adminProject.php?archive">
			<i class="fas fa-fw fa-chart-area"></i>
			<span>Archived Projects</span></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="pages/adminUser.php">
			<i class="fas fa-fw fa-table"></i>
			<span>Users</span></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="pages/adminApplication.php">
			<i class="fas fa-fw fa-file-invoice"></i>
			<span>Applications</span></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="pages/adminCourses.php">
			<i class="fas fa-fw fa-table"></i>
			<span>Course Listings</span></a>
	</li>
</ul>

<div class="container-fluid">
	<br>

    <div class="row">
        <div class="col">
			<button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button" id="sendProposerReminderBtn" data-toggle="tooltip" 
                data-placement="bottom" title="<?php echo $tooltipSendApplicationReminders?>">
			Send Reminder Emails
			</button>
			
			<a href='./pages/applicationsCSV.php'><button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button"  data-toggle="tooltip" 
                data-placement="bottom" title="This button will delete all current applications in the system. Use with caution.">
			Application CSV Summary
			</button></a>
			
           <button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button" id="clearApplicationsBtn" data-toggle="tooltip" 
                data-placement="bottom" title="This button will delete all current applications in the system. Use with caution.">
			Delete All Applications
			</button>			
			
            <?php
			if (count($projects) == 0) {
			    echo '<p>There are no published projects for students to apply for.</p>';
			} else {
			    echo '<h2>Applications for Review</h2>';
			    foreach ($projects as $project) {
					if (count($submittedApplications[$project->getId()]) > 0){
						
						$proposerName = Security::HtmlEntitiesEncode($project->getProposer()->getFirstName())
						. ' ' . Security::HtmlEntitiesEncode($project->getProposer()->getLastName());
						$proposerEmail = Security::HtmlEntitiesEncode($project->getProposer()->getEmail());
						$emailLink = "<a href='mailto: $proposerEmail'>$proposerName</a>";
						echo '<h3>' . Security::HtmlEntitiesEncode($project->getTitle()) . ' [ ' . $emailLink . ' ] ' . '</h3>';
						renderAdminApplicationTable($submittedApplications[$project->getId()]);
					}
			    }
			}
			echo '<h2>My Applications in Progress</h2>';
			renderApplicationTable($userApplications, false);
            ?>
        </div>
    </div>
</div>

	
<?php 
include_once PUBLIC_FILES . '/modules/footer.php'; 
?>

<script>
/**
 * Event handler for creating a new application based on user input into the modal
 */
function handleReviewApplicationReminder() {
    let body = {
        action: 'sendProposerApplicationReminders'
    };

    api.post('/./applications.php', body).then(res => {
        snackbar(res.message, 'success');
    }).catch( err=> {
        snackbar(err.message, 'error');
    });
}
$('#sendProposerReminderBtn').on('click', handleReviewApplicationReminder);


$('#clearApplicationsBtn').on('click', function() {
	let res = confirm('You are about to delete all applications. There is no going back.');
	if(!res) return false;
	let data = {
		action: 'clearApplications'
	};
	api.post('/applications.php', data).then(res => {
		snackbar(res.message, 'success');
	}).catch(err => {
		snackbar(err.message, 'error');
	});
});

</script>