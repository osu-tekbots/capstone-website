<?php

$numOfCardsCreated = 0;

/****************************************************************************************
* Function Name: createCardGroup()
* Authors: Symon Ramos and Thien Nam
* Date: 2/28/19
* Description: Creates the project cards in the My Projects and Browse Projects pages.
* createNavBarBtn is in ./navbar.php - createDeleteBtn is in this file
*****************************************************************************************/
function createCardGroup($isMyProjectsPage, $isAdminProjectsPage){
	//$hasEditButton is false when called in browseProjects.php
	//$hasEditButton is true when called in myProjects.php

	global $numOfCardsCreated;

	if($isMyProjectsPage){
		$result = getMyProjects();
		$publishedOnly = false;
	}
	else if ($isAdminProjectsPage){
		$result = getAllProjects();
		$publishedOnly = false;
	}
	else{
		$result = getAllProjects();
		//Only show published projects in Browse Projects.
		$publishedOnly = true;
	}

	while($row = $result->fetch_assoc()){
		$id = $row['project_id'];
		$title = $row['title'];
		//Limit length of title to XX characters for the cards.
		$title = strlen($title) > 60 ? substr($title,0,60)."..." : $title;

		$description = ($row['description'] != NULL ? $row['description'] : '');
		//Limit length of description to XX characters for the cards.
		$description = strlen($description) > 90 ? substr($description,0,90)."..." : $description;

		$status = $row['status'];
		$category = $row['category'];
		$nda = $row['NDA/IP'];
		if($nda == "NDA Required" || $nda == "NDA/IP Required"){
			$nda = "NDA/IP Required";
		}
		else{
			$nda = "";
		}

		//The extra string contains the small text. Project type, year,
		//status, and whether or not the project has an NDA/IP are all
		//displayed here.
		$extra = ($row['year'] != NULL ? $row['type'] . " " . $row['year'] : '');
		if ($isMyProjectsPage || $isAdminProjectsPage){
			$extra .= '<br> Status: ' . $row['status'];
		}
		$extra .= ' ' . '<h6>' . $nda . '</h6>';

		$keywords = explode(",", $row['keywords']);

		foreach($keywords as $key){
			if($key != ' ' && strlen($extra) < 400){
				$extra .= '<span class="badge badge-light keywordBadge">' . $key . '</span>';
			}
		}

		$image = $row['image'] != NULL ? $row['image'] : "capstone.jpg";

		$lastUpdated = '<br> Last Updated: ' . $row['last_updated'];

		//Omit any projects that are specified to be hidden.
		if($isAdminProjectsPage){
			if($row['status'] == 'Pending' || $row['status'] == 'Published' || $row['status'] == 'Denied'){
				createProjectCard($id, $title, $description, $extra, $image, $status, $category, $isMyProjectsPage, $isAdminProjectsPage, $lastUpdated);
				$numOfCardsCreated++;
			}
		}
		else if($publishedOnly && $row['status'] == 'Published' && $row['is_hidden'] == 0){
			//For the Browse Projects page.
			createProjectCard($id, $title, $description, $extra, $image, $status, $category, $isMyProjectsPage, $isAdminProjectsPage, $lastUpdated);
			$numOfCardsCreated++;
		}
		else if(!$publishedOnly){
			//For the My Projects page.
			createProjectCard($id, $title, $description, $extra, $image, $status, $category, $isMyProjectsPage, $isAdminProjectsPage, $lastUpdated);
			$numOfCardsCreated++;
		}

	}
}

