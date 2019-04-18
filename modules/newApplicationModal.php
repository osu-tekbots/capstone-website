<div class="modal fade" id="newApplicationModal">
    <br><br><br><br>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			  <!-- Modal Header -->
			  <div class="modal-header">
				<h4 class="modal-title">Create new application for...</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			  </div>


	
			  <!-- Modal body -->
			  <div class="modal-body">
					<h4 id="projectNameApplicationHeader">
						<?php 
							$result = getSingleProject($projectID);
							$row = $result->fetch_assoc();
							echo $row['title']; 
						?>
					</h4>
					<p>
						<?php
							echo $row['description'];
							
							
						?>
					</p>
					<br>
					<h6 class="text-secondary"><?php echo $row['NDA/IP'];?></h6>
			  </div>

			 <!-- Modal footer -->
			  <div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal" id="createApplicationBtn">Create</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			  </div>

		</div>
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function() {
	  $('#newApplicationModal').on('shown.bs.modal', function() {
		//$('#projectTitleInput').trigger('focus');
	  });
	  $('#newApplicationModal').on('hide.bs.modal', function() {
		//$('#projectTitleInput').val('');
	  });
	});
</script>