<?php
use DataAccess\CapstoneProjectsDao;

if (!session_id()) {
    session_start();
}

include_once PUBLIC_FILES . '/lib/shared/authorize.php';
allowIf($_SESSION['userID'] . '' != '');

// Get all the projects that need to be displayed on this page
$dao = new CapstoneProjectsDao($dbConn, $logger);
if($_SESSION['accessLevel'] == 'Admin') {
	$projects = array_merge(
		$dao->getPendingCapstoneProjects(),
		$dao->getCapstoneProjectsForUser($_SESSION['userID'])
	);
} else {
	$projects = $dao->getCapstoneProjectsForUser($_SESSION['userID']);
}

$title = 'My Projects';
include_once PUBLIC_FILES . '/modules/header.php';

include_once PUBLIC_FILES . '/modules/cards.php';

?>
<br><br>
<div class="container-fluid">
	<h1>My Projects</h1>
	<div class="row">
		<div class="col-sm-3">
			<button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button" data-toggle="modal"
				data-target="#newProjectModal" id="openNewProjectModalBtn">Create New Project</button>
			<div id="deleteText" class="adminText" style="color: red;">Project Deleted</div>
		</div>

		<div class="col-sm-9 scroll jumbotron capstoneJumbotron">
			<div class="card-columns capstoneCardColumns" id="projectCardGroup">
				<?php renderProjectCardGroup($projects); ?>
			</div>
		</div>

	</div>

	<?php include PUBLIC_FILES . '/modules/newProjectModal.php'; ?>

	<script type="text/javascript">
		$('#createProjectBtn').on('click', function () {
			// Capture the data we need
			let data = {
				action: 'createProject',
				title: $('#projectTitleInput').val(),
				uid: $('#proposerIDHeader').val()
			};

			// Send our request to the API endpoint
			api.post('/projects.php', data).then(res => {
				window.location.replace('pages/editProject.php?id=' + res.content.id);
			}).catch(err => {
				snackbar(err.message, 'error');
			});
		});
	</script>
</div>
<?php echo '<input id="proposerIDHeader" style="display:none;" value="' . $_SESSION['userID'] . '"></input>'; ?>
<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>