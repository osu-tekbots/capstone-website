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
			//$isProposer will dictate whether the Edit button will appear 
			//(for students) or the Review button (for proposers).
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
				foreach ($rows as $row): 
					$appID = $row['application_id'];
					
					$appReviewResult = getApplicationReviewEntry($appID);
					$appReviewRow = $appReviewResult->fetch_assoc();
					
					$interestLevel = $appReviewRow['interest_level'];
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
					if ($strDate == '0000-00-00 00:00:00') {
						$dateUpdated = 'N/A';
					} else {
						$dateUpdated = date('m-d-Y h:i a', strtotime($strDate));
					}
					$strDate = $row['last_updated'];
					if ($strDate == '0000-00-00 00:00:00') {
						$dateApplied = 'Not Submitted';
					} else {
						$dateApplied = date('m-d-Y h:i a', strtotime($strDate));
					}
					
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