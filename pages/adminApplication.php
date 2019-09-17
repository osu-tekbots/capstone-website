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

	<!-- PAGES FOLDER DROP DOWN ON SIDE BAR
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fas fa-fw fa-folder"></i>
			<span>Pages</span>
		</a>
		<div class="dropdown-menu" aria-labelledby="pagesDropdown">
			<h6 class="dropdown-header">Login Screens:</h6>
			<a class="dropdown-item" href="login.html">Login</a>
			<a class="dropdown-item" href="register.html">Register</a>
			<a class="dropdown-item" href="forgot-password.html">Forgot Password</a>
			<div class="dropdown-divider"></div>
			<h6 class="dropdown-header">Other Pages:</h6>
			<a class="dropdown-item" href="404.html">404 Page</a>
			<a class="dropdown-item" href="blank.html">Blank Page</a>
		</div>
	</li>
					-->

	<li class="nav-item">
		<a class="nav-link" href="pages/adminProject.php">
			<i class="fas fa-fw fa-chart-area"></i>
			<span>Projects</span></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="pages/adminUser.php">
			<i class="fas fa-fw fa-table"></i>
			<span>Users</span></a>
	</li>
	<li class="nav-item active">
		<a class="nav-link" href="pages/adminApplication.php">
			<i class="fas fa-fw fa-file-invoice"></i>
			<span>Applications</span></a>
	</li>
</ul>

<div class="container-fluid">
	<br>

    <div class="row">
        <div class="col">
			<button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button" id="sendProposerReminderBtn">
			Send Reminder Emails
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

</script>