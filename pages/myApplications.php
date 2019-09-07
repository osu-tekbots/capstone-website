<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use DataAccess\CapstoneApplicationsDao;
use DataAccess\UsersDao;
use Model\UserType;
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
    $projects = $projectsDao->getCapstoneProjectsForUser($uId);
    if ($projects) {
        foreach ($projects as $p) {
            $pid = $p->getId();
            $projectApplications = $applicationsDao->getAllApplicationsForProject($pid, true);
            $submittedApplications[$pid] = $projectApplications;
        }
    }
    if ($isAdmin) {
        $userApplications = $applicationsDao->getAllApplicationsForUser($uId);
    }
} else {
    $userApplications = $applicationsDao->getAllApplicationsForUser($uId);
}

$title = 'My Applications';
include_once PUBLIC_FILES . '/modules/header.php';
include_once PUBLIC_FILES . '/modules/applications.php';

?>

<div class="container-fluid">
    <br><br>
    <h1>My Applications</h1>

    <div class="row">
        <div class="col">
            <?php
            if ($isProposer || $isAdmin) {
                if (!$projects || count($projects) == 0) {
                    echo "<p>You don't have any projects for students to apply for.</p>";
                } else {
                    if ($isAdmin) {
                        echo '<h2>Applications for Review</h2>';
                    }
                    foreach ($projects as $project) {
                        echo '<h3>' . Security::HtmlEntitiesEncode($project->getTitle()) . '</h3>';
                        renderApplicationTable($submittedApplications[$project->getId()], true);
                    }
                }
                if ($isAdmin) {
                    echo '<h2>My Applications in Progress</h2>';
                    renderApplicationTable($userApplications, false);
                }
            } else {
                renderApplicationTable($userApplications, false);
            }
            ?>
        </div>
    </div>
</div>

<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>