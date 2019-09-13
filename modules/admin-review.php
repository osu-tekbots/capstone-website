<?php

/**
 * Renders the HTML for the panel that displays options for reviewing a capstone project to admins.
 *
 * @param \Model\CapstoneProject $project the project being reviewed
 * @param \Model\CapstoneProjectCategory[] $categories an array of the available project categories
 * @return void
 */
function renderAdminReviewPanel($project, $categories, $singleView) {

    $pId = $project->getId();
    $pStatusName = $project->getStatus()->getName();
    $pCategoryId = $project->getCategory()->getId();
    $pCategoryName = $project->getCategory()->getName();
    $pIsHidden = $project->getIsHidden();
    $pIsArchived = $project->getIsArchived();
    $pComments = $project->getProposerComments();
    $aComments = $project->getAdminComments();
    $tooltipPurge = "This will complete remove a project as well as all of its connections.  (Deletes all applications and images related to the project and then deletes the project itself).  Use with caution";

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
        $options .= "<option $selected value='$id'>$name</option>";
    }

    
    if ($singleView){
        $button = "
        <a href='pages/editProject.php?id=$pId'>
        <button class='btn btn-lg btn-primary admin-btn' type='button'  
            id='adminViewProjectBtn'>
            &laquo Edit Project
        </button>
        </a>";
    } else {
        $button = "
        <a href='pages/viewSingleProject.php?id=$pId'>
        <button class='btn btn-lg btn-primary admin-btn' type='button' 
            id='adminViewProjectBtn'>
            &laquo View Project
        </button>
        </a>";
    }

    echo "
    <br/>
    <div class='row'>
        <div class='col-sm border rounded border-dark' id='adminProjectStatusDiv'>
            <center><h4><p style='color: black;'>-- Admin Project Status Review --</p></h4></center>
            $actionsHtmlContent
            $visibility
            <h6><p style='color:red'>$isArchived</p></h6>
            $commentsHtml
            <h6><p style='color:black'>Current Project Status: $pStatusName</p></h6>
            <h6><p style='color:black'>Major Category: $pCategoryName</p></h6>
            <select class='form-control' id='projectCategorySelect' data-toggle='tooltip'
                data-placement='top' title=''>
                $options
            </select>
            <br/>
            <h6>Admin Comments (Only visible by admins)</h6>
            <textarea class='form-control input' id='projectAdminComments'>$aComments</textarea>
            <center>
                <button class='btn btn-lg btn-success admin-btn' type='button' 
                    id='adminApproveProjectBtn'>Approve Project</button>
                <button class='btn btn-lg btn-danger admin-btn' type='button' 
                    id='adminUnapproveProjectBtn'>Reject/Unapprove Project</button>
                <button class='btn btn-lg btn-outline-danger admin-btn' type='button' 
                id='adminMakeProjectArchivedBtn'>Archive Project</button>
                <br/>

                <button class='btn btn-lg btn-outline-danger admin-btn' type='button' 
                    id='adminMakeProjectPrivateBtn'>Make Project Private</button>
                <button class='btn btn-lg btn-outline-info admin-btn' type='button' 
                    id='adminMakeProjectNotPrivateBtn'>Make Project Public</button>
                    <button class='btn btn-lg btn-outline-danger admin-btn' type='button' 
                    data-toggle='tooltip' data-placement='bottom' title='$tooltipPurge' id='adminDeleteProjectBtn'>PURGE</button>
                <br/>
                $button
                <a href='pages/adminProject.php'>
                    <button class='btn btn-lg btn-primary admin-btn' type='button' 
                        id='adminReturnBtn'>Return &raquo</button>
                </a>
            </center>
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
