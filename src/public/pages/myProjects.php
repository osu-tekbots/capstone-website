<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use DataAccess\KeywordsDao;
use DataAccess\CategoriesDao;

if (!session_id()) {
    session_start();
}

include_once PUBLIC_FILES . '/lib/shared/authorize.php';
allowIf($_SESSION['userID'] . '' != '');

$userId = $_SESSION['userID'];

$isAdmin = isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';
$isProposer = isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Proposer';

// Get all the projects that need to be displayed on this page
$dao = new CapstoneProjectsDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);
$categoriesDao = new CategoriesDao($dbConn, $logger);

$projects = $dao->getActiveCapstoneProjectsForUser($userId);
$archived = $dao->getArchivedCapstoneProjectsForUser($userId);

$title = 'My Projects';
include_once PUBLIC_FILES . '/modules/header.php';
include_once PUBLIC_FILES . '/modules/cards.php';

?>
<br><br><br>
<div class="container-fluid">
	<h1>My Projects</h1>
	<div class="row">
		<div class="col-sm-3">
			<?php 
			if($isAdmin || $isProposer): ?>
				<button class="btn btn-lg btn-outline-primary capstone-nav-btn" type="button" data-toggle="modal"
					data-target="#newProjectModal" id="openNewProjectModalBtn">Create New Project</button>
			<?php
			endif; ?>
		</div>
	</div>
	<div class='row'>
		<h3>Active Projects</h3>
		<div class="col-sm-12 scroll jumbotron capstoneJumbotron">
			<div class="masonry" id="projectCardGroup">
				<?php renderProjectCardGroup($projects, $keywordsDao, $categoriesDao); ?>
			</div>
		</div>
	</div>
	<div class="row">	
		<h3>Archived Projects</h3>
		<div class="col-sm-12 scroll jumbotron capstoneJumbotron">
			<div class="masonry" id="projectArchiveCardGroup">
				<?php renderProjectCardGroup($archived, $keywordsDao, $categoriesDao); ?>
			</div>
		</div>

	</div>

	<?php 
	if ($isAdmin || $isProposer) {
	    include_once PUBLIC_FILES . '/modules/newProjectModal.php'; ?>

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

	<?php
	} ?>

</div>

<?php echo '<input id="proposerIDHeader" style="display:none;" value="' . $_SESSION['userID'] . '"></input>'; ?>
<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>