/****************************************************************************************
* Function Name: createProjectCard()
* Authors: Symon Ramos and Thien Nam
* Date: 2/28/19
* Description: Creates the singular project card in the My Projects and Browse Projects pages.
*****************************************************************************************/
function createProjectCard($id, $title, $description, $extra, $image, $status, $category, $isMyProjectsPage, $isAdminProjectsPage, $lastUpdated){
	global $numOfCardsCreated;
	echo '
			<div class="card capstoneCard my-3" id="projectCard' . $numOfCardsCreated . '">
				<a href="./viewSingleProject.php?id=' . $id . '" target="_blank" style="color:black">
					<img class="card-img-top" id="projectImg' . $id . '" src="../images/' . $image . '" alt="Card image cap">
				</a>
				<div class="card-body" id="projectCardBody' . $numOfCardsCreated . '">
				  <h6>' . $title . '</h6>'
				  . $description .
				  '<br>
				  <small class="text-muted">' . $extra . '</small>
				  <div style=\'float:right;margin-right:10px;\'>';

	if($isAdminProjectsPage){
		if ($status == "Pending" && $category == ""){
			echo "<h6><p style='color:red'>Awaiting Approval | Needs Category Placement</p></h6>";
		}
		else if ($status == "Pending"){
			echo "<h6><p style='color:red'>Awaiting Approval</p></h6>";
		}
		else if ($category == "" && $status != "Denied"){
			echo "<h6><p style='color:red'>Needs Category Placement</p></h6>";
		}
	}

	createNavBarBtn("./viewSingleProject.php?id=" . $id, "View");

	if($isMyProjectsPage || $isAdminProjectsPage){
		createNavBarBtn("./editProject.php?id=" . $id, "Edit");
		createDeleteBtn($title, $id, $numOfCardsCreated);
	}


	echo '<small id="small' . $id . '" class="text-muted lastUpdatedSmall">' . $lastUpdated . '</small>';
	echo '
				  </div>
				</div>
				<br>
			</div>
	';
	echo '
		<script type="text/javascript">
			$(document).ready(function(){
			  $("#projectCard' . $id . '").hover(function(){
					$(this).css("background-color", "#f8f9fa");
					//$("#projectImg' . $id . '").css("width", "100%");
					//$("#projectImg' . $id . '").css("height", "100%");
					$("#projectImg' . $id . '").css("transition", "all .2s ease-in-out");

				}, function(){
					$(this).css("background-color", "white");
					//$("#projectImg' . $id . '").css("width", "50%");
			  });
			});
		</script>
	';
}

function createRelatedProjectCard($id, $title, $description, $extra, $image){
	global $numOfCardsCreated;
	echo '
			<div class="card capstoneCard my-3" id="projectCard' . $numOfCardsCreated . '">
				<a href="./viewSingleProject.php?id=' . $id . '" target="_blank" style="color:black">
					<img class="card-img-top" id="projectImg' . $id . '" src="../images/' . $image . '" alt="Card image cap">
				</a>
				<div class="card-body" id="projectCardBody' . $numOfCardsCreated . '">
				  <h6>' . $title . '</h6>'
				  . $description .
				  '<br>
				  <small class="text-muted">' . $extra . '</small>
				  <div style=\'float:right;\'>';

	createNavBarBtn("./viewSingleProject.php?id=" . $id, "View");
// If  current UserID is equal to proposer_id for project
	//if(){
		//createNavBarBtn("./editProject.php?id=" . $id, "Edit");
	//}
	echo '
					</div>
				</div>
				<br>
			</div>
	';

}



function createDeleteBtn($title, $projectID, $numOfCardsCreated){
	// Delete Project Btn CSS is display none in capstone.css
	echo '<button class="btn btn-outline-danger deleteProjectBtn" id="deleteProjectBtn'.$projectID.'" style="display:none" type="button">Delete</button>';
	echo '<script type="text/javascript">
		$("#deleteProjectBtn'.$projectID.'").on("click", function(){
			projectID = '.$projectID.';
			$.ajax({
				type: "POST",
				url: "../db/dbManager.php",
				dataType: "html",
				data: {
						projectID: projectID,
						action: "deleteProject"},
						success: function(result)
						{
							$("#projectCard' . $numOfCardsCreated . '").remove();
							$("#deleteText").append("'.$title.'");
							$("#deleteText").css("display", "block");
						},
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(xhr.responseText);
							alert(thrownError);
				}
		});
	});







	</script>';

}



