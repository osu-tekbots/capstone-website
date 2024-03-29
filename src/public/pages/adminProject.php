<?php
include_once '../bootstrap.php';

/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */

use DataAccess\CapstoneProjectsDao;
use DataAccess\KeywordsDao;
use DataAccess\CategoriesDao;

session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$isAdmin = isset($_SESSION['userID']) && !empty($_SESSION['userID']) 
	&& isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

$userId = $_SESSION['userID'];

allowIf($isAdmin);

$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);
$categoriesDao = new CategoriesDao($dbConn, $logger);

$projects = $projectsDao->getCapstoneProjectsForAdmin();
$types = $projectsDao->getCapstoneProjectTypes();
$statuses = $projectsDao->getCapstoneProjectStatuses();

if (isset($_REQUEST['archive'])){ //Only show archived projects to admin
	$projects_new = Array();
	foreach ($projects AS $project){
		if ($project->getIsArchived() == true)
			$projects_new[] = $project;
	}
	$CardCount = count($projects_new);
	$projects = $projects_new;
} else { //Only show un-archived projects to admin
	$projects_new = Array();
	foreach ($projects AS $project){
		if ($project->getIsArchived() == false)
			$projects_new[] = $project;
	}
	$CardCount = count($projects_new);
	$projects = $projects_new;
}

include_once PUBLIC_FILES . '/modules/cards.php';

$title = 'Admin Project Control';
$css = array(
    'assets/css/sb-admin.css',
	'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'
);
$js = array(
    'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'
);

include_once PUBLIC_FILES . '/modules/header.php';

?>

<script type="text/javascript">

    /*********************************************************************************
    * Function Name: strstr()
    * Description: Mimics strstr() php function that searches for the first occurence
    * of a string (needle) in another string (haystack).
    *********************************************************************************/
    function strstr(haystack, needle, bool) {
        var pos = 0;
        haystack += '';
        pos = haystack.toLowerCase().indexOf((needle + '').toLowerCase());
        if (pos == -1) {
            return false;
        } else {
            if (bool) {
                return haystack.substr(0, pos);
            } else {
                return haystack.slice(pos);
            }
        }
    }
	
	
	/*********************************************************************************
    * Function Name: typeChange(id)
    * Description: Updates the type of a project using onchange() for a displayed project.
    *********************************************************************************/
    function typeChange(id) {
		var childID = $('#typeselect'+id).children(":selected").attr("value");
		
		let body = {
			action: 'updateType',
			typeId: childID,
			projectId: id
		}
		
		api.post('/projects.php', body).then(res => {
			snackbar(res.message, 'Updated');
		}).catch(err => {
			snackbar(err.message, 'error');
		});
	}
	

    $(document).ready(function(){

		//As each letter is typed in filterInput, filtering of cards will occur.
		//For drop down lists, like filtering by key word, filterInput is programmically
		//filled and keyup behavior is explicitly called.

		$("#filterInput").on("keyup", function(){
			var value = $(this).val().toLowerCase();
			
			$('#projectCardsTable tr').filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				
			});
		});
		
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
	
	function showAdminNeeded() {
		$.ajax({
                type: 'POST',
                url: './modules/filter.php',
                dataType: 'html',
                data: {action: 'adminRequired'},
                success: function(result)
                    {
                        $('#projectCardGroup').load(result);  
                    },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(xhr.responseText);
                    alert(thrownError);
                }
            });
		
	}
</script>


<br/>
<div style="background-color:#e9ecef;">

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
	<div class="container-fluid">
		<br>
			<!-- Breadcrumbs-->
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a>Projects</a>
				</li>
				<li class="breadcrumb-item active">Approval Proccess</li>
			</ol>

		<div class="row">
		<div class="col-sm-12">
            <div class="row">
                <div class="col-sm-3">
                    <h2>Search and Filter</h2>
					<input class="form-control" id="filterInput" type="text" placeholder="Search..." />
					<br />
                </div>

				<?php
				if (!isset($_REQUEST['archive'])){//Only filter options for active projects
					echo "
					<div class='col-sm-5' style='border: 2px solid grey; border-radius: 10px; margin-bottom: 10px; padding: 10px;'>				
					<div class='row'>
						<div class='col-sm-6'>
							<div class='form-check'>
								<input type='checkbox' class='form-check-input' id='ApprovalRequiredCheckBox' onchange='toggleAdminNeeded();'>
								<label for='ApprovalRequiredCheckBox'>Hide projects do NOT need Admin Action</label>
							</div>
							<div class='form-check'>
								<input type='checkbox' class='form-check-input' id='PendingApprovalCheckBox' onchange='togglePendingApproval();' checked>
								<label for='PendingApprovalCheckBox' style='background-color: #f9cb9c'>Show projects Pending Approval</label>
							</div>
							<div class='form-check'>
								<input type='checkbox' class='form-check-input' id='ApprovedUnpublishedCheckBox' onchange='toggleApprovedUnpublished();' checked>
								<label for='ApprovedUnpublishedCheckBox' style='background-color: #ffe599'>Show Approved but Unpublished projects</label>
							</div>
						</div>	
						<div class='col-sm-6'>
							<div class='form-check'>
								<input type='checkbox' class='form-check-input' id='notSubmittedCheckBox' onchange='toggleShowCreated();' checked>
								<label for='notSubmittedCheckBox' style='background-color: #9fc5e8'>Show Created projects (Not-Submitted)</label>
							</div>
							<div class='form-check'>
								<input type='checkbox' class='form-check-input' id='AcceptingApplicantsCheckBox' onchange='toggleAcceptingApplicants();' checked>
								<label for='AcceptingApplicantsCheckBox' style='background-color: #b6d7a8'>Show projects Accepting Applicants</label>
							</div>
							<div class='form-check'>
								<input type='checkbox' class='form-check-input' id='RejectedCheckBox' onchange='toggleRejected();' checked>
								<label for='RejectedCheckBox' style='background-color: #ff8a6d'>Show Rejected projects</label>
							</div>
						</div>
					</div>
				</div>";
				}
				?>

                
            </div>
        </div>
	</div>

		<table class='table' id='ProjectsTable'>	
			<caption>Current Inventory</caption>
			<thead>
				<tr>
					<th>Status</th>
					<th></th>
					<th>Description</th>
					<th>Project Info</th>
					<th>Partner Info</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody id="projectCardsTable">
				<?php renderAdminProjectCardGroup($projects, $keywordsDao, $categoriesDao, $types, $statuses, false);?>
			</tbody>
		</table>

