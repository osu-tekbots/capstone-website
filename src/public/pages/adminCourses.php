<?php
include_once '../bootstrap.php';
include_once PUBLIC_FILES . '/modules/newCourseModal.php';

use DataAccess\PreferredCoursesDao;

session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';
include_once PUBLIC_FILES . '/modules/cards.php';

$isAdmin = isset($_SESSION['userID']) && !empty($_SESSION['userID']) 
	&& isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

allowIf($isAdmin);

$preferredCoursesDao = new PreferredCoursesDao($dbConn, $logger);
$preferredCourses = $preferredCoursesDao->getAllPreferredCourses();

$title = 'Admin Courses Control';
$css = array(
    'assets/css/sb-admin.css',
    'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'
);
$js = array(
    'assets/js/jquery.tableedit.js',
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

        
        <div id="content-wrapper" stlye="padding: 20px">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3">
                        <h2>Course Listings</h2>
					    <br />
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <input class="form-control" id="filterInput" type="text" placeholder="Search..." />
                    </div>
                    <div class="col-sm-3" style="margin-left: auto; margin-right: 50px;">
                        <button type="button" id="openNewCourseModal" class="btn btn-primary" data-toggle="modal" data-target="#newCourseModal" style="display: block; margin: auto;">
                            + Add Course Listing
                        </button>
                    </div>
                </div>
                <br/>
                <table class='table' id='CourseListingsTable'>	
                    <caption>Current Inventory</caption>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="CourseListingsCardsTable">
                        <?php renderCourseListingCardGroup($preferredCourses);?>
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

    $(document).ready(function(){
    //As each letter is typed in filterInput, filtering of cards will occur.
    //For drop down lists, like filtering by key word, filterInput is programmically
    //filled and keyup behavior is explicitly called.
    $("#filterInput").on("keyup", function(){
        var value = $(this).val().toLowerCase();
        
        $('#CourseListingsTable tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            
        });
    });

    });

    //$('#createCourseBtn').on('click', function () {
    function createCourse() {
        console.log("yo");
        // Capture the data we need
        let courseName = $('#courseNameInput').val();
        let courseCode = $('#courseCodeInput').val();
        courseCode = courseCode.replace(/\s/g, '');
        console.log("name: ", courseName);
        console.log("code: ", courseCode);

        let data = {
            action: 'createCourse',
            name: courseName,
            code: courseCode
        };

        // Send our request to the API endpoint
        api.post('/courses.php', data).then(res => {
            window.location.reload();
            snackbar(res.message, 'success');
        }).catch(err => {
            snackbar(err.message, 'error');
        });
    };

    $('#testbtn').on('click', function () {
		snackbar(res.message, 'success');

	});

    function filterSelectChanged(filterObject){
        var value = filterObject.value;
        $("#filterInput").val(value);

        //Manually trigger keydown to mimic keydown function feature.
        //Attempted to programmically toggleProjectCard, but ran into
        //logical bug 2/26/19.
        var e = jQuery.Event("keydown");
        e.which = 77;
        $("#filterInput").trigger(e);
    }



</script>

<?php 
include_once PUBLIC_FILES . '/modules/footer.php';
?>