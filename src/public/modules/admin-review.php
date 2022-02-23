<?php

/**
 * Renders the HTML for the panel that displays options for reviewing a capstone project to admins.
 *
 * @param \Model\CapstoneProject $project the project being reviewed
 * @param \Model\CapstoneProjectCategory[] $categories an array of the available project categories
 * @return void
 */
function renderAdminReviewPanel($project, $categories, $users, $singleView) {

    $pId = $project->getId();
    $pStatusName = $project->getStatus()->getName();
    $pCategoryId = $project->getCategory()->getId();
    $pProposerId = $project->getProposerId();
    $pProposer = $project->getProposer(); //This gets a type User
    $pCategoryName = $project->getCategory()->getName();
    $pIsHidden = $project->getIsHidden();
    $pIsArchived = $project->getIsArchived();
    $pComments = $project->getProposerComments();
    $aComments = $project->getAdminComments();
    $tooltipPurge = "This will completely remove a project as well as all of its connections and images.  (Deletes all applications and images related to the project and then deletes the project itself).  Use with caution not email generated to the proposer";
	$tooltipArchive = "This will archive this project making it not visible to students. It is also removed from many Admin lists reducing clutter.";
	$tooltipUnArchive = "This will Unarchive the project making it viewable by students and adding it to the current list of projects.";
	$tooltipApprove = "SENDS EMAIL: This will inform the proposer vai email thier project is published and available for student sto view.";
	$tooltipUnapprove = "SENDS EMAIL: This will unapprove a project and inform the propser. This is useful if the proposer wants to make changes to the project description.";
	$tooltipPublish = "This will reveal this project to a general search by students. Allows students to find and bid on project.";
	$tooltipunPublish = "This hides a project from students. Useful for project that are already filled with selected students or has an NDA concern.";
    $tooltipEdit = "This allows you to edit this project.";

    $usersHTML = "<select id='proposerSelect' class='form-control'>";
	foreach ($users as $u)
		$usersHTML .= "<option value='".$u->getId()."' ".($pProposerId == $u->getId() ? 'selected' : '' ).">" . $u->getLastname() . ", " . $u->getFirstname() . ": " .$u->getEmail(). "</option>";
	$usersHTML .= "</select>";
	
	$actions = array();
    if ($pStatusName == 'Pending Approval') {
        $actions[] = 'Project Review';
    }
    if ($pCategoryName == 'None') {
        $actions[] = 'Project Category Placement';
    }
    $visibility = $pIsHidden
					? '<h6><p style="color:red">Private Project (Not viewable on Browse Projects)</p></h6>' 
					: '<h6><p style="color:black">Public Project</p></h6>';
    $actionsHtmlContent = count($actions) > 0 
					? '<h6><p style="color:red">Action Required: ' . implode(' and ', $actions) . '</p></h6>'
                    : '<h6><p style="color:black">No action required at this time</p></h6>';

    $commentsHtml = $pComments != '' 
					? "<h6><p style='background-color:#f1a582'>Proposer Comments: $pComments</p></h6>"
                    : '';
    $isArchived = $pIsArchived
                    ? 'Archived Project (Not longer Active)'
                    : '';

    $options = '';
    foreach ($categories as $c) {
        $id = $c->getId();
        $name = $c->getName();
        $selected = $id == $pCategoryId ? 'selected' : '';
        // $options .= "<option $selected value='$id'>$name</option>";
        $options .= "<input type='checkbox' value='$id' checked='$selected'>$name<br>";
    }

    echo "
    <br/>
    <div class='row'>
        <div class='col-sm border rounded border-dark' id='adminProjectStatusDiv'>
            <center><h4><p style='color: black;'>-- Administrator Options --</p></h4></center>
            <div class='row'>
			    <div class='col-6'>
                    $actionsHtmlContent
                    $visibility
                    <h6><p style='color:red'>$isArchived</p></h6>
                    $commentsHtml
                    <h6><p style='color:black'>Current Project Status: $pStatusName</p></h6>
                    <h6><p style='color:black'>Major Categories: </p></h6>
                    <fieldset id='projectCategoryCheckBox'>  
                        $options
                        <br>  
                    </fieldset>

                </div>
                <div class='col-6'>
                    <h6><p style='color:black'>Project Proposer: $usersHTML</p></h6>
                </div>
			</div>
            <h6>Admin Comments (Only visible by admins)</h6>
            <textarea class='form-control input' id='projectAdminComments'>$aComments</textarea>
            <center>
                ";
	if ($pStatusName == 'Pending Approval')
		echo "<button class='btn btn-lg btn-success admin-btn' type='button' data-toggle='tooltip' data-placement='bottom' title='$tooltipApprove' id='adminApproveProjectBtn'>Approve Project</button>";
	else
		echo "<button class='btn btn-lg btn-danger admin-btn' type='button' data-toggle='tooltip' data-placement='bottom' title='$tooltipUnapprove' id='adminUnapproveProjectBtn'>Unapprove Project</button>";
                
	if ($isArchived == false)
		echo "<button class='btn btn-lg btn-warning admin-btn' type='button' data-toggle='tooltip' data-placement='bottom' title='$tooltipArchive' id='adminMakeProjectArchivedBtn'>Archive Project</button>";
	else
		echo "<button class='btn btn-lg btn-warning admin-btn' type='button' data-toggle='tooltip' data-placement='bottom' title='$tooltipUnarchive' id='adminUnarchiveProjectBtn'>Unarchive Project</button>";

	if ($pIsHidden == false)
		echo "<button class='btn btn-lg btn-outline-danger admin-btn' type='button' data-toggle='tooltip' data-placement='bottom' title='$tooltipunPublish' id='adminMakeProjectPrivateBtn'>Unpublish Project</button>";
	else
		echo "<button class='btn btn-lg btn-outline-info admin-btn' type='button' data-toggle='tooltip' data-placement='bottom' title='$tooltipPublish' id='adminMakeProjectNotPrivateBtn'>Publish Project</button>";	
               
                
    echo "<button class='btn btn-lg btn-outline-danger admin-btn' type='button' data-toggle='tooltip' data-placement='bottom' title='$tooltipPurge' id='adminDeleteProjectBtn'>PURGE</button>
                <br/>";
	if ($singleView == true)
		echo "<a href='pages/editProject.php?id=$pId'><button class='btn btn-lg btn-info admin-btn' type='button' data-toggle='tooltip' data-placement='bottom' title='$tooltipEdit' id='adminDeleteProjectBtn'>Edit Project</button></a>";
				
        echo"</center>
            <div id='approvedText' class='adminText' 
                style='color: green;'>Project Approved!</div>
            <div id='rejectedText' class='adminText' 
                style='color: red;'>Project Rejected!</div>
            <div id='privateText' class='adminText' 
                style='color: red;'>Project Now Private! (Will NOT show up in Browse Projects)</div>
            <div id='publicText' class='adminText' 
                style='color: blue;'>Project Now Public! (WILL show up in Browse Projects)</div>
            <div id='categoryText' class='adminText' 
                style='color: green;'>Category Changed!</div>
            <div id='archivedText' class='adminText' 
                style='color: blue;'>Project Archived! (No Longer Active)</div>
        </div>
    </div>

    ";
}



?>