function adminCreateCardGroup(){
	//$hasEditButton is false when called in browseProjects.php
	//$hasEditButton is true when called in myProjects.php

	global $numOfCardsCreated;
	$result = getAdminProjects();
	$publishedOnly = false;


	while($row = $result->fetch_assoc()){
		$id = $row['project_id'];
		$title = $row['title'];
		$category = $row['category'];
		$status = $row['status'];
		$title = strlen($title) > 24 ? substr($title,0,24)."..." : $title;
		$description = ($row['description'] != NULL ? $row['description'] : '');
		//Limit length of description to 70 characters for the cards.
		$description = strlen($description) > 70 ? substr($description,0,70)."..." : $description;


		$status = $row['status'];
		$nda = $row['NDA/IP'];
		if($nda == "NDA Required" || $nda == "NDA/IP Required"){
			$nda = "NDA/IP Required";
		}
		else{
			$nda = "";
		}

		$extra = ($row['year'] != NULL ? $row['type'] . " " . $row['year'] : '');
		$extra .= '<br> Status: ' . $row['status'];
		$extra .= ' ' . '<h6>' . $nda . '</h6>';



		$keywords = explode(",", $row['keywords']);

		foreach($keywords as $key){
			if($key != ' ' && strlen($extra) < 400){
				$extra .= '<span class="badge badge-light keywordBadge">' . $key . '</span>';
			}
		}

		$lastUpdated = '<br> Last Updated: ' . $row['last_updated'];

		$image = $row['image'] != NULL ? $row['image'] : "capstone.jpg";


		adminCreateProjectCard($id, $title, $description, $extra, $image, $category, $status, $lastUpdated);
		$numOfCardsCreated++;


	}
}

function adminCreateProjectCard($id, $title, $description, $extra, $image, $category, $status, $lastUpdated){
	global $numOfCardsCreated;
	echo '
			<div class="card capstoneCardAdmin my-3" id="projectCard' . $numOfCardsCreated . '">
				<a href="./viewSingleProject.php?id=' . $id . '" target="_blank" style="color:black">
					<img class="card-img-top" id="projectImg' . $id . '" src="../images/' . $image . '" alt="Card image cap">
				</a>
				<div class="card-body" id="projectCardBody' . $numOfCardsCreated . '">
				  <h6>' . $title . '</h6>'
				  . $description .
				  '<br>
				  <small class="text-muted">' . $extra . '</small>
				  <div style=\'float:right;\'>
					';
	if ($status == "Pending" && $category == ""){
		echo "<h6><p style='color:red'>Awaiting Approval | Needs Category Placement</p></h6>";
	}
	else if ($status == "Pending"){
		echo "<h6><p style='color:red'>Awaiting Approval</p></h6>";
	}
	else if ($category == "" && $status != "Denied"){
		echo "<h6><p style='color:red'>Needs Category Placement</p></h6>";
	}

	createNavBarBtn("./viewSingleProject.php?id=" . $id, "View");
	createNavBarBtn("./editProject.php?id=" . $id, "Edit");
	echo '<small id="small' . $id . '" class="text-muted lastUpdatedSmall">' . $lastUpdated . '</small>';
	echo '
				  </div>
				</div>
				<br>
			</div>
	';
	echo '
		<script type="text/javascript">
			$(document).ready(function(){
			  $("#projectCard' . $id . '").hover(function(){
					$(this).css("background-color", "#f8f9fa");
					//$("#projectImg' . $id . '").css("width", "100%");
					//$("#projectImg' . $id . '").css("height", "100%");
					$("#projectImg' . $id . '").css("transition", "all .2s ease-in-out");

				}, function(){
					$(this).css("background-color", "white");
					//$("#projectImg' . $id . '").css("width", "50%");
			  });
			});
		</script>
	';
}


?>
