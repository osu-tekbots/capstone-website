<?php
//FOR ADMIN USERS PAGE *CODE CALLED IN ../pages/adminUser.php
// UPDATES DATABASE WHEN CELLS ARE UPDATED ON THE TABLE
//SQL database configuration is in the ../database.ini file.




header('Content-Type: application/json');
// CHECK REQUEST METHOD
if ($_SERVER['REQUEST_METHOD']=='POST') {
	$input = filter_input_array(INPUT_POST);
} else {
	$input = filter_input_array(INPUT_GET);
}

$parsed_ini = parse_ini_file("../../database.ini");
$db_hostname = $parsed_ini['host'];
$db_username = $parsed_ini['user'];
$db_password = $parsed_ini['password'];
$db_name = $parsed_ini['db_name'];

$conn = mysqli_connect($db_hostname, $db_username, $db_password, $db_name) or die("Connection failed: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$input = filter_input_array(INPUT_POST);
if ($input['action'] == 'edit') {
	$update_field='';
	if(isset($input['u_fname'])) {
		$update_field.= "u_fname='".$input['u_fname']."'";
	} else if(isset($input['u_lname'])) {
		$update_field.= "u_lname='".$input['u_lname']."'";
	} else if(isset($input['u_onid'])) {
		$update_field.= "u_onid='".$input['u_onid']."'";
	} else if(isset($input['u_phone'])) {
		$update_field.= "u_phone='".$input['u_phone']."'";
	} else if(isset($input['u_major'])) {
		$update_field.= "u_major='".$input['u_major']."'";
	} else if(isset($input['u_ut_id'])) {
  		$update_field.= "u_ut_id='".$input['u_ut_id']."'";
	} else if(isset($input['u_email'])) {
		$update_field.= "u_email='".$input['u_email']."'";
  	} else if(isset($input['u_affiliation'])) {
		$update_field.= "u_affiliation='".$input['u_affiliation']."'";
  	} else if(isset($input['project_assigned'])) {
  		$update_field.= "project_assigned='".$input['project_assigned']."'";
  	}

	if($update_field && $input['u_id']) {
		$sql_query = "UPDATE user SET $update_field WHERE u_id='" . $input['u_id'] . "'";
		mysqli_query($conn, $sql_query) or die("database error:". mysqli_error($conn));
	}
}

mysqli_close($conn);
echo json_encode($input);
?>
