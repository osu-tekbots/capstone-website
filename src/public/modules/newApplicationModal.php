<?php
include_once '../bootstrap.php';
use Util\Security;
use DataAccess\CapstoneApplicationsDao;

include_once PUBLIC_FILES . '/modules/button.php';



/**
 * Renders the HTML for the modal that will start the application process for a student applying to a capstone
 * project.
 *
 * @param \Model\CapstoneProject $project
 * @return void
 */
function renderNewApplicationModal($project) {
    $pTitle = Security::HtmlEntitiesEncode($project->getTitle());
    $pDescription = Security::HtmlEntitiesEncode($project->getDescription());
	$pNdaIp = $project->getNdaIp()->getName();
		
    echo "
		<div class='modal fade' id='newApplicationModal'>
			<br><br><br><br>
		<div class='modal-dialog modal-lg'>
			<div class='modal-content'>

					<!-- Modal Header -->
					<div class='modal-header'>
					<h4 class='modal-title'>Create New Application for Project</h4>
					<button type='button' class='close' data-dismiss='modal'>&times;</button>
					</div>
		
					<!-- Modal body -->
					<div class='modal-body'>
						<h4 id='projectNameApplicationHeader'>$pTitle</h4>
						<p>$pDescription</p>
						<br>
						<h6 class='text-secondary'>$pNdaIp</h6>
					</div>

					<!-- Modal footer -->
					<div class='modal-footer'>
					<button type='button' class='btn btn-success' data-dismiss='modal' id='createApplicationBtn'>Apply</button>
					<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
					</div>

				</div>
			</div>
		</div>
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#newApplicationModal').on('shown.bs.modal', function() {
				//$('#projectTitleInput').trigger('focus');
				});
				$('#newApplicationModal').on('hide.bs.modal', function() {
				//$('#projectTitleInput').val('');
				});
			});
		</script>
	";
}