</div>




<script>
function toggleAdminNeeded(){
	
	var activeProjectsAccepting = document.getElementsByClassName('acceptingApplicants');
	var activeProjectsCreated = document.getElementsByClassName('created');
	var activeProjectsRejected = document.getElementsByClassName('rejected');
	var checkBox = document.getElementById("ApprovalRequiredCheckBox");
	
	if (checkBox.checked == true){
		for (var i = 0; i < activeProjectsAccepting.length; i ++) {
			activeProjectsAccepting[i].style.display = 'none';
		}
		for (var i = 0; i < activeProjectsCreated.length; i ++) {
			activeProjectsCreated[i].style.display = 'none';
		}
		for (var i = 0; i < activeProjectsRejected.length; i ++) {
			activeProjectsRejected[i].style.display = 'none';
		}
	} else {
		for (var i = 0; i < activeProjectsAccepting.length; i ++) {
			activeProjectsAccepting[i].style.display = '';
		}
		for (var i = 0; i < activeProjectsCreated.length; i ++) {
			activeProjectsCreated[i].style.display = '';
		}
		for (var i = 0; i < activeProjectsRejected.length; i ++) {
			activeProjectsRejected[i].style.display = '';
		}
	} 
		
}

// Toggle projects that are just created and not submitted
function toggleShowCreated(){
	
	var activeProjects = document.getElementsByClassName('created');
	var checkBox = document.getElementById("notSubmittedCheckBox");
	
	if (checkBox.checked == true){
		for (var i = 0; i < activeProjects.length; i ++) {
			activeProjects[i].style.display = '';
		}
	} else {
		for (var i = 0; i < activeProjects.length; i ++) {
			activeProjects[i].style.display = 'none';
		}
	} 
		
}

// Toggle projects that are accepting applicants
function toggleAcceptingApplicants(){

	var activeProjects = document.getElementsByClassName('acceptingApplicants');
	var checkBox = document.getElementById("AcceptingApplicantsCheckBox");
	
	if (checkBox.checked == true){
		for (var i = 0; i < activeProjects.length; i ++) {
			activeProjects[i].style.display = '';
		}
	} else {
		for (var i = 0; i < activeProjects.length; i ++) {
			activeProjects[i].style.display = 'none';
		}
	} 
}

// Toggle projects that are pending approval
function togglePendingApproval(){

	var activeProjects = document.getElementsByClassName('pendingApproval');
	var checkBox = document.getElementById("PendingApprovalCheckBox");

	if (checkBox.checked == true){
		for (var i = 0; i < activeProjects.length; i ++) {
			activeProjects[i].style.display = '';
		}
	} else {
		for (var i = 0; i < activeProjects.length; i ++) {
			activeProjects[i].style.display = 'none';
		}
	} 
}

// Toggle projects that are rejected
function toggleRejected(){

	var activeProjects = document.getElementsByClassName('rejected');
	var checkBox = document.getElementById("RejectedCheckBox");

	if (checkBox.checked == true){
		for (var i = 0; i < activeProjects.length; i ++) {
			activeProjects[i].style.display = '';
		}
	} else {
		for (var i = 0; i < activeProjects.length; i ++) {
			activeProjects[i].style.display = 'none';
		}
	} 
}


// Toggle projects that are approved but unpublished
function toggleApprovedUnpublished(){

	var activeProjects = document.getElementsByClassName('approvedUnpublished');
	var checkBox = document.getElementById("ApprovedUnpublishedCheckBox");

	if (checkBox.checked == true){
		for (var i = 0; i < activeProjects.length; i ++) {
			activeProjects[i].style.display = '';
		}
	} else {
		for (var i = 0; i < activeProjects.length; i ++) {
			activeProjects[i].style.display = 'none';
		}
	} 
}
$('#ProjectsTable').DataTable({
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

