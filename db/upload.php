<?php
/****************************************************************************************
* File Name: upload.php
* Authors: Symon Ramos and Thien Nam
* Date: 3/3/19
* Description: This script is executed inside of ./pages/editProject.php to upload images into 
* the ./images/ directory. 
*****************************************************************************************/

//Note that all image names in the database are modified to be specific to the project. 
//project_$projectid_[image name]
if ($_POST['action'] == 'upload'){
	if(isset($_FILES['file'])){
		$errors= array();
		$file_name = $_FILES['file']['name'];
		$file_size = $_FILES['file']['size'];
		$file_tmp  = $_FILES['file']['tmp_name'];
		$file_type = $_FILES['file']['type'];
		
		$file_ext = strtolower(end(explode('.',$_FILES['file']['name'])));

		$dbfilename = $_POST['id'] . $file_name;
	
		if($file_size > (5 * 2097152)){
			echo 'File size must be under 10 MB';
		}
		if(empty($errors)==true){
			move_uploaded_file($file_tmp,"../images/".$dbfilename);
		}else{
			echo "Errors occurred!";
			exit();
		}
	}
}
?>