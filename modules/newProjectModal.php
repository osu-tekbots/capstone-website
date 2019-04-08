<div class="modal fade" id="newProjectModal">
    <br><br><br><br>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			  <!-- Modal Header -->
			  <div class="modal-header">
				<h4 class="modal-title">Create New Project</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			  </div>

			  <!-- Modal body -->
			  <div class="modal-body">
					<input id="projectTitleInput" class="form-control form-control-lg" type="text" placeholder="Your amazing project name goes here...">
			  </div>

			 <!-- Modal footer -->
			  <div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal" id="createProjectBtn">Create Project</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			  </div>

		</div>
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function() {
	  $('#newProjectModal').on('shown.bs.modal', function() {
		$('#projectTitleInput').trigger('focus');
	  });
	  $('#newProjectModal').on('hide.bs.modal', function() {
		$('#projectTitleInput').val('');
	  });
	});
</script>