<!DOCTYPE html>
<html lang="en">
<head>


	<?php include_once('../includes/header.php'); ?>

	<!-- Custom styles for this template-->
  <link href="../assets/css/sb-admin.css" rel="stylesheet">
	<title>Admin Control Users</title>
</head>

<?php require_once('../db/dbManager.php'); ?>
<?php require_once('../modules/createCards.php'); ?>
<?php //require_once('../modules/redirect.php'); ?>

<?php
if($_SESSION['accessLevel'] != 'Admin'){
	echo('<script type="text/javascript">alert("You are not authorized to be here!")</script>');
	header("Location: ./index.php"); /* Redirect Browser */
}
?>

<body>
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

		<!-- PAGES FOLDER DROP DOWN ON SIDE BAR
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-fw fa-folder"></i>
				<span>Pages</span>
			</a>
			<div class="dropdown-menu" aria-labelledby="pagesDropdown">
				<h6 class="dropdown-header">Login Screens:</h6>
				<a class="dropdown-item" href="login.html">Login</a>
				<a class="dropdown-item" href="register.html">Register</a>
				<a class="dropdown-item" href="forgot-password.html">Forgot Password</a>
				<div class="dropdown-divider"></div>
				<h6 class="dropdown-header">Other Pages:</h6>
				<a class="dropdown-item" href="404.html">404 Page</a>
				<a class="dropdown-item" href="blank.html">Blank Page</a>
			</div>
		</li>
                     -->

		<li class="nav-item">
			<a class="nav-link" href="adminProject.php">
				<i class="fas fa-fw fa-chart-area"></i>
				<span>Projects</span></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="adminUser.php">
				<i class="fas fa-fw fa-table"></i>
				<span>Users</span></a>
		</li>
        <li class="nav-item active">
			<a class="nav-link" href="adminApplication.php">
				<i class="fas fa-fw fa-file-invoice"></i>
				<span>Applications</span></a>
		</li>
	</ul>
	<div class="container-fluid">
		<br>
			<!-- Breadcrumbs-->
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a>Application</a>
				</li>
				<li class="breadcrumb-item active">Project Placement</li>
			</ol>
	
	</div>
	<?php include_once("../modules/footer.php"); ?>

</body>

<script type="text/javascript">

</script>


</html>
