<?php
include_once '../bootstrap.php';

include_once PUBLIC_FILES . '/modules/header.php';
?>

<!-- Hero Banner -->
<div class="bg-primary py-5 mb-5">
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
</div>

<div class="container">
	<div class="row">
		<div class="col-sm-6">
			<div id="description1div">
				<h1><img src="assets/img/light.png" alt="icon" width="70px" height="70px" />What is Capstone?</h1>
				<hr>
				Organized by the <a href="http://eecs.oregonstate.edu">School of Electrical Engineering and Computer Science</a> at <a href="http://www.oregonstate.edu">Oregon State University</a>,
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
				<h2><img src="assets/img/light.png" alt="icon" width="70px" height="70px" />What projects are available?
				</h2>
				<hr>
				Senior Design Capstone hosts many appealing projects tailored for multiple majors ranging from
				Web Application Development, Embedded Programming, Virtual Reality, Artificial Intelligence,
				and Automation. <a href="./pages/browseProjects.php" target="_blank">Click here</a> to browse projects now!
			</div>
			<br><br>
			<div id="description3div">
				<h2><img src="assets/img/light.png" alt="icon" width="70px" height="70px" />How do I get involved?</h2>
				<hr>
				Students and potential proposers can register for an account by <a href="./pages/login.php"
					target="_blank">clicking
					here</a>.
			</div>
		</div>

	</div>
</div>

<?php 
include PUBLIC_FILES . '/modules/newProjectModal.php';
include_once PUBLIC_FILES . '/modules/footer.php'; 
?>