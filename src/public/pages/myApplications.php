<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use DataAccess\CapstoneApplicationsDao;
use DataAccess\UsersDao;
use Model\UserType;
use Model\CapstoneProjectStatus;
use Util\Security;

if (!session_id()) {
    session_start();
}

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

allowIf(isset($_SESSION['userID']) && $_SESSION['userID'] . '' != '');

$uId = $_SESSION['userID'];

$usersDao = new UsersDao($dbConn, $logger);

$user = $usersDao->getUser($uId);

$isProposer = $user->getType()->getId() == UserType::PROPOSER;
$isAdmin = $user->getType()->getId() == UserType::ADMIN;

$applicationsDao = new CapstoneApplicationsDao($dbConn, $logger);

$userApplications = array();
$submittedApplications = array();

if ($isProposer || $isAdmin) {
    $projectsDao = new CapstoneProjectsDao($dbConn, $logger);
     $projects = $projectsDao->getActiveCapstoneProjectsForUser($uId);
   if ($projects) {
        foreach ($projects as $p) {
            $pid = $p->getId();
            if ($p->getIsArchived() == false){
				$projectStatus = $p->getStatus()->getId();
				$projectApplications = $applicationsDao->getAllApplicationsForProject($pid, true);
				$submittedApplications[$pid] = $projectApplications;
			}
        }
    }
    if ($isAdmin) {
        $userApplications = $applicationsDao->getAllApplicationsForUser($uId);
    }
} //else {
    $userApplications = $applicationsDao->getAllApplicationsForUser($uId);
//}

$title = 'My Applications';
include_once PUBLIC_FILES . '/modules/header.php';
include_once PUBLIC_FILES . '/modules/applications.php';

?>

<div class="container-fluid">
    <br><br>
    <div class="row">
        <div class="col">
            <?php
            if ($isProposer || $isAdmin) {
                echo '<h2>Applications for My Projects</h2>';
				if ($isAdmin) {    
                    renderApplicationTable($userApplications, false);
                }
				
				if (!$projects || count($projects) == 0) {
                    echo "<p>You don't have any projects for students to apply for.</p>";
                } else {
                    if ($isAdmin) {
                        echo '<h1>Applications for Review</h1>';
                    }
                    foreach ($projects as $project) {
                        $isProjectSubmitted = $project->getStatus()->getId() == CapstoneProjectStatus::ACCEPTING_APPLICANTS ? TRUE : FALSE;
                        if ($isProjectSubmitted){
                        echo '<h4>' . Security::HtmlEntitiesEncode($project->getTitle()) . '</h4>';
                        renderApplicationTable($submittedApplications[$project->getId()], true);
                        }
                        else {
                            echo '<h4>' . Security::HtmlEntitiesEncode($project->getTitle()) . '</h4>';
                            echo "<p>This project is not yet submitted.</p>";
                        }
                    }
                }
				
            } //else {
				echo '<h2>Applications I have submitted to projects</h2>';
                    
                renderApplicationTable($userApplications, false);
           // }
            ?>
        </div>
    </div>
</div>

<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>