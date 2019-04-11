<?php
//FOR ADMIN USERS PAGE *CODE CALLED IN ../pages/adminUser.php
// UPDATES DATABASE WHEN CELLS ARE UPDATED ON THE TABLE
//SQL database configuration is in the config.php file.
include_once('../includes/config.php');

$conn = mysqli_connect($db_hostname, $db_username, $db_password, $db_name) or die("Connection failed: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$input = filter_input_array(INPUT_POST);
if ($input['action'] == 'edit') {
	$update_field='';
	if(isset($input['first_name'])) {
		$update_field.= "first_name='".$input['first_name']."'";
	} else if(isset($input['last_name'])) {
		$update_field.= "last_name='".$input['last_name']."'";
	} else if(isset($input['student_id'])) {
		$update_field.= "student_id='".$input['student_id']."'";
	} else if(isset($input['phone'])) {
		$update_field.= "phone='".$input['phone']."'";
	} else if(isset($input['major'])) {
		$update_field.= "major='".$input['major']."'";
	}	else if(isset($input['type'])) {
  	$update_field.= "type='".$input['type']."'";
  } else if(isset($input['project_assigned'])) {
  		$update_field.= "project_assigned='".$input['project_assigned']."'";
  }

	if($update_field && $input['user_id']) {
		$sql_query = "UPDATE users SET $update_field WHERE user_id='" . $input['user_id'] . "'";
		mysqli_query($conn, $sql_query) or die("database error:". mysqli_error($conn));
	}
}
?>
