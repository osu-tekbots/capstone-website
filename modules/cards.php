<?php
use Util\Security;

include_once PUBLIC_FILES . '/modules/button.php';

// Keeps track of how many cards have been generated. This is available globally.
$numCardsCreated = 0;

/**
 * Renders the HTML for the card group that displays projects.
 * 
 * The projects passed to this render function will ALL be rendered. All projects that should not be rendered (e.g.
 * projects that are not published or should be hidden) should be filtered out before calling this function.
 *
 * @param \Model\CapstoneProject[] $projects the projects to display
 * @param boolean $browsing indicates whether the rendered cards are for browsing or viewing/editing user projects
 * @param boolean $showActions determines whether to show the actionable (Edit, Delete) buttons
 * @return void
 */
function renderProjectCardGroup($projects, $keywordsDao, $browsing = false) {
	global $numCardsCreated;
	global $image_dir;
	
	if(!$projects || count($projects) == 0) {
		return;
	}

    foreach ($projects as $p) {
        // Capture and format all of the variables we need before rendering the HTML
        $id = $p->getId();
        $title = Security::HtmlEntitiesEncode($p->getTitle());
        if (strlen($title) > 60) {
            // Restrict the title length
            $title = substr($title, 0, 60) . '...';
        }
        $description = Security::HtmlEntitiesEncode($p->getDescription());
        if (strlen($description) > 90) {
            // Restrict the description length
            $description = substr($description,0,90) . '...';
        }
        $status = $p->getStatus()->getName();
        $category = $p->getCategory()->getName();
        $nda = $p->getNdaIp()->getName();

        // The details string contains the small text for the project
        $details = $p->getType()->getName() . ' ' . $p->getDateStart()->format('Y') . '<br/>';
        if (!$browsing) {
            $details .= "Status: $status";
        }

/*	Disabling NDA display on card for look		
		if($nda == 'No Agreement Required'){
			$details .= "<h6>NDA: $nda</h6>";
		}
		//NDA is "NDA Required" or "NDA/IP Required"
		else{
			$details .= "<h6>$nda</h6>";
		}
*/
		$extra = '';
		$preexistingKeywords = $keywordsDao->getKeywordsForEntity($id);
		if($preexistingKeywords){
			foreach ($preexistingKeywords as $k) {
				if (trim($k->getName()) != '') {
					$extra .= '<span class="badge badge-light keywordBadge">' . $k->getName() . '</span>';
				}
			}
		}
		

		$image = false;
		$images = $p->getImages();
		if($images) {
			foreach($images as $i) {
				if($i->getIsDefault()){
					$image = $i->getId();
					break;
				}
			}
		}
		
        if (!$image) {
            $image = $image_dir . 'assets/img/capstone.jpg';
        } else {
            $image = $image_dir . "images/$image";
        }

        $dateUpdated = $p->getDateUpdated()->format('Y-m-d');
		$lastUpdated = "<br/>Last Updated: $dateUpdated";
		
		$published = !$p->getIsHidden();
		
		renderProjectCard($id, $title, $description, $details, $image, $status, $category, $lastUpdated, 
			$numCardsCreated, $browsing, $published, $extra);

        $numCardsCreated++;
    }
}

/**
 * Renders the HTML for the card group that displays projects.
 * 
 * The projects passed to this render function will ALL be rendered. All projects that should not be rendered (e.g.
 * projects that are not published or should be hidden) should be filtered out before calling this function.
 *
 * @param \Model\CapstoneProject[] $projects the projects to display
 * @param boolean $browsing indicates whether the rendered cards are for browsing or viewing/editing user projects
 * @param boolean $showActions determines whether to show the actionable (Edit, Delete) buttons
 * @return void
 */
