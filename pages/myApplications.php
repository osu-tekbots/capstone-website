<!DOCTYPE html>
<html lang="en">

<head>
	<?php include_once PUBLIC_FILES . '/includes/header.php' ?>
	<title>My Applications</title>
</head>

<?php include_once PUBLIC_FILES . '/db/dbManager.php' ?>
<?php include_once PUBLIC_FILES . '/modules/redirect.php' ?>

<body>
	<?php include_once '../modules/navbar.php' ?>

	<div class="container-fluid">
		<br>
		<h1>My Applications</h1>
		<br>
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
					echo '<th>Application Name</th>';
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
			$projectResult = getMyProjects();

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
				echo '<br><br><h2>Your Student Interfae:</h2><br><br>';
				createStudentInterface();
				break;
			default: 
				break;
		}
		
		echo '</div>';
		?>
	
	<?php include_once PUBLIC_FILES . '/modules/footer.php' ?>
</body>

</html>