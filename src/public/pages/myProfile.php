<?php
include_once '../bootstrap.php';

use DataAccess\UsersDao;
use DataAccess\CapstoneProjectsDao;

session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);

allowIf($isLoggedIn);

$title = 'My Profile';
include_once PUBLIC_FILES . '/modules/header.php';

$usersDao = new UsersDao($dbConn, $logger);

$user = $usersDao->getUser($_SESSION['userID']);

// TODO: handle the case where we aren't able to fetch the user
$salutations = $usersDao->getUserSalutations();

$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$projects = $projectsDao->getCapstoneProjectsForUser($user->getId());

$uId = $user->getId();
$uFirstName = $user->getFirstName();
$uLastName = $user->getLastName();
$uAffiliation = $user->getAffiliation();
$uSalutationId = $user->getSalutation()->getId();
$uSalutationName = $user->getSalutation()->getName();
$uMajor = $user->getMajor();
$uPhone = $user->getPhone();
$uEmail = $user->getEmail();
$uTypeName = $user->getType()->getName();

?>

<form id="formUserProfile">
	<input type="hidden" name="uid" value="<?php echo $uId; ?>" />
	<div class="jumbotron jumbotron-fluid">
		<h1 class="display-4">My Profile</h1>
		<hr class="my-4">
		<div class="container bootstrap snippets">
			<div class="row">
				<div class="col-sm-6">
					<div class="panel-heading">
						<h4 class="panel-title">User Info</h4>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col control-label" for="firstNameText">First Name</label>
							<div class="col-sm-11">
								<textarea class="form-control" id="firstNameText" name="firstName" rows="1"
									required><?php echo $uFirstName; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col control-label" for="lastNameText">Last Name</label>
							<div class="col-sm-11">
								<textarea class="form-control" id="lastNameText" name="lastName"
									rows="1"><?php echo $uLastName; ?></textarea>
							</div>
						</div>
						<div class="container">
							<div class="row">

								<div class="col-sm-6">
									<label for="affiliationText">Affiliation</label>
									<textarea class="form-control" id="affiliationText" name="affiliation"
										rows="1"><?php echo $uAffiliation; ?></textarea>
								</div>
								<div class="col-sm-5">
									<label for="salutationSelect">Salutation</label>
									<select class="form-control" id="salutationSelect" name="salutationId">
										<?php
											foreach ($salutations as $salutation) {
											    $option = '<option ';
											    $usid = $salutation->getId();
											    $usname = $salutation->getName();
											    if ($usid == $uSalutationId) {
											        $option .= 'selected ';
											    }
											    $option .= "value=\"$usid\">$usname</option>";
											    echo $option;
											} ?>
									</select>
								</div>
							</div>
						</div>
						<br>
						<?php 
						if ($user->getType()->getName() == 'Student'): ?>
							<div class="student">
								<label class="col control-label" for="majorText">Major</label>
								<div class="col-sm-11">
									<textarea class="form-control" id="majorText" name="major"
										rows="1"><?php echo $user->getMajor(); ?></textarea>
								</div>
							</div>
						<?php 
						endif; ?>
						<div class="panel-body">
							<br>
							<div class="col-sm-11">
								<button class="btn btn-large btn-block btn-primary" id="saveProfileBtn"
									type="button">Save</button>
								<div id="successText" class="successText" style="display:none;">Success!</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="panel-heading">
						<h4 class="panel-title">Contact Info</h4>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col control-label" for="phoneText">Phone Number</label>
							<div class="col">
								<textarea class="form-control" id="phoneText" name="phone" rows="1"
									required><?php echo $user->getPhone(); ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col control-label" for="emailText">Email Address</label>
							<div class="col">
								<textarea class="form-control" id="emailText" name="email" rows="1"
									required><?php echo $user->getEmail(); ?></textarea>
							</div>
						</div>
						<br>
						<div class="panel-heading">
							<h4 class="panel-title">Account info</h4>
						</div>
						<hr class="my-4">
						<div class="form-group">
							<p class="form-control-static">User Type: <?php echo $user->getType()->getName(); ?> </p>
							<div class="col">

							</div>
						</div>
						<div class="form-group">
							<?php
								// TODO: display projects here
								?>
							<div class="col-sm-11">

							</div>
						</div>
					</div>
				</div>
			</div>

</form>
</div>

<script defer type="text/javascript">
/**
 * Event handler for a click event on the 'Save' button for user profiles.
 */
function onSaveProfileClick() {

	let data = new FormData(document.getElementById('formUserProfile'));
	data.append("email", editor.getData());

	let body = {
		action: 'saveProfile'
	};
	for(const [key, value] of data.entries()) {
		body[key] = value;
	}	

	api.post('/users.php', body).then(res => {
		snackbar(res.message, 'success');
	}).catch(err => {
		snackbar(err.message, 'error');
	});

}
$('#saveProfileBtn').on('click', onSaveProfileClick);
</script>

<!-- link for rich text editing -->
<script src="https://cdn.ckeditor.com/ckeditor5/31.1.0/classic/ckeditor.js"></script>
<script>
	let editor;
    ClassicEditor
        .create( document.querySelector( '#emailText' ), {
			toolbar: [ 'heading', '|',
				'bold', 'italic', '|',
				'bulletedList', 'numberedList', 'blockQuote', '|',  
				'link', 'unlink', '|', 
				'outdent', 'indent', '|',
				'inserttable', '|', 
				'undo', 'redo' ]
		} )
		.then( newEditor => {
        editor = newEditor;
    	} )
        .catch( error => {
            console.error( error );
        } );
</script>

<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>