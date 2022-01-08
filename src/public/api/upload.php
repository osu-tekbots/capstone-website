<?php
/**
 * This api endpoint uploads files into the 'images/' directory. It is invoked from the 'editProject.php' page.
 */
include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use Model\CapstoneProjectImage;
use Util\Security;


//Image File Settings TODO: Need to move to general configuration files
	$max_width = 400; //Target width after resize
	$max_height = 400; //Target height after resize

/**
 * Simple function that allows us to respond with a response code and a message inside a JSON object.
 *
 * @param int  $code the HTTP status code of the response
 * @param string $message the message to send back to the client
 * @return void
 */
function respond($code, $message) {
    header('Content-Type: application/json');
    header("X-PHP-Response-Code: $code", true, $code);
    echo '{"message": "' . $message . '"}';
    die();
}

function correctImageOrientation($filename) {
  if (function_exists('exif_read_data')) {
    $exif = exif_read_data($filename);
    if($exif && isset($exif['Orientation'])) {
      $orientation = $exif['Orientation'];
      if($orientation != 1){
        $img = imagecreatefromjpeg($filename);
        $deg = 0;
        switch ($orientation) {
          case 3:
            $deg = 180;
            break;
          case 6:
            $deg = 270;
            break;
          case 8:
            $deg = 90;
            break;
        }
        return $deg;
      } // if there is some rotation necessary
    } // if have the exif orientation info
  } // if function exists   
	return 0;  
}

if ($_POST['action'] == 'deleteImage') {
	$dao = new CapstoneProjectsDao($dbConn, $logger);
	$id = $_POST['id'];
    
}

if ($_POST['action'] == 'uploadImage') {
    header('Content-Type: application/json');

    $dao = new CapstoneProjectsDao($dbConn, $logger);

    $id = $_POST['id'];
    if (empty($id)) {
        respond(400, "Must include ID of project in file upload request");
    }

    if (isset($_FILES['image'])) {
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp  = $_FILES['image']['tmp_name'];

        $supported_image = array(
            'gif',
            'jpg',
            'jpeg',
            'png'
        );
        $path_parts = pathinfo($file_name);
        $file_name = Security::HtmlEntitiesEncode($file_name);
        $extension = strtolower($path_parts['extension']);
       
        if(!in_array($extension, $supported_image))
        {
            respond(400, "Unsupported file type.");
        
        }

        if ($file_size > (5 * 2097152)) {
            respond(400, "File size must be less than 10MB");
        }
		
		switch(strtolower($_FILES['image']['type']))
		{
		case 'image/jpeg':
			if (!($image_file = imagecreatefromjpeg($_FILES['image']['tmp_name'])))
				respond(400, "Unsupported file type. Possible file extension mismatch");
			break;
		case 'image/png':
			if (!($image_file = imagecreatefrompng($_FILES['image']['tmp_name'])))
				respond(400, "Unsupported file type. Possible file extension mismatch");
			break;
		case 'image/gif':
			if (!($image_file = imagecreatefromgif($_FILES['image']['tmp_name'])))
				respond(400, "Unsupported file type. Possible file extension mismatch");
			break;
		default:
			respond(400, "Unsupported file type.");
		}
		
		
		$deg = correctImageOrientation($file_tmp);
		$image_file = imagerotate($image_file, $deg, 0); 
		
		$project = $dao->getCapstoneProject($id);
        // TODO: handle case when no project is found
		
		$newflag = false;
		if (count($project->getImages()) == 0){
			$image = new CapstoneProjectImage();
			$newflag = true;
		} else {
			$images = $project->getImages();
			$image = $images[0];
		}
		
		$imageId = $image->getId();

        if (count($project->getImages()) == 0) {
            $image->setIsDefault(true);
        }
        $image->setName($file_name)->setProject($project);
		
		// Get current dimensions
		$old_width  = imagesx($image_file);
		$old_height = imagesy($image_file);

		// Calculate the scaling we need to do to fit the image inside our frame
		$scale      = min($max_width/$old_width, $max_height/$old_height);

		// Get the new dimensions
		$new_width  = ceil($scale*$old_width);
		$new_height = ceil($scale*$old_height);

		// Create new empty image
		$new = imagecreatetruecolor($new_width, $new_height);

		// Resize old image into new
		imagecopyresampled($new, $image_file, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
		
		
		// Save the imagedata
		ob_start();
		$ok = imagejpeg($new, PUBLIC_FILES . '/images' . "/$imageId");
		$data = ob_get_clean();
		
		// Destroy resources
		imagedestroy($image_file);
		imagedestroy($new);
		
		if (!$ok) {
            respond(500, "Failed to upload the new image");
        }

        if ($newflag)
			$ok = $dao->addNewCapstoneProjectImage($image);
		else {
			$ok = $dao->updateCapstoneProjectImage($image);
			//$logger->warn("Image was uploaded with id '$imageId', but inserting metadata into the database failed");
            //respond(500, "An image already exists");
		}
			
        if (!$ok) {
            $logger->warn("Image was uploaded with id '$imageId', but inserting metadata into the database failed");
            respond(500, "Failed to upload the image");
        }

        respond(201, "Successfully uploaded an image");

    }
}
