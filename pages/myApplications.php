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
								<th>
				';
				
				if($isProposer){
					echo 'Application Name';
				}
				else{
					echo 'Project Name';
				}
				
				echo '
								</th>
					';
				
				if($isProposer){
					echo '<th>Applicant</th>';
				}
				else{
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
					$projectId = $row['project_id'];
					
					if($isProposer){
						$firstColumnInfo = 'Application ' . $row['project_id'];
					}
					else{
						//This will be the name of the project.
						$firstColumnInfo = $row['title'];
					}
					
					if($isProposer){
						//Display the name of the applicant for proposers.
						$secondColumnInfo = $row['first_name'] . ' ' . $row['last_name'];
					}
					else{
						//This will show whether or not the student's application 
						//has been created or submitted.
						$secondColumnInfo = $row['status'];
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
					$title = $row['title']; 
					echo '
						<tr>
							<td>' . $firstColumnInfo . '</td>
							<td>' . $secondColumnInfo . '</td> 
							<td>' . $dateApplied . '</td>
							<td>' . $dateUpdated . '</td>
							<td>';
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
		//FIXME FIXME FIXME: change "!=" to "=="
		if ($_SESSION['accessLevel'] != 'Proposer'){ 
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
		else{ 
			$result = getMyApplications($_SESSION['userID']);
			$rows = array();
			while ($tmp = $result->fetch_assoc()) {
			    $rows[] = $tmp;
			} 
			createApplicationTable($rows, false);
		}
		
		echo '</div>';
		?>
	
	<?php include_once PUBLIC_FILES . '/modules/footer.php' ?>
</body>

</html>