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
    'assets/css/sb-admin.css',
	'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'
);
$js = array(
    'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'
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
				<table id="data_table" class="search-table inner scrolltable" width="2250" style="overflow: scroll">
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
								<td style='overflow: hidden'><input type='text' value='$uLastName' onchange='editValue(this, \"u_lname\")' onkeypress='checkEnterKey(event)' style='border: none'></td>
								<td style='overflow: hidden'><input type='text' value='$uFirstName' onchange='editValue(this, \"u_fname\")' onkeypress='checkEnterKey(event)' style='border: none'></td>
								<td style='overflow: hidden'><input type='text' value='$uEmail' onchange='editValue(this, \"u_email\")' onkeypress='checkEnterKey(event)' style='border: none'></td>
								<td style='overflow: hidden'><input type='text' value='$uOnid' onchange='editValue(this, \"u_onid\")' onkeypress='checkEnterKey(event)' style='border: none'></td>
								<td style='overflow: hidden'><select onchange='editValue(this, \"u_ut_id\")' style='border: none; -webkit-appearance:none; text-indent: 1px; text-overflow: \"\"'>
									<option value='1' ".($uType=='Student' ? 'selected' : '').">Student</option>
									<option value='2' ".($uType=='Proposer' ? 'selected' : '').">Proposer</option>
									<option value='3' ".($uType=='Admin' ? 'selected' : '').">Admin</option>
									</select></td>
								<td style='overflow: hidden'>$uId</td>
								<td style='overflow: hidden'><input type='text' value='$uPhone' onchange='editValue(this, \"u_phone\")' style='border: none' onkeypress='checkEnterKey(event)'></td>
								<td style='overflow: hidden'>$uSalutation</td>
								<td style='overflow: hidden'><input type='text' value='$uAffiliation' onchange='editValue(this, \"u_affiliation\")' style='border: none' onkeypress='checkEnterKey(event)'></td>
								<td style='overflow: hidden'><input type='text' value='$uMajor' onchange='editValue(this, \"u_major\")' style='border: none' onkeypress='checkEnterKey(event)'></td>
								<td style='overflow: hidden'>$uAuthProvider</td>
						
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

function editValue(evntTarget, type) {
	let data = `action=edit&u_id=${evntTarget.parentElement.parentElement.id}&${type}=${evntTarget.value}`;

	let xhr = new XMLHttpRequest();
	xhr.onload = function () {
		let data;
		try {        
			data = JSON.parse(this.response);
			console.log(this.response);
		} catch(err) { 
			console.log(this.response);
			console.log(err);
			snackbar('Failed to parse response from server (Expecting JSON)', 'error');
			return;
		}
		if (this.status >= 200 && this.status < 300) {
			console.log(data);
			snackbar('Successfully updated', 'success');
			return;
		} else {
			snackbar(data.message, 'error');
		}
	};
	xhr.open('post', 'modules/live_edit.php', true);


	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send(data);
}

function checkEnterKey(evt) {
	if(evt.key == 'Enter') {
		let trigger = new Event('change');
		evt.target.blur();
		evt.target.dispatchEvent(trigger);
	}

}

$('#data_table').DataTable({
	'searching': false,
	// 'scrollX': true, 
	paging: true, 
	pageLength: 100,
	lengthMenu: [ [50, 100, 250, 500, 1000, -1], [50, 100, 250, 500, 1000, "All"] ],
	order:[[0, 'asc']],
	columns: [
		{ orderable: false },
		{ orderable: false },
		{ orderable: false },
		{ orderable: false },
		{ orderable: false },
		{ orderable: false },
		{ orderable: false },
		{ orderable: false },
		{ orderable: false },
		{ orderable: false },
		{ orderable: false }
		]
});

</script>

<?php 
include_once PUBLIC_FILES . '/modules/footer.php';
?>
