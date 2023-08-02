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
					Students in the <a href="https://eecs.oregonstate.edu">School of Electrical Engineering and Computer Science</a> at <a href="https://www.oregonstate.edu">Oregon State University</a> engage in a 
					multitude of projects as part of their education and extracurricular activities. If you have a project idea that you would love to see become a reality we encourage you to become a 
					Project Partner and submit your idea.
				</p>
				<a href='/capstone/submission/pages/login.php'><h3>Submit a Project (Login Required) >>></h3></a>
			</div>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-sm-6">
			<div id="description1div">
				<h2>What types of projects should I propose?</h2>
				<hr>
				<p>We look for projects with most of the work involving programming or electronics. We also look for projects outside of electrical engineering and computer science that require a technical team. 
				If you have an idea or strong interest in a project, we suggest you submit it so we can talk more.</p>
				<strong>Project Options:</strong>
				<ul>
				<li><b>CS and ECE Capstone:</b> 9-month cycle starting once a year from September - June with teams of 3-4 students dedicated to your project.</li>
				<li><b>CS Post-Bacc Capstone:</b> 3-month cycle starting every quarter (September, January, March, and June) with teams of 2-3 students dedicated to your project.</li>
				<li><b>Open Project:</b> Any student can apply. You choose the team, how long the project will last, and the scope of work.</li>
				</ul>
			</div>
		</div>
		<div class="col-sm-6">
			<div id="description2div">
				<h2>What happens after I submit a project?
				</h2>
				<hr>
				<p>Once you submit your project idea, it becomes available for many people to review. Our most active projects occur via the 
				<a href="https://eecs.oregonstate.edu/industry-relations/capstone-and-senior-design-projects">Capstone courses</a> in both ECE or CS. Other projects, though, might fit our Juniors 
				design classes or benefit an individual student's project interests.</p>
				<p>One of our staff will reach out to you before your project is made public to make sure of all the details.</p>
			</div>
			<br><br>
			<div id="description3div">
				<h2>Project Partner Expectations</h2>
				<hr>
				<ul><li>Once your project is accepted and a group begins work, you will need to spend an hour per week mentoring your team. This minimum time commitment helps teams make progress and better achieve goals.</li>
					<li>Based on your project, your student team may need extra materials purchased. These purchases are handled in different ways based on the project and the program it runs through. We will discuss the options in more detail when we contact you.</li>
					<li>Should this be a sponsored project, kindly indicate that when submitting your project.</li>
					<li>Students own any IP resulting from the project. If your organization would like to retain IP rights, indicate that when submitting your project. We may ask you to complete an NDA.</li>
					<li>Remember, our faculty facilitate educational activities that our students are paying for. They are not employees, and we cannot guarantee a functioning product, as this is an educational experience. While there may be many positive outcomes from your involvement, the students are still learning. For many, this is their first exposure working with industry.</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="row" style="padding-top:3em;"></div>
</div>

<?php 
include PUBLIC_FILES . '/modules/newProjectModal.php';
include_once PUBLIC_FILES . '/modules/footer.php'; 
?>