function renderAdminProjectCardGroup($projects, $keywordsDao, $browsing = false) {
	global $numCardsCreated;
	global $image_dir;
	
	if(!$projects || count($projects) == 0) {
		return;
	}

    foreach ($projects as $p) {
        // Capture and format all of the variables we need before rendering the HTML
        $id = $p->getId();
        $title = Security::HtmlEntitiesEncode($p->getTitle());
        if (strlen($title) > 60) {
            // Restrict the title length
            $title = substr($title, 0, 60) . '...';
        }
        $description = Security::HtmlEntitiesEncode($p->getDescription());
        if (strlen($description) > 90) {
            // Restrict the description length
            $description = substr($description,0,90) . '...';
        }
        $status = $p->getStatus()->getName();
        $category = $p->getCategory()->getName();
		$nda = $p->getNdaIp()->getName();
		$archived = $p->getIsArchived();
		$CategoryName = $p->getCategory()->getName();
		$name = Security::HtmlEntitiesEncode($p->getProposer()->getFirstName()) 
		. ' ' 
		. Security::HtmlEntitiesEncode($p->getProposer()->getLastName());
		$proposerID = $p->getProposer()->getId();
		$proposerPhone = $p->getProposer()->getPhone();

		$info = '';
		$info .= "<p>Proposer: $name</p>";
		$info .= "<p>Proposer Number: $proposerPhone</p>";
		$info .= "<p>Proposer ID: $proposerID</p>";
		$info .= "<p>Project ID: $id</p>";

		$extra = '';
		// Set Extra Information for Admin Browse (If Archived, show that, if just a created project show nothing)
		if ($archived) {
			$extra = "Archived";
			$status = "";
		}
		else if ($status == "Created") {
			$extra = "";
		}
		else if ($CategoryName != 'Electrical Engineering' && $CategoryName != 'Computer Science' && $CategoryName != 'EECS'){
			$extra = "Category Placement";
		}

        // The details string contains the small text for the project
        $details = $p->getType()->getName() . ' ' . $p->getDateStart()->format('Y') . '<br/>';
        if (!$browsing) {
            $details .= "Status: $status";
        }
		
		if($nda == 'No Agreement Required'){
			$details .= "<h6>NDA: $nda</h6>";
		}
		//NDA is "NDA Required" or "NDA/IP Required"
		else{
			$details .= "<h6>$nda</h6>";
		}
		
		


		

		$image = false;
		$images = $p->getImages();
		if($images) {
			foreach($images as $i) {
				if($i->getIsDefault()){
					$image = $i->getId();
					break;
				}
			}
		}
		
		if (!$image) {
            $image = $image_dir . 'assets/img/capstone.jpg';
        } else {
            $image = $image_dir . "images/$image";
        }

        $dateUpdated = $p->getDateUpdated()->format('Y-m-d');
		$lastUpdated = "<br/>Last Updated: $dateUpdated";
		
		$published = !$p->getIsHidden();
		
		renderAdminProjectCard($id, $title, $description, $details, $image, $status, $category, $lastUpdated, 
			$numCardsCreated, $browsing, $published, $archived, $info, $extra);

        $numCardsCreated++;
    }
}

/**
 * Renders the HTML required for a project card.
 *
 * @param string $id the ID of the project
 * @param string $title the title of the project
 * @param string $description the description of the project
 * @param string $details the details for the project, including type and year
 * @param string $imageLink the link to the image to use for the project
 * @param string $status the current status of the project
 * @param string $category the category the project is associated with
 * @param string $lastUpdated the date the project was last updated
 * @param integer $num the index of this card globally (which number card is it)
 * @param boolean $browsing whether to hide or show the edit and delete buttons. A value of true hides.
 * @return void
 */
function renderProjectCard($id, $title, $description, $details, $imageLink, $status, $category, $lastUpdated, $num, $browsing, $published, $extra) {
    $statusColor = ($status == 'Awaiting Approval' || $status == 'Rejected') ? 'red' : 'inherit';
    $viewButton = $published ? createLinkButton("pages/viewSingleProject.php?id=$id", 'View') : '';
	$editButton = !$browsing ? createLinkButton("pages/editProject.php?id=$id", 'Edit') : '';
	$deleteButton = !$browsing ? createProjectDeleteButton($id, $num) : '';

    echo "
	<div class='card capstoneCard my-3' id='projectCard$num'>
		<a href='pages/viewSingleProject.php?id=$id' target='_blank' style='color: black'>
			<img class='card-img-top' id='projectImg$id' src='$imageLink' alt='Card Image Capstone' />
		</a>
		<div class='card-body' id='projectCardBody$num'>
			<h6>$title</h6>
			$description
			<br>
			<small class='text-muted'>$details</small>
			<div style='position: absolute; float: left; margin-right: 10px; bottom: 10px;'>";
				//<h6><p style='color: $statusColor'>$status</p></h6>
echo "			<small class='text-muted'>$extra</small><br>
				$viewButton
				$editButton
				$deleteButton
				<small id='small$id' class='text-muted lastUpdatedSmall'>$lastUpdated</small>
			</div>
		</div>
		<br/>
	</div>

	<script type='text/javascript'>
		$(document).ready(function() {
			$('#projectCard$id').hover(function() {
				$(this).css('background-color', '#f8f9fa');
				$('#projectImg$id').css('transition', 'all .2s ease-in-out');
			}, function() {
				$(this).css('background-color', 'white');
			});
		});
	</script>
	";
}


