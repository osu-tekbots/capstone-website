<!DOCTYPE html>
<html lang="en">
<head>


	<?php include_once('../includes/header.php'); ?>

	<!-- Custom styles for this template-->

	<link href="../assets/css/sb-admin.css" rel="stylesheet">
	<!--
	(3/19/19) NEED TO FIX :
	CSS to make table row turn red on change - BUT changes size of text and overwrites other CSS as well
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
-->
	<script type="text/javascript" src="../assets/js/jquery.tableedit.js"></script>
	<title>Admin Interface</title>
</head>
<?php require_once('../db/dbManager.php'); ?>
<?php //require_once('../modules/redirect.php'); ?>
<?php
if($_SESSION['accessLevel'] != 'Admin'){
	echo('<script type="text/javascript">alert("You are not authorized to be here!")</script>');
	header("Location: ./index.php"); /* Redirect Browser */
}
?>

<body id="page-top">
	<?php include_once("../modules/navbar.php"); ?>

	<div id="wrapper">

	<!-- Sidebar -->
	<ul class="sidebar navbar-nav">
		<li class="nav-item">
			<a class="nav-link" href="adminInterface.php">
				<i class="fas fa-fw fa-tachometer-alt"></i>
				<span>Dashboard</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" href="adminProject.php">
				<i class="fas fa-fw fa-chart-area"></i>
				<span>Projects</span></a>
		</li>
		<li class="nav-item active">
			<a class="nav-link" href="adminUser.php">
				<i class="fas fa-fw fa-table"></i>
				<span>Users</span></a>
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
			<h6>*Columns colored orange are editable (click on row)*</h6>
	<div class="search-table-outter wrapper">
	<table id="data_table" class="search-table inner">
		<thead>
			<tr>
				<th bgcolor="#66C2E0">user_id</th>
				<th bgcolor="#FFA500">first_name <i class="fas fa-edit"></i></th>
				<th bgcolor="#FFA500">last_name <i class="fas fa-edit"></i></th>
				<th bgcolor="#FFA500">student_id <i class="fas fa-edit"></i></th>
				<th bgcolor="#66C2E0">salutation</th>
				<th bgcolor="#66C2E0">email</th>
				<th bgcolor="#FFA500">phone <i class="fas fa-edit"></i></th>
				<th bgcolor="#66C2E0">affiliation</th>
				<th bgcolor="#FFA500">major <i class="fas fa-edit"></th>
				<th bgcolor="#66C2E0">auth_provider</th>
				<th bgcolor="#FFA500">type <i class="fas fa-edit"></th>
				<th bgcolor="#FFA500">project_assigned <i class="fas fa-edit"></th>
			</tr>
		</thead>
		<tbody>


		<?php
		$result = getListUsers();
		if ($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				?>
				<tr id="<?php echo $row['user_id']; ?>">
				<td><?php echo $row['user_id']; ?></td>
				<td><?php echo $row['first_name']; ?></td>
				<td><?php echo $row['last_name']; ?></td>
				<td><?php echo $row['student_id']; ?></td>
				<td><?php echo $row['salutation']; ?></td>
				<td><?php echo $row['email']; ?></td>
				<td><?php echo $row['phone']; ?></td>
				<td><?php echo $row['affiliation']; ?></td>
				<td><?php echo $row['major']; ?></td>
				<td><?php echo $row['auth_provider']; ?></td>
				<td><?php echo $row['type']; ?></td>
				<td><?php echo $row['project_assigned']; ?></td>
				</tr>
			<?php }
		} ?>
	 </tbody>
	</table>
	</div>

		 </div>
	</div>
</div>



</body>

<script type="text/javascript">

$(document).ready(function(){
	$('#data_table').Tabledit({
		deleteButton: false,
		autoFocus: false,
		editButton: false,
		columns: {
		  identifier: [0, 'user_id'],
		  editable: [[1, 'first_name'], [2, 'last_name'], [3, 'student_id'], [6, 'phone'], [8, 'major'], [10, 'type', '{"1": "Admin", "2": "Proposer", "3": "Student"}'], [11, 'project_assigned']]
		},
		hideIdentifier: false,
		url: '../modules/live_edit.php'
	});
});



</script>
<?//php include('footer.php');?>
