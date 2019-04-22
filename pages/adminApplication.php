<!DOCTYPE html>
<html lang="en">
<head>


	<?php include_once('../includes/header.php'); ?>

	<!-- Custom styles for this template-->
	<link href="../assets/css/sb-admin.css" rel="stylesheet">
	<link href="../assets/css/sb-admin.min.css" rel="stylesheet">
	<title>Admin Control Users</title>
</head>

<?php require_once('../db/dbManager.php'); ?>
<?php require_once('../modules/createCards.php'); ?>
<?php include_once PUBLIC_FILES . '/db/dbManager.php' ?>
<?php include_once PUBLIC_FILES . '/modules/redirect.php' ?>
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



			<?php 
			/*********************************************************************************
			* Function Name: createApplicationTable($rows, $isProposer)
			* Input: $rows is an array of objects from the 'users_application' table. $isProposer
			* indicates whether or not the user is a proposer - this will dictate certain columns 
			* in the table in addition to the button displayed.
			* Output: Creates an application table in the user interface. Note that multiple application 
			* tables can exists and this function is used in both the Student and Proposer interfaces.
			*********************************************************************************/
			function createApplicationTable($rows, $isProposer){	
				
				echo '
				<div class="row">
					<div class="col">
				';
				
				if($isProposer){
					echo '<h3>' . $rows[0]['title'] . '</h3>';
				}
				
				echo '
						<table class="table">
							<thead>
				';
				
				//Create table column headers based on the user's access level.
				if($isProposer){
					echo '<th>Application ID</th>';
					echo '<th>Applicant</th>';
					echo '<th>Reviewed?</th>';
					echo '<th>Interest Level</th>';
				}
				else{
					echo '<th>Project Name</th>';
					echo '<th>Status</th>';
				}

				echo '
								<th>Start Date</th>
								<th>Updated</th>
								<th></th>
							</thead>
							<tbody>
				';
				
				//Iterating through every single application associated with this specific project...
				foreach ($rows as $row): 
					$appID = $row['application_id'];
					
					//Gather relevant application review data.
					$appReviewResult = getApplicationReviewEntry($appID);
					$appReviewRow = $appReviewResult->fetch_assoc();
					
					//Possible interest levels include "Desirable", "Impartial", and "Undesirable" as of 4/15/19.
					$interestLevel = $appReviewRow['interest_level'];
					//The interestLevel must be selected for an application to have been reviewed.
					$isReviewed = $interestLevel != '' ? "Yes" : "No";
				
					if($isProposer){
						$title = 'Application ' . $appID;
						//Display the name of the applicant for proposers.
						$name = $row['first_name'] . ' ' . $row['last_name'];
					}
					else{
						//This will be the name of the project.
						$title = $row['title'];
						//This will show whether or not the student's application 
						//has been created or submitted.
						$status = $row['appstatus'];
					}

					$applicationId = $row['application_id'];
					$strDate = $row['last_updated'];
					
					//Handle invalid dates.
					if ($strDate == '0000-00-00 00:00:00') {
						$dateUpdated = 'N/A';
					} else {
						$dateUpdated = date('m-d-Y h:i a', strtotime($strDate));
					}
					
					$strDate = $row['date_applied'];
					
					if ($strDate == '0000-00-00 00:00:00') {
						$dateApplied = 'Not Submitted';
					} else {
						$dateApplied = date('m-d-Y h:i a', strtotime($strDate));
					}
					
					//Generate table rows for each application.
					echo '<tr>';
					echo '<td>' . $title . '</td>';
					
					if($isProposer){
						echo '<td>' . $name . '</td>';
						echo '<td>' . $isReviewed . '</td>';
						echo '<td>' . $interestLevel . '</td>';
					}
					else{
						echo '<td>' . $status . '</td>';
					}
					
					echo '
							<td>' . $dateApplied . '</td>
							<td>' . $dateUpdated . '</td>';
					
					echo '<td>';
					
					if($isProposer){		
						echo '<a class="btn btn-outline-primary" href="./reviewApplication.php?id=' . $applicationId . '">Review</a>';
					}
					//Student view
					else{
						echo '<a class="btn btn-outline-success" href="./editApplication.php?id=' . $applicationId . '">Edit</a>';
					}
					echo '		
							</td>
						</tr>
						';
					endforeach;
					echo '
								</tbody>
							</table>
						</div>
					</div>
					';
			}
		?>

		<?php 
		
		function createProposerInterface(){
			$projectResult = getAllProjects();

			while($projectRow = $projectResult->fetch_assoc()){

				$applicationResult = getApplicationsAssociatedWithProject($projectRow['project_id']);
				$applicationRows = array();
				while ($tmp = $applicationResult->fetch_assoc()) {
					$applicationRows[] = $tmp;
				}

				//Omit projects that don't have any applications.
				if(count($applicationRows) > 0){
					createApplicationTable($applicationRows, true);
				}
			}
		}
		
		function createStudentInterface(){
			$result = getMyApplications($_SESSION['userID']);
			$rows = array();
			while ($tmp = $result->fetch_assoc()) {
			    $rows[] = $tmp;
			} 
			createApplicationTable($rows, false);
		}
		
		//Different interfaces are displayed depending on user's access level.
		switch($_SESSION['accessLevel']){
			case 'Proposer': 
				createProposerInterface();
				break;
			case 'Student':
				createStudentInterface();
				break;
			case 'Admin': 
				echo '<h2>Your Proposer Interface:</h2><br><br>';
				createProposerInterface();
				echo '<br><br><h2>Your Student Interface:</h2><br><br>';
				createStudentInterface();
				break;
			default: 
				break;
		}
		
		echo '</div>';
		?>
	
	
	</div>

	







	<?php include_once("../modules/footer.php"); ?>

</body>

<script type="text/javascript">

</script>


</html>
