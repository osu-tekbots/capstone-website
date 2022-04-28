<div class="modal fade" id="newCourseModal">
    <br><br><br><br>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Create New Course</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                    <input id="courseCodeInput" class="form-control form-control-lg" type="text" placeholder="Course Code...">
                    <br>
                    <input id="courseNameInput" class="form-control form-control-lg" type="text" placeholder="Course Name...">
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" id="createCourseBtn" onclick="createCourse()">Create Course</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    //$('#createCourseBtn').on('click', function () {
    // function createCourse() {
    //     console.log("yo");
    //     // Capture the data we need
    //     let courseName = $('courseNameInput').val();
    //     let courseCode = $('courseCodeInput').val();
    //     courseCode = courseCode.replace(/\s/g, '');

    //     let data = {
    //         action: 'createCourse',
    //         name: courseName,
    //         code: courseCode
    //     };

    //     // Send our request to the API endpoint
    //     api.post('/courses.php', data).then(res => {
    //         window.location.reload();
    //         snackbar(res.message, 'success');
    //     }).catch(err => {
    //         snackbar(err.message, 'error');
    //     });
    // };

	$(document).ready(function() {
	  $('#newCourseModal').on('shown.bs.modal', function() {
		$('#courseCodeInput').trigger('focus');
        $('#courseNameInput').trigger('focus');
	  });
	  $('#newCourseModal').on('hide.bs.modal', function() {
		$('#courseCodeInput').val('');
        $('#courseNameInput').val('');
	  });
	});
</script>