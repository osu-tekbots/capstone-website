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
				<span>Active Projects</span></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="pages/adminProject.php?archive">
				<i class="fas fa-fw fa-chart-area"></i>
				<span>Archived Projects</span></a>
		</li>
		<li class="nav-item active">
			<a class="nav-link" href="pages/adminUser.php">
				<i class="fas fa-fw fa-table"></i>
				<span>Users</span></a>
		</li>
		<!-- <li class="nav-item">
			<a class="nav-link" href="pages/adminApplication.php">
				<i class="fas fa-fw fa-file-invoice"></i>
				<span>Applications</span></a>
		</li> -->
		<li class="nav-item">
			<a class="nav-link" href="pages/adminCourses.php">
			<i class="fas fa-fw fa-table"></i>
			<span>Course Listings</span></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="pages/adminKeywords.php">
				<i class="fas fa-fw fa-table"></i>
				<span>Keywords</span></a>
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
							<th bgcolor="#FFA500">Last Name <i class="fas fa-edit"></i></th>
							<th bgcolor="#FFA500">First Name <i class="fas fa-edit"></i></th>
							<th bgcolor="#FFA500">Email <i class="fas fa-edit"></i></th>
							<th bgcolor="#FFA500">ONID <i class="fas fa-edit"></i></th>
							<th bgcolor="#FFA500">Access Level <i class="fas fa-edit"></th>
							<th bgcolor="#66C2E0">User ID</th>
							<th bgcolor="#FFA500">Phone <i class="fas fa-edit"></i></th>
							<th bgcolor="#66C2E0">Salutation</th>
							<th bgcolor="#FFA500">Affiliation <i class="fas fa-edit"></i></th>
							<th bgcolor="#FFA500">Major <i class="fas fa-edit"></th>
							<th bgcolor="#66C2E0">Auth Provider</th>
							
							<?php
							//<th bgcolor="#FFA500">project_assigned <i class="fas fa-edit"></th>
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						$users = $usersDao->getAllUsers();
						foreach ($users as $u) {
							$uLastName = $u->getLastName();
							$uFirstName = $u->getFirstName();
							$uEmail = $u->getEmail();
							$uOnid = $u->getOnid();
							$uType = $u->getType()->getName();
							$uId = $u->getId();
							$uPhone = $u->getPhone();
							$uSalutation = $u->getSalutation()->getName();
							$uAffiliation = $u->getAffiliation();
							$uMajor = $u->getMajor();
							$uAuthProvider = $u->getAuthProvider()->getName();

							// TODO: project assigned?

							echo "
							<tr id='$uId'>
								<td>$uLastName</td>
								<td>$uFirstName</td>
								<td>$uEmail</td>
								<td>$uOnid</td>
								<td>$uType</td>
								<td>$uId</td>
								<td>$uPhone</td>
								<td>$uSalutation</td>
								<td>$uAffiliation</td>
								<td>$uMajor</td>
								<td>$uAuthProvider</td>
						
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
	let startTime = Date.now();
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
		paging: true,
		columns: {
		  identifier: [5, 'u_id'],
		  editable: [[1, 'u_fname'], [0, 'u_lname'], [3, 'u_onid'], [6, 'u_phone'], [9, 'u_major'], [4, 'u_ut_id', '{"1": "Student", "2": "Proposer", "3": "Admin"}'], [11, 'project_assigned'], [8, 'u_affiliation'], [2, 'u_email'] ]
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
	console.log('Building Tabledit took ' + (Date.now() - startTime) + 'ms');
});

</script>

<?php 
include_once PUBLIC_FILES . '/modules/footer.php';
?>
