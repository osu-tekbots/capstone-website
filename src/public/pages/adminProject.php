<?php
include_once '../bootstrap.php';

/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */

use DataAccess\CapstoneProjectsDao;
use DataAccess\KeywordsDao;

session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$isAdmin = isset($_SESSION['userID']) && !empty($_SESSION['userID']) 
	&& isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

$userId = $_SESSION['userID'];

allowIf($isAdmin);

$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);

$projects = $projectsDao->getCapstoneProjectsForAdmin();
$types = $projectsDao->getCapstoneProjectTypes();
$categories = $projectsDao->getCapstoneProjectCategories();
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
	
	
	/*********************************************************************************
    * Function Name: categoryChange(id)
    * Description: Updates the category of a project using onchange() for a displayed project.
    *********************************************************************************/
    function categoryChange(id) {
		// Grab the ID of the selected element
		var childID = $('#categoryselect'+id).children(":selected").attr("value");
		
//		alert(childID);
		
		let body = {
			action: 'updateCategory',
			categoryId: childID,
			projectId: id
		}

		api.post('/projects.php', body).then(res => {
			snackbar(res.message, 'success');
		}).catch(err => {
			snackbar(err.message, 'error');
		});
		
	}

    $(document).ready(function(){

		//As each letter is typed in filterInput, filtering of cards will occur.
		//For drop down lists, like filtering by key word, filterInput is programmically
		//filled and keydown behavior is explicitly called.
		$("#filterInput").keydown(function(){
			var value = $(this).val().toLowerCase();
			var projects = document.getElementById($('[id^=editDialog]'));
			
			$('[id^=projectCard]').each(function() {
				if($(this).text().toLowerCase().indexOf(value) > -1){
					$(this).show();
				}
				else 
					$(this).hide();
				
			});
		});
		
/*		$("#filterInput").keydown(function(){
			var value = $(this).val().toLowerCase();

			for(var i = 0; i < <?php echo $CardCount; ?>; i++){
				if($("#projectCard" + i).text().toLowerCase().indexOf(value) > -1){
					$("#projectCard" + i).show();
				}
				else{
					$("#projectCard" + i).hide();
				}
			}
		});
*/
		 $("#ApprovalRequiredCheckBox").change(function(){
			if($(this).is(":checked")){
				for(var i = 0; i < <?php echo $CardCount; ?>; i++){
					if(($("#projectCard" + i).text().toLowerCase().indexOf("category placement") <= -1) && ($("#projectCard" + i).text().toLowerCase().indexOf("pending approval") <= -1)) {
						$("#projectCard" + i).hide();
					}
				}

			}
			 else{
				for(var i = 0; i < <?php echo $CardCount; ?>; i++){
					$("#projectCard" + i).show();
				}
			 }
		});

		$("#notSubmittedCheckBox").change(function(){
			if($(this).is(":checked")){
				for(var i = 0; i < <?php echo $CardCount; ?>; i++){
					if($("#projectCard" + i).text().toLowerCase().indexOf("not yet submitted") > -1)  {
						$("#projectCard" + i).hide();
					}
				}
			 }
			 else{
				for(var i = 0; i < <?php echo $CardCount; ?>; i++){
					$("#projectCard" + i).show();
				}
			 }
		});

			
		// Automatically check the Hide Projects that do NOT need Admin Approval Checkbox and trigger ajax
	//	$('#ApprovalRequiredCheckBox').prop('checked', true).change();
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

		<!--
					<div class="col-sm-3">
						<div class="form-group">
							<label for="projectShowSelect">Show..</label>
							<select class="form-control" id="projectShowSelect" onchange="showAdminNeeded()">
								<option></option>
								<option>Admin Required</option>
								<option>Approved Projects</option>
								<option>Not Yet Submitted</option>
								<option>Archived</option>

							</select>
						</div>
					</div>

					

							
-->

		<div class="row">
		<div class="col-sm-12">
            <div class="row">
                <div class="col-sm-4">
                    <h2>Search and Filter</h2>
<!--					<input class="form-control" id="filterInput" type="text" placeholder="Search..." />
                    <br />
-->

<!-- CHECKBOX HIDE IF PROJECTS REQUIRE NDA NOT FUNCTIONING
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="NDAFilterCheckBox" />
                        <label for="NDAFilterCheckBox">Hide projects that require an NDA/IP</label>
                    </div>
-->
                </div>


                <div class="col-sm-4">				
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="ApprovalRequiredCheckBox" onchange="toggleAdminNeeded();">
							<label for="ApprovalRequiredCheckBox">Hide projects do NOT need Admin Action</label>
						</div>


						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="notSubmittedCheckBox" onchange="toggleShowCreated();" checked>
							<label for="notSubmittedCheckBox">Show Not-Submitted projects</label>
						</div>

				</div>
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
			<tbody>
				<?php renderAdminProjectCardGroup2($projects, $keywordsDao, $types, $categories, $statuses, false);?>
			</tbody>
		</table>

</div>




<script>
function toggleAdminNeeded(){
	
	var archivedItems = document.getElementsByClassName('adminneeded');
	var checkBox = document.getElementById("ApprovalRequiredCheckBox");
	
	if (checkBox.checked == true){
		for (var i = 0; i < archivedItems.length; i ++) {
			archivedItems[i].style.display = 'none';
		}
	} else {
		for (var i = 0; i < archivedItems.length; i ++) {
			archivedItems[i].style.display = '';
		}
	} 
		
}

function toggleShowCreated(){
	
	var archivedItems = document.getElementsByClassName('createdonly');
	var checkBox = document.getElementById("notSubmittedCheckBox");
	
	if (checkBox.checked == true){
		for (var i = 0; i < archivedItems.length; i ++) {
			archivedItems[i].style.display = '';
		}
	} else {
		for (var i = 0; i < archivedItems.length; i ++) {
			archivedItems[i].style.display = 'none';
		}
	} 
		
}

$('#ProjectsTable').DataTable({
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

