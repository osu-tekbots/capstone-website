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
            <li class="nav-item">
                <a class="nav-link" href="pages/adminCourses.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Course Listings</span></a>
            </li>
        </ul>

        
        <div id="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    Course Listings 
                </div>
                <table class='table' id='CourseListingsTable'>	
                    <caption>Current Inventory</caption>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Associated Keywords</th>
                        </tr>
                    </thead>
                    <tbody id="CourseListingsCardsTable">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#CourseListingsTable').DataTable({
		'searching':false,
		'scrollX':true, 
		'paging':false, 
		'order':[[1, 'asc']],
		"columns": [
			{ "orderable": false },
			null,
			null,
			null,
			null
		  ]
		});
</script>

<?php 
include_once PUBLIC_FILES . '/modules/footer.php';
?>