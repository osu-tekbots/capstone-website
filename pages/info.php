<?php
include_once PUBLIC_FILES . '/modules/header.php';
?>
<br />
<br />
<div class="container">
	<div class="row">
		<div class="col-sm-6">
			<div id="description1div">
				<h1><img src="assets/img/light.png" alt="icon" width="70px" height="70px" />What is Capstone?</h1>
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
				<h2><img src="assets/img/light.png" alt="icon" width="70px" height="70px" />What projects are available?
				</h2>
				<hr>
				Senior Design Capstone hosts many appealing projects tailored for multiple majors ranging from
				Web Application Development, Embedded Programming, Virtual Reality, Artificial Intelligence,
				and Automation. <a href="./browseProjects.php" target="_blank">Click here</a> to browse projects now!
			</div>
			<br><br>
			<div id="description3div">
				<?php
				// Only show this section if the user is not logged in
				if (!(array_key_exists('userID',$_SESSION) && $_SESSION['userID'] != '')): ?>
					<h2><img src="assets/img/light.png" alt="icon" width="70px" height="70px"/>How do I get involved?</h2>
					<hr/>
					Students and potential proposers can register for an account by 
					<a href="./login.php" target="_blank">clicking here</a>
				<?php endif; ?>
			</div>
		</div>

	</div>
	<div class="row">
		<div class="col-sm-4">
		</div>
		<div class="col-sm-8">
		</div>
	</div>
</div>


<?php 
include PUBLIC_FILES . '/modules/newProjectModal.php';
include_once PUBLIC_FILES . '/modules/footer.php'; 
?>
