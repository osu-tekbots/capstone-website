
<?php
	function createNavbarBtn($path, $name){
		echo '<a href="' . $path . '"><button class="btn btn-outline-primary capstone-nav-btn" type="button"><h6>' . $name . '</h6></button></a>';
	}




?>

<nav class="navbar navbar-light navbarColor fixed-top navbarBrowser">
	<a class="navbar-brand" href="./"><h2 class="websiteTitle">Senior Design Capstone</h2></a>
	<form class="form-inline">
		<!-- All -->
		<?php createNavbarBtn("./browseProjects.php", "Browse Projects"); ?>

		<!-- All, Signed In -->
		<?php
			if(array_key_exists("userID",$_SESSION) && $_SESSION['userID'] != ''){
				createNavbarBtn("./myProjects.php", "My Projects");
				//Future Implementation @3/19/19 Release
				//We will be implementing student application functionality for the next release.
				createNavbarBtn("./myApplications.php", "My Applications");
				createNavbarBtn("./myProfile.php", "My Profile");
			}
		?>

		<!-- Admin -->
		<?php
			if(array_key_exists("accessLevel", $_SESSION) && $_SESSION['accessLevel'] == "Admin"){
				createNavbarBtn("./adminInterface.php", "Admin");
			}
		?>

		<!-- All -->
		<?php
			createNavbarBtn("./info.php", "Info");

			if(!array_key_exists("userID",$_SESSION) || $_SESSION['userID'] == ''){
				createNavbarBtn("./login.php", "Login");
			}
			else{
				createNavbarBtn("./login.php?provider=logout", "Logout");
			}
		?>

	</form>
</nav>
<!--
<nav class="navbar navbar-light navbarColor fixed-top navbarMobile">
<div class="pos-f-t">
  <nav class="navbar navbar-dark bg-dark navbarColor">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </nav>
  <div class="collapse" id="navbarToggleExternalContent">
    <div class="bg-dark navbarColor p-4">
      <h5 class="h4">Collapsed content</h5>
      <span class="text-muted" style="float:right;">Toggleable via the navbar brand.</span>
    </div>
  </div>

</div>
</nav>
--->

<br>
