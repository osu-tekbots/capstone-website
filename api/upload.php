<?php
use DataAccess\CapstoneProjectsDao;
use Model\CapstoneProjectImage;

/**
 * This api endpoint uploads files into the 'images/' directory. It is invoked from the 'editProject.php' page.
 */
if ($_POST['action'] == 'uploadImage') {
    header('Content-Type: application/json');

    $dao = new CapstoneProjectsDao($dbConn, $logger);

    $id = $_POST['id'];
    if (empty($id)) {
        http_response_code(400);
        echo '{"message": "Must include ID of project in file upload request"}';
        die();
    }

    if (isset($_FILES['image'])) {
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp  = $_FILES['image']['tmp_name'];
	
        if ($file_size > (5 * 2097152)) {
            http_response_code(400);
            echo '{"message": "File size must be less than 10MB"}';
            die();
        }
	
        $project = $dao->getCapstoneProject($id);
        // TODO: handle case when no project is found

		$image = new CapstoneProjectImage();
		$imageId = $image->getId();

        if (count($project->getImages()) == 0) {
            $image->setIsDefault(true);
        }
        $image->setName($file_name)->setProject($project);

        $ok = move_uploaded_file($file_tmp, PUBLIC_FILES . '/images' . "/$imageId");

        if (!$ok) {
            http_response_code(500);
			echo '{"message": "Failed to upload the new image"}';
			die();
        }

        $ok = $dao->addNewCapstoneProjectImage($image);
        if (!$ok) {
            $logger->warning("Image was uploaded with id '$iid', but inserting metadata into the database failed");
            http_response_code(500);
			echo '{"message": "Failed to upload the new image"}';
			die();
        }

        http_response_code(201);
        echo '{"message": "Successfully uploaded a new image"}';
        die();
    }
}
