<?php
include_once '../bootstrap.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use DataAccess\CapstoneProjectsDao;
use DataAccess\CategoriesDao;

session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$isAdmin = isset($_SESSION['userID']) && !empty($_SESSION['userID']) 
	&& isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

allowIf($isAdmin);

$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$categoryDao = new CategoriesDao($dbConn, $logger);

$title = 'Admin Interface';
$css = array(
    'assets/css/sb-admin.css'
);
include_once PUBLIC_FILES . '/modules/header.php';

$tooltipApprovedProjects = "";
$tooltipPendingProjects = "";
$tooltipCreatedProjects = "";
$tooltipAllProjects = "";

$projectStats = '';

$cs461 = 0;
$ece441 = 0;
$cs467 = 0;
$ai = 0;
$projects = $projectsDao->getCapstoneProjectsForAdmin();
foreach ($projects as $p){
	if (($categoryDao->categoryExistsForEntity(5, $p->getId()) || $categoryDao->categoryExistsForEntity(7, $p->getId())) && $p->getStatus()->getId() == 4)
		$ece441++;
	if ($categoryDao->categoryExistsForEntity(6, $p->getId()) || $categoryDao->categoryExistsForEntity(9, $p->getId()) || $categoryDao->categoryExistsForEntity(7, $p->getId()))
		$cs461++;
	if ($categoryDao->categoryExistsForEntity(8, $p->getId()) || $categoryDao->categoryExistsForEntity(9, $p->getId()))
		$cs467++;
}

$projectStats .= "ECE44x Projects: $ece441<BR>CS461 Projects: $cs461<BR>CS467 Projects: $cs467<BR>";





?>
<br/>
<div id="page-top">

	<div id="wrapper">

		<!-- Sidebar -->
		<ul class="sidebar navbar-nav">
			<li class="nav-item active">
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
			<li class="nav-item">
				<a class="nav-link" href="pages/adminUser.php">
					<i class="fas fa-fw fa-table"></i>
					<span>Users</span></a>
			</li>
<!--	<li class="nav-item">
		<a class="nav-link" href="pages/adminApplication.php">
			<i class="fas fa-fw fa-file-invoice"></i>
			<span>Applications</span></a>
	</li>
--><li class="nav-item">
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
						<a>Dashboard</a>
					</li>
					<li class="breadcrumb-item active">Overview</li>
				</ol>

				<?php
				$stats = $projectsDao->getCapstoneProjectStats();
				$pendingProjects = $stats['projectsPending'];
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
							<a class="card-footer text-white clearfix small z-1" href="pages/adminProject.php?status=2">
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
				<div class="csvArea mb-3">
					<div class="card-header">
						<i class="fas fa-chart-area"></i>
						Export Options</div>
						<form method="post" action="modules/export.php">
						<div class="row">
							<div class="col-sm">
								<input type="submit" name="exportApprovedProjects" class="csvExport btn btn-success" value="APPROVED Projects CSV" />
								<input type="submit" name="exportPendingProjects" class="csvExport btn btn-success" value="PENDING Projects CSV" />
								<input type="submit" name="exportCreatedProjects" class="csvExport btn btn-success" value="CREATED Projects CSV" />
								<input type="submit" name="exportAllProjects" class="csvExport btn btn-success" value="ALL Projects CSV" />


							</div>
			
						</div>
						</form>
				
				</div>

				<!-- Area Chart Example-->
				<div class="card mb-3">
					<div class="card-header">
						<i class="fas fa-chart-area"></i>
						Statistics</div>
					<div style="height: 300px; width: 100%;">
					<?php echo $projectStats;?></div>
					<div id="chartContainer" style="height: 300px; width: 100%;"></div>
					<div class="card-footer small text-muted"></div>
				</div>




			</div>
		</div>
	</div>
</div>

<script>


</script>

<?php 
include_once PUBLIC_FILES . '/modules/footer.php' ; 
?>
