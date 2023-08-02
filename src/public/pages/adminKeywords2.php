<!-- This is an old version of the adminKeywords.php page, preserved until Don verifies the new page. 
    Preserved on 7/31/23 -->

<?php
include_once '../bootstrap.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use DataAccess\KeywordsDao;

session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$isAdmin = isset($_SESSION['userID']) && !empty($_SESSION['userID']) 
	&& isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

allowIf($isAdmin);

$keywordsDao = new KeywordsDao($dbConn, $logger);

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
			<li class="nav-item">
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
            <li class="nav-item active">
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
					<li class="breadcrumb-item active">Keywords</li>
				</ol>

                <div class="tableWrapper">
                    <div class="tableHead" style="background: lightgreen">Approved Keywords</div>
                    <div class="tableBody">
                        <?php
                        $keywords = $keywordsDao->adminGetApprovedKeywords();

                        $countUp = 0;
                        foreach($keywords as $keyword) {
                            echo '<div class="tableElement">'.htmlspecialchars($keyword->getName()).'</div>';
                            $countUp++;
                        }
                        ?>
                    </div>
                </div>

                <div class="tableWrapper">
                    <div class="tableHead" style="background: #f3f38b">User-Added Keywords</div>
                    <div class="tableBody">
                        <?php
                        $keywords = $keywordsDao->adminGetUnapprovedKeywords();

                        foreach($keywords as $keyword) {
                            echo '<div class="tableElement">'.htmlspecialchars($keyword->getName()).'</div>';
                        }
                        ?>
                    </div>
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