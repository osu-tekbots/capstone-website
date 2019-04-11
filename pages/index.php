<!DOCTYPE html>
<html lang="en">
<!-- **********HOME PAGE************ -->
<head>
	<?php include_once('../includes/header.php'); ?>
	<title> Senior Design Capstone</title>
</head>

<?php require_once('../db/dbManager.php'); ?>


<body>
	<?php include_once("../modules/navbar.php"); ?>
	  <!-- Header -->
	  <header class="bg-primary py-5 mb-5">
		<div class="container h-100">
		  <div class="row h-100 align-items-center">
			<div class="col-lg-12">
			  <h1 class="display-4 text-white mt-5 mb-2 homePageText">Driven By Ideas</h1>
			  <p class="lead mb-5">
			The Senior Design Capstone program provides students with the opportunity
			to implement exciting, creative, and high-impact solutions to real world problems.

			  </p>
			</div>
		  </div>
		</div>
	  </header>

	  <div class="container">
		  <div class="row">
			<div class="col-sm-6">
				<div id="description1div">
					<h1><img src="../images/light.png" alt="icon" width="70px" height="70px"/>What is Capstone?</h1>
					<hr>
					Organized by the College of Electrical Engineering and Computer Science at Oregon State University,
					the Senior Design Capstone program is a year-long course where students are presented with various
					opportunities to design, implement, and promote unique and engaging projects sponsored by companies,
					businesses, and proposers/student clubs with personal projects.
					<br><br>
					This application enables proposers to submit their projects for students to view. Students can apply
					to projects to convey interest and connect with proposers as well.
				</div>
				<br><br>


				<br><br>
				<br><br>
			</div>
			<div class="col-sm-6">
				<div id="description2div">
					<h2><img src="../images/light.png" alt="icon" width="70px" height="70px"/>What projects are available?</h2>
					<hr>
					Senior Design Capstone hosts many appealing projects tailored for multiple majors ranging from
					Web Application Development, Embedded Programming, Virtual Reality, Artificial Intelligence,
					and Automation. <a href="./browseProjects.php" target="_blank">Click here</a> to browse projects now!
				</div>
				<br><br>
				<div id="description3div">
					<h2><img src="../images/light.png" alt="icon" width="70px" height="70px"/>How do I get involved?</h2>
					<hr>
					Students and potential proposers can register for an account by <a href="./login.php" target="_blank">clicking here</a>.
				</div>
			</div>

		  </div>
	  </div>
	  
	  <!-- 4/1/19 Testing Code for the default image selector, i want to talk about it with Don first -->
	  <!--
	  
	  			<select class="image-picker show-html">
			  <option data-img-src="../images/1.jpg" data-img-class="first" data-img-alt="Page 1" value="1">  Page 1  </option>
			  <option data-img-src="../images/capstone.jpg" data-img-alt="Page 2" value="2">  Page 2  </option>
			  <option data-img-src="../images/light.jpg" data-img-alt="Page 12" data-img-class="last" value="12"> Page 12 </option>
			</select>
			
			<br><br><br><br>

			<script type="text/javascript">
			$("select").imagepicker();
			</script>
			
			-->

	<?php include "../modules/newProjectModal.php"; ?>
	<?php include_once("../modules/footer.php"); ?>

</body>

</html>
