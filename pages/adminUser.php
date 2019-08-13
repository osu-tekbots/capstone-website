<?php
include_once '../bootstrap.php';

use DataAccess\UsersDao;

session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$isAdmin = isset($_SESSION['userID']) && !empty($_SESSION['userID']) 
	&& isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

allowIf($isAdmin);

$usersDao = new UsersDao($dbConn, $logger);

$title = 'Admin User Control';
$css = array(
    'assets/css/sb-admin.css'
);
$js = array(
    'assets/js/jquery.tableedit.js'
);
include_once PUBLIC_FILES . '/modules/header.php';
?>
<br/>
<div id="page-top">

	<div id="wrapper">

	<!-- Sidebar -->
	<ul class="sidebar navbar-nav">
		<li class="nav-item">
			<a class="nav-link" href="pages/adminInterface.php">
				<i class="fas fa-fw fa-tachometer-alt"></i>
				<span>Dashboard</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" href="pages/adminProject.php">
				<i class="fas fa-fw fa-chart-area"></i>
				<span>Projects</span></a>
		</li>
		<li class="nav-item active">
			<a class="nav-link" href="pages/adminUser.php">
				<i class="fas fa-fw fa-table"></i>
				<span>Users</span></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="pages/adminApplication.php">
				<i class="fas fa-fw fa-file-invoice"></i>
				<span>Applications</span></a>
		</li>
	</ul>

	<div id="content-wrapper">

		<div class="container-fluid">

			<!-- Breadcrumbs-->
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a>Users</a>
				</li>
				<li class="breadcrumb-item active">Overview</li>
			</ol>


			<h2>Users Table</h2>
			<h6>*Columns colored orange are editable (click on cell)*</h6>
	<div class="search-table-outter wrapper">
	<table id="data_table" class="search-table inner scrolltable">
		<thead>
			<tr>
				<th bgcolor="#66C2E0">User ID</th>
				<th bgcolor="#FFA500">First Name <i class="fas fa-edit"></i></th>
				<th bgcolor="#FFA500">Last Name <i class="fas fa-edit"></i></th>
				<th bgcolor="#FFA500">ONID <i class="fas fa-edit"></i></th>
				<th bgcolor="#66C2E0">Salutation</th>
				<th bgcolor="#66C2E0">Email</th>
				<th bgcolor="#FFA500">Phone <i class="fas fa-edit"></i></th>
				<th bgcolor="#66C2E0">Affiliation</th>
				<th bgcolor="#FFA500">Major <i class="fas fa-edit"></th>
				<th bgcolor="#66C2E0">Auth Provider</th>
				<th bgcolor="#FFA500">Access Level <i class="fas fa-edit"></th>
				<?php
				//<th bgcolor="#FFA500">project_assigned <i class="fas fa-edit"></th>
				?>
			</tr>
		</thead>
		<tbody>


		<?php
		$users = $usersDao->getAllUsers();
		foreach ($users as $u) {
		    $uId = $u->getId();
		    $uFirstName = $u->getFirstName();
		    $uLastName = $u->getLastName();
		    $uOnid = $u->getOnid();
		    $uSalutation = $u->getSalutation()->getName();
		    $uEmail = $u->getEmail();
		    $uPhone = $u->getPhone();
		    $uAffiliation = $u->getAffiliation();
		    $uMajor = $u->getMajor();
		    $uAuthProvider = $u->getAuthProvider()->getName();
		    $uType = $u->getType()->getName();
		    // TODO: project assigned?

		    echo "
			<tr id='$uId'>
				<td>$uId</td>
				<td>$uFirstName</td>
				<td>$uLastName</td>
				<td>$uOnid</td>
				<td>$uSalutation</td>
				<td>$uEmail</td>
				<td>$uPhone</td>
				<td>$uAffiliation</td>
				<td>$uMajor</td>
				<td>$uAuthProvider</td>
				<td>$uType</td>
			</tr>
			";
		}
		?>
	 </tbody>
	</table>
	</div>

		 </div>
	</div>
</div>

</div>

<script type="text/javascript">

$(document).ready(function(){
	$('#data_table').Tabledit({
		url: 'modules/live_edit.php',
		editmethod: 'post',
		// Class for row when ajax request fails
		dangerClass: 'danger',
		// Class for row when save changes
		successClass: 'table-success',
		deleteButton: false,
		autoFocus: false,
		editButton: false,
		columns: {
		  identifier: [0, 'u_id'],
		  editable: [[1, 'u_fname'], [2, 'u_lname'], [3, 'u_onid'], [6, 'u_phone'], [8, 'u_major'], [10, 'u_ut_id', '{"1": "Student", "2": "Proposer", "3": "Admin"}'], [11, 'project_assigned']]
		},
		hideIdentifier: false,
		// Executed when the ajax request is completed
		onSuccess: function () {
			snackbar("Entry Successfully Updated!", type = 'success');
			return;
		},
		onFail: function () {
			snackbar("Ajax Request Error", type = 'error');
			return;
		}

		

	});
});

</script>

<?php 
include_once PUBLIC_FILES . '/modules/footer.php';
?>
