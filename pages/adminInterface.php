<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;

session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$isAdmin = isset($_SESSION['userID']) && !empty($_SESSION['userID']) 
	&& isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

allowIf($isAdmin);

$projectsDao = new CapstoneProjectsDao($dbConn, $logger);

$title = 'Admin Interface';
$css = array(
    'assets/css/sb-admin.css'
);
include_once PUBLIC_FILES . '/modules/header.php';

?>
<br/>
<div id="page-top">

	<div id="wrapper">

		<!-- Sidebar -->
		<ul class="sidebar navbar-nav">
			<li class="nav-item active">
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
				<a class="nav-link" href="pages/adminProject.php">
					<i class="fas fa-fw fa-chart-area"></i>
					<span>Projects</span></a>
			</li>
			<li class="nav-item">
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
						<a>Dashboard</a>
					</li>
					<li class="breadcrumb-item active">Overview</li>
				</ol>

				<?php
				$stats = $projectsDao->getCapstoneProjectStats();
				$pendingProjects = $stats['projectsPending'];
				$pendingCategories = $stats['projectsNeedingCategoryPlacement'];

				// TODO: ask about email here
				// if ($pendingProjects == 5 || $pendingCategories == 5){
				// 	notifyAdminEmail($pendingProjects, $pendingCategories);
				// }
				?>
				<!-- Icon Cards-->
				<div class="row">
					<div class="col-xl-3 col-sm-6 mb-3">
						<div class="card text-white bg-danger o-hidden h-100">
							<div class="card-body">
								<div class="card-body-icon">
									<i class="fas fa-thumbs-up"></i>
								</div>
								<div class="mr-5"><?php echo($pendingProjects)?> PENDING projects!</div>
							</div>
							<a class="card-footer text-white clearfix small z-1" href="pages/adminProject.php">
								<span class="float-left">View Details</span>
								<span class="float-right">
									<i class="fas fa-angle-right"></i>
								</span>
							</a>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6 mb-3">
						<div class="card text-white bg-warning o-hidden h-100">
							<div class="card-body">
								<div class="card-body-icon">
									<i class="fas fa-fw fa-list"></i>
								</div>
								<div class="mr-5"><?php echo($pendingCategories); ?> Projects Need Categories</div>
							</div>
							<a class="card-footer text-white clearfix small z-1" href="pages/adminProject.php">
								<span class="float-left">View Details</span>
								<span class="float-right">
									<i class="fas fa-angle-right"></i>
								</span>
							</a>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6 mb-3">
						<div class="card text-white bg-success o-hidden h-100">
							<div class="card-body">
								<div class="card-body-icon">
									<i class="fas fa-fw fa-shopping-cart"></i>
								</div>
								<div class="mr-5">Browse Projects</div>
							</div>
							<a class="card-footer text-white clearfix small z-1" href="pages/adminProject.php">
								<span class="float-left">View Details</span>
								<span class="float-right">
									<i class="fas fa-angle-right"></i>
								</span>
							</a>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6 mb-3">
						<div class="card text-white bg-primary o-hidden h-100">
							<div class="card-body">
								<div class="card-body-icon">
									<i class="fas fa-users"></i>
								</div>
								<div class="mr-5">Users Table</div>
							</div>
							<a class="card-footer text-white clearfix small z-1" href="pages/adminUser.php">
								<span class="float-left">View Details</span>
								<span class="float-right">
									<i class="fas fa-angle-right"></i>
								</span>
							</a>
						</div>
					</div>
				</div>

				<!-- Area Chart Example-->
				<div class="card mb-3">
					<div class="card-header">
						<i class="fas fa-chart-area"></i>
						Bar Graph</div>
					<div id="barGraphContainer" style="height: 300px; width: 100%;"></div>
					<div class="card-footer small text-muted"></div>
				</div>

				<div class="card mb-3">
					<div class="card-header">
						<i class="fas fa-chart-area"></i>
						Pie Chart</div>
					<div id="chartContainer" style="height: 300px; width: 100%;"></div>
					<div class="card-footer small text-muted"></div>
				</div>


			</div>
		</div>
	</div>
</div>

<script>

window.onload = function () {

var options = {
	animationEnabled: true,
	title: {
		text: "Most Popular Keywords"
	},
	axisY: {
		title: "Number Of Times Tagged",
		suffix: "",
		includeZero: false
	},
	axisX: {
		title: "Keywords"
	},
	data: [{
		type: "column",
		yValueFormatString: "#,##0.0#"%"",
		dataPoints: [
			{ label: "Machine Learning", y: 10.09 },
			{ label: "Artificial Intelligence", y: 10.40 },
			{ label: "Embedded Systems", y: 18.50 },
			{ label: "Coding", y: 11.96 },
			{ label: "Circuits", y: 7.80 },
			{ label: "Probability", y: 15.56 },
			{ label: "Statistical Analysis", y: 7.20 },
			{ label: "3D Printing", y: 7.3 }

		]
	}]
};

var options1 = {
	title: {
		text: "User Type"
	},
	subtitles: [{
		text: ""
	}],
	animationEnabled: true,
	data: [{
		type: "pie",
		startAngle: 40,
		toolTipContent: "<b>{label}</b>: {y}%",
		showInLegend: "true",
		legendText: "{label}",
		indexLabelFontSize: 16,
		indexLabel: "{label} - {y}%",
		dataPoints: [
			{ y: 48.36, label: "Proposers" },
			{ y: 26.85, label: "Students" },
			{ y: 1.49, label: "Admins" }
		]
	}]
};

$("#barGraphContainer").CanvasJSChart(options);
$("#chartContainer").CanvasJSChart(options1);

}


</script>

<?php 
include_once PUBLIC_FILES . '/modules/footer.php' ; 
?>
