<?php
use Model\CapstoneInterestLevel;
use Util\Security;

/**
 * Renders the HTML for an application table in the user interface.
 * 
 * Note that multiple application tables can exist and this function is used in both the student and proposer
 * interfaces.
 *
 * @param \Model\CapstoneApplication[] $applications the applications to display in the table
 * @param boolean $isProposer indicates whether the table generator should account for the user being a proposer
 * @return void
 */
function renderApplicationTable($applications, $isProposer) {

    echo '<div class="row"><div class="col">';

    if (!$applications || count($applications) == 0) {
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
        $interestLevel = $isProposer ? $app->getReviewInterestLevel()->getName() : '';
        
        //The interestLevel must be selected for an application to have been reviewed.
        $isReviewed = $app->getReviewInterestLevel()->getId() != CapstoneInterestLevel::NOT_SPECIFIED ? 'Yes' : 'No';
        
        if ($isProposer) {
            //Display the name of the applicant for proposers.
            $name = Security::HtmlEntitiesEncode($app->getStudent()->getFirstName()) 
                . ' ' 
                . Security::HtmlEntitiesEncode($app->getStudent()->getLastName());
        } else {
            //This will be the name of the project.
            $title = Security::HtmlEntitiesEncode($app->getCapstoneProject()->getTitle());
            //This will show whether or not the student's application 
            //has been created or submitted.
            $status = $app->getStatus()->getName();
        }

        $format = 'm-d-Y h:i a';
        $dateUpdated = $app->getDateUpdated()->format($format);
        $dateApplied = $app->getDateSubmitted()->format($format);
            
        //Generate table rows for each application.
        echo '<tr>';
        if(!$isProposer) {   
            echo '<td>' . $title . '</td>';
        }
            
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