<?php

/**
 * Renders the HTML for the panel that displays options for reviewing a capstone project to admins.
 *
 * @param \Model\CapstoneProject $project the project being reviewed
 * @param \Model\CapstoneProjectCategory[] $categories an array of the available project categories
 * @return void
 */
function renderAdminReviewPanel($project, $categories) {

    $pId = $project->getId();
    $pStatusName = $project->getStatus()->getName();
    $pCategoryId = $project->getCategory()->getId();
    $pCategoryName = $project->getCategory()->getName();
    $pIsHidden = $project->getIsHidden();
    $pComments = $project->getProposerComments();

    $actions = array();
    if ($pStatusName == 'Awaiting Approval') {
        $actions[] = 'Project Review';
    }
    if ($pCategoryName == 'None') {
        $actions[] = 'Project Category Placement';
    }
    $visibility = $pIsHidden
					? 'Private Project (Not viewable on Browse Projects)' 
					: 'Public Project';
    $actionsHtmlContent = count($actions) > 0 
					? 'Acion Required: ' . implode(' and ', $actions)
					: 'No action required at this time';

    $commentsHtml = $pComments != '' 
					? "<h6><p style='color:red'>Proposer Comments: $pComments</p></h6>"
					: '';

    $options = '';
    foreach ($categories as $c) {
        $id = $c->getId();
        $name = $c->getName();
        $selected = $id == $pCategoryId ? 'selected' : '';
        $options .= "<option $selected value='$id'>$name</option>";
    }

    $viewButtonStyle = $pIsHidden ? 'display: none;' : '';

    echo "
    <br/>
    <div class='row'>
        <div class='col-sm-3'></div>
        <div class='col-sm-6 border rounded border-dark' id='adminProjectStatusDiv'>
            <center><h4><p style='color: black;'>-- Admin Project Status Review --</p></h4></center>
            <h6><p style='color:red'>$actionsHtmlContent</p></h6>
            <h6><p style='color:red'>$visibility</p></h6>
            $commentsHtml
            <h6><p style='color:black'>Current Project Status: $pStatusName</p></h6>
            <h6><p style='color:black'>Major Category: $pCategoryName</p></h6>
            <select class='form-control' id='projectCategorySelect' data-toggle='tooltip'
                data-placement='top' title=''>
                $options
            </select>
            <center>
                <a href='pages/viewSingleProject.php?id=$pId'>
                    <button class='btn btn-lg btn-primary admin-btn' type='button' style='$viewButtonStyle' 
                        id='adminViewProjectBtn'>
                        View Project &raquo
                    </button>
                </a>
                <button class='btn btn-lg btn-success admin-btn' type='button' 
                    id='adminApproveProjectBtn'>Approve Project</button>
                <button class='btn btn-lg btn-danger admin-btn' type='button' 
                    id='adminUnapproveProjectBtn'>Reject/Unapprove Project</button>
                <br/>
                <button class='btn btn-lg btn-outline-danger admin-btn' type='button' 
                    id='adminMakeProjectPrivateBtn'>Make Project Private</button>
                <button class='btn btn-lg btn-outline-info admin-btn' type='button' 
                    id='adminMakeProjectNotPrivateBtn'>Make Project Public</button>
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
        </div>
    </div>
    <div class='col-sm-3'></div>
    ";
}
