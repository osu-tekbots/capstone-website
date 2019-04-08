<?php
if(!array_key_exists("userID",$_SESSION) || $_SESSION['userID'] == '' ){
	echo('<script type="text/javascript">alert("You are not authorized to be here!")</script>');
	header("Location: ./index.php"); /* Redirect Browser */
}
?>
