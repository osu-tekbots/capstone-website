<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneApplicationsDao;
use DataAccess\CapstoneProjectsDao;
use DataAccess\UsersDao;
use Util\Security;

if (!session_id()) session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$isAdmin = isset($_SESSION['userID']) && !empty($_SESSION['userID']) 
	&& isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

allowIf($isAdmin);

$uId = $_SESSION['userID'];

$applicationsDao = new CapstoneApplicationsDao($dbConn, $logger);
$projectsDao = new CapstoneProjectsDao($dbConn, $logger);

$application_HTML = "";
$projects = $projectsDao->getCapstoneProjectsForAdmin($uId);
foreach ($projects as $p) {
    $pid = $p->getId();
    $projectApplications = $applicationsDao->getAllApplicationsForProject($pid, true);
   foreach ($projectApplications AS $a){
	   $cp_id = $p->getId();
	   $onid = $a->getStudent()->getOnid();
	   $rating = $a->getReviewInterestLevel()->getName();
	   $application_HTML .= "$cp_id, $onid, $rating\n";
   }
}

$title = 'Admin Application Control';
$css = array(
    'assets/css/sb-admin.css'
);
//include_once PUBLIC_FILES . '/modules/header.php';
//include_once PUBLIC_FILES . '/modules/applications.php';

header('Content-Type: application/csv');
header('Content-Disposition: attachement; filename="Applications.csv"');

echo "cp_id, onid, Rating\n";
echo $application_HTML;



?>

	
<?php 
//include_once PUBLIC_FILES . '/modules/footer.php'; 
?>
