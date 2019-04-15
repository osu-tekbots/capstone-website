<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once('../includes/header.php'); ?>
	<title> My Projects</title>
</head>

<?php require_once('../db/dbManager.php'); ?>
<?php require_once('../modules/createCards.php'); ?>
<?php require_once('../modules/redirect.php'); ?>

<body>
	<?php include_once("../modules/navbar.php"); ?>
	<br><br>
	<div class="container-fluid">
		<h1>My Projects</h1>
		<div class="row">

			<div class="col-sm-3">
			<?php
				//All access levels (Student, Proposer, Admin) can create projects.
				//Students and Admins can apply for projects.
				//Proposers by system design are unable to apply for projects.

				echo '<button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button" data-toggle="modal" data-target="#newProjectModal" id="openNewProjectModalBtn">Create New Project</button>';
				echo '<button class="btn btn-lg btn-outline-danger capstone-nav-btn" type="button" data-toggle="modal" id="toggleDeleteProjectBtn">Toggle Delete Project Button</button>';
			?>
			<div id="deleteText" class="adminText" style="color: red;">Project Deleted: </div>

			</div>

			<div class="col-sm-9 scroll jumbotron capstoneJumbotron">
				<div class="card-columns capstoneCardColumns" id="projectCardGroup">
					<!-- createCardGroup() is found in ../modules/createCards.php -->
					<?php
					$check_value = isset($_POST['deleteProjects']) ? 1 : 0;
					createCardGroup(true, false);

					?>
				</div>
			</div>

		</div>

	</div>
	<?php echo '<input id="proposerIDHeader" style="display:none;" value="' . $_SESSION['userID'] . '"></input>'; ?>

	<?php include "../modules/newProjectModal.php"; ?>
	<?php include_once("../modules/footer.php"); ?>

</body>

<script type="text/javascript">
	$('#toggleDeleteProjectBtn').on('click', function(){
		//Toggle visibility of the delete project button for each project card.
		//The deleteProjectBtn is dynamically generated in ./modules/createCards.php.
		$(".deleteProjectBtn").toggle();
	});

	$('#createProjectBtn').on('click', function(){
		title = $('#projectTitleInput').val();
		userID = $('#proposerIDHeader').val();
		$.ajax({
			type: 'POST',
			url: '../db/dbManager.php',
			dataType: 'html',
			data: {
					title : title,
					userID: userID,
					action: 'createProject'},
					success: function(result)
					{
						//result will return the id of the newly created project.
						//This occurs in ../db/dbManager.php
						url = "./editProject.php?id=" + result;
						window.location.replace(url);

					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(xhr.responseText);
						alert(thrownError);
					}
		});
	});

</script>

</html>