function renderAdminProjectCard($id, $title, $description, $details, $imageLink, $status, $category, $lastUpdated, $num, $browsing, $published, $archived, $info, $extra) {
	$statusColor = ($status == 'Pending Approval' || $status == 'Rejected') ? 'red' : (($status == 'Created' || $status == 'Incomplete') ? '#ffcc00' : 'inherit');
	$statusColorExtra = ($extra == 'Category Placement' || $status == 'Rejected') ? 'red' : (($extra == 'Archived') ? '#ffcc00' : 'inherit');
    $viewButton = $published ? createLinkButton("pages/viewSingleProject.php?id=$id", 'View') : '';
	$editButton = (!$browsing && !$archived) ? createLinkButton("pages/editProject.php?id=$id", 'Edit') : '';
	$deleteButton = (!$browsing && !$archived) ? createProjectDeleteButton($id, $num) : '';
	if ($status == 'Created') {
		$status = 'Not Yet Submitted';
	}

	//<small class='text-muted'>$extra</small><br> (Above $viewButton)
    echo "
	<div class='card capstoneCard my-3' id='projectCard$num'>
	";
	if (!$archived){
		echo "
		<a href='pages/viewSingleProject.php?id=$id' target='_blank' style='color: black'>
			<img class='card-img-top' id='projectImg$id' src='$imageLink' alt='Card Image Capstone' />
		</a>
		";
	}
	else {
		echo "
		<img class='card-img-top' id='projectImg$id' src='$imageLink' alt='Card Image Capstone' />
		";
	}
	echo "
		<div class='card-body' id='projectCardBody$num'>
			<h6>$title</h6>
			<small class='text-muted'>$details</small>
			<small class='text-muted'>$info</small>
			<div style='position: absolute; float: left; margin-right: 10px; bottom: 10px;'>
				<h6><p style='color: $statusColor'>$status</p></h6>
				<h6><p style='color: $statusColorExtra'>$extra</p></h6>
				$viewButton
				$editButton
				$deleteButton
				<small id='small$id' class='text-muted lastUpdatedSmall'>$lastUpdated</small>
			</div>
		</div>
		<br/>
	</div>

	<script type='text/javascript'>
		$(document).ready(function() {
			$('#projectCard$id').hover(function() {
				$(this).css('background-color', '#f8f9fa');
				$('#projectImg$id').css('transition', 'all .2s ease-in-out');
			}, function() {
				$(this).css('background-color', 'white');
			});
		});
	</script>
	";
}

/**
 * Renders the HTML for a project card that displays related project information.
 *
 * @param string $id the ID of the project
 * @param string $title the title of the project
 * @param string $description the description of the project
 * @param string $details the details for the project
 * @param string $imageLink the link to the image to display with the project
 * @return void
 */
function renderRelatedProjectCard($id, $title, $description, $details, $imageLink) {
	global $numCardsCreated;
	$viewButton = createLinkButton("pages/viewSingleProject.php?id=$id", 'View');

	echo "
	<div class='card capstoneCard my-3' id='projectCard$numCardsCreated'>
		<a href='pages/viewSingleProject.php?id=$id' target='_blank' style='color: black'>
			<img class='card-img-top' id='projectImg$id' src='$imageLink' alt='Card Image Capstone' />
		</a>
		<div class='card-body' id='projectCardBody$numCardsCreated'>
			<h6>$title</h6>
			$description
			<br>
			<small class='text-muted'>$details</small>
			<div style='float: right; margin-right: 10px;'>
				$viewButton
			</div>
		</div>
		<br/>
	</div>
	";
}

/**
 * Creates the HTML and associated JavaScript required for the project 'Delete' button functionality.
 *
 * @param string $title the title of the project
 * @param string $projectId
 * @param integer $cardNumber
 * @return void
 */
function createProjectDeleteButton($projectId, $cardNumber) {
	return "
	<button class='btn btn-outline-danger deleteProjectBtn' id='deleteProjectBtn$projectId' type='button'>
		Delete
	</button>
	
	<script type='text/javascript'>
		$('#deleteProjectBtn$projectId').on('click', function() {
			let res = confirm('You are about to delete a project. This action cannot be undone.');
			if(!res) return false;
			let projectId = '$projectId';
			let data = {
				action: 'archiveProject',
				id: projectId,
			};
			api.post('/projects.php', data).then(res => {
				$('#projectCard$cardNumber').remove();
				snackbar(res.message, 'success');
			}).catch(err => {
				snackbar(err.message, 'error');
			});
		});
	</script>
	";
}
