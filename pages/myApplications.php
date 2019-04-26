<?php
use DataAccess\CapstoneProjectsDao;
use DataAccess\CapstoneApplicationsDao;
use DataAccess\UsersDao;
use DataAccess\CapstoneApplicationReviewsDao;
use Model\UserType;

if (!session_id()) {
    session_start();
}

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

allowIf(isset($_SESSION['userID']) && $_SESSION['userID'] . '' != '');

$uId = $_SESSION['userID'];

$title = 'My Applications';
include_once PUBLIC_FILES . '/modules/header.php';

$usersDao = new UsersDao($dbConn, $logger);

$user = $usersDao->getUser($uId);

$isProposer = $user->getType()->getId() == UserType::PROPOSER;
$isAdmin = $user->getType()->getId() == UserType::ADMIN;

$applicationsDao = new CapstoneApplicationsDao($dbConn, $logger);
$appReviewsDao = new CapstoneApplicationReviewsDao($dbConn, $logger);

$userApplications = array();
$submittedApplications = array();

if ($isProposer || $isAdmin) {
    $projectsDao = new CapstoneProjectsDao($dbConn, $logger);
    $projects = $projectsDao->getCapstoneProjectsForUser($uId);
    foreach ($projects as $p) {
        $pid = $p->getId();
        $projectApplications = $applicationsDao->getAllApplicationForProject($pid, true);
        $submittedApplications[$pid] = $projectApplications;
    }
    if($isAdmin) {
        $userApplications = $applicationsDao->getAllApplicationsForUser($uId);
    }
} else {
    $userApplications = $applicationsDao->getAllApplicationsForUser($uId);
}

/**
 * Creates an application table in the user interface.
 * 
 * Note that multiple application tables can exist and this function is used in both the student and proposer
 * interfaces.
 *
 * @param \Model\CapstoneApplication[] $applications the applications to display in the table
 * @param boolean $isProposer indicates whether the table generator should account for the user being a proposer
 * @return void
 */
function createApplicationTable($applications, $isProposer) {
    global $appReviewsDao;

    echo '<div class="row"><div class="col">';

    if (count($applications) == 0) {
        if ($isProposer) {
            echo '<p>No applications have been submitted for this project</p>';
        } else {
            echo "<p>You don't have any applications yet</p>";
        }
        echo '</div></div>';
        return;
    }

    echo '<table class="table"><thead>';
	
    //Create table column headers based on the user's access level.
    if ($isProposer) {
        echo '<th>Application Name</th>';
        echo '<th>Applicant</th>';
        echo '<th>Reviewed?</th>';
        echo '<th>Interest Level</th>';
    } else {
        echo '<th>Project Name</th>';
        echo '<th>Status</th>';
    }

    echo '<th>Start Date</th>';
    echo '<th>Updated</th>';
    echo '<th></th>';
    echo '</thead>';
    echo '<tbody>';
	
    //Iterating through every single application associated with this specific project...
    foreach ($applications as $app) {
        $appID = $app->getId();
		
        //Gather relevant application review data.
        $appReview = $appReviewsDao->getApplicationReviewForApplication($appID);
        if ($appReview) {
            $interestLevel = $appReview->getInterestLevel()->getName();
        } else {
            $interestLevel = '';
        }
        
        //The interestLevel must be selected for an application to have been reviewed.
        $isReviewed = $interestLevel != '' ? 'Yes' : 'No';
        
        if ($isProposer) {
            $title = 'Application ' . $appID;
            //Display the name of the applicant for proposers.
            $name = $app->getStudent()->getFirstName() . ' ' . $app->getStudent()->getLastName();
        } else {
            //This will be the name of the project.
            $title = $app->getCapstoneProject()->getTitle();
            //This will show whether or not the student's application 
            //has been created or submitted.
            $status = $app->getStatus()->getName();
        }

        $format = 'm-d-Y h:i a';
        $dateUpdated = $app->getDateUpdated()->format($format);
        $dateApplied = $app->getDateSubmitted()->format($format);
            
        //Generate table rows for each application.
        echo '<tr>';
        echo '<td>' . $title . '</td>';
            
        if ($isProposer) {
            echo '<td>' . $name . '</td>';
            echo '<td>' . $isReviewed . '</td>';
            echo '<td>' . $interestLevel . '</td>';
        } else {
            echo '<td>' . $status . '</td>';
        }
            
        echo '
                    <td>' . $dateApplied . '</td>
                    <td>' . $dateUpdated . '</td>';
            
        echo '<td>';
            
        if ($isProposer) {
            echo '<a class="btn btn-outline-primary" href="pages/reviewApplication.php?id=' . $appID . '">Review</a>';
        }
        //Student view
        else {
            echo '<a class="btn btn-outline-success" href="pages/editApplication.php?id=' . $appID . '">Edit</a>';
        }
        echo '		
                    </td>
                </tr>
                ';
    }
		
    echo '</tbody></table></div></div>';
}
?>

<div class="container-fluid">
    <br><br>
    <h1>My Applications</h1>

    <div class="row">
        <div class="col">
            <?php
            if ($isProposer || $isAdmin) {
                if (count($projects) == 0) {
                    echo "<p>You don't have any projects for students to apply for.</p>";
                } else {
                    if($isAdmin) {
                        echo "<h2>Applications for Review</h2>";
                    }
                    foreach ($projects as $project) {
                        echo '<h3>' . $project->getTitle() . '</h3>';
                        createApplicationTable($submittedApplications[$project->getId()], true);
                    }
                }
                if ($isAdmin) {
                    echo '<h2>My Applications in Progress</h2>';
                    createApplicationTable($userApplications, false);
                }
            } else {
                createApplicationTable($userApplications, false);
            }
            ?>
        </div>
    </div>
</div>

<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>