<!DOCTYPE html>
<html lang="en">

<head>
	<?php include_once PUBLIC_FILES . '/includes/header.php'; ?>
	<title>My Applications</title>
</head>

<?php include_once PUBLIC_FILES . '/db/dbManager.braden.php'; ?>
<?php include_once PUBLIC_FILES . '/modules/redirect.php'; ?>

<body>
	<?php include_once '../modules/navbar.php'; ?>

	<div class="container-fluid">
		<br>
		<h1>My Applications</h1>

		<?php if ($_SESSION['accessLevel'] == 'Proposer'): ?>

		<?php else: ?>

		<?php
			$result = getMyApplications($_SESSION['userID']);
			$rows = array();
			while ($tmp = $result->fetch_assoc()) {
			    $rows[] = $tmp;
			} ?>

		<div class="row">
			<div class="col">
				<table class="table">
					<thead>
						<th>Project</th>
						<th>Status</th>
						<th>Start Date</th>
						<th>Updated</th>
						<th></th>
					</thead>
					<tbody>
						<?php foreach ($rows as $row): ?>
						<?php
							$projectId = $row['project_id'];
							$projectName = $row['title'];
							$status = $row['status'];
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
							$title = $row['title']; ?>
						<tr>
							<td><?php echo $projectName ?></td>
							<td><?php echo $status ?></td>
							<td><?php echo $dateApplied ?></td>
							<td><?php echo $dateUpdated ?></td>
							<td><a href="./editApplication.php?id=<?php echo $applicationId; ?>">Edit</a></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

		<?php endif; ?>

	</div>
	<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>
</body>

</html>