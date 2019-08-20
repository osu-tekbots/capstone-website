<?php
namespace Api;

use Model\CapstoneProject;
use Model\CapstoneProjectStatus;
use Model\Keyword;

/**
 * Defines the logic for how to handle AJAX requests made to modify project information.
 */
class ProjectsActionHandler extends ActionHandler {

    /** @var \DataAccess\CapstoneProjectsDao */
    private $projectsDao;
    /** @var \DataAccess\UsersDao */
    private $usersDao;
    /** @var \DataAccess\KeywordsDao */
    private $keywordsDao;	
    /** @var \Email\ProjectMailer */
    private $mailer;
    /** @var \Util\ConfigManager */
    private $config;

    /**
     * Constructs a new instance of the action handler for requests on project resources.
     *
     * @param \DataAccess\CapstoneProjectsDao $projectsDao the data access object for projects
     * @param \DataAccess\CapstoneProjectsDao $usersDao the data access object for users
     * @param \Email\ProjectMailer $mailer the mailer used to send project related emails
     * @param \Util\ConfigManager $config the configuration manager providing access to site config
     * @param \Util\Logger $logger the logger to use for logging information about actions
     */
    public function __construct($projectsDao, $usersDao, $keywordsDao, $mailer, $config, $logger) {
        parent::__construct($logger);
        $this->projectsDao = $projectsDao;
        $this->usersDao = $usersDao;
		$this->keywordsDao = $keywordsDao;
        $this->mailer = $mailer;
        $this->config = $config;
    }

    /**
     * Creates a new capstone project entry in the database.
     *
     * @return void
     */
    public function handleCreateProject() {
        // Ensure all the requred parameters are present
        $this->requireParam('uid');
        $this->requireParam('title');

        $body = $this->requestBody;

        $user = $this->usersDao->getUser($body['uid']);
        // TODO: handle case when user is not found

        $project = new CapstoneProject();
        $project->setTitle($body['title']);
        $project->setProposer($user);

        $ok = $this->projectsDao->addNewCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to create new project'));
        }

        $this->respond(new Response(
            Response::CREATED, 
            'Successfully created new project resource', 
            array('id' => $project->getId())
        ));
    }

    /**
     * Updates the projects category in the database.
     *
     * @return void
     */
    public function handleUpdateProjectCategory() {
        // Ensure all required parameters are present
        $this->requireParam('projectId');
        $this->requireParam('categoryId');

        $body = $this->requestBody;

        $project = $this->projectsDao->getCapstoneProject($body['projectId']);
        // TODO: handle case when project is not found

        $project->getCategory()->setId($body['categoryId']);

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to update project'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully updated the project'
        ));
    }

    /**
     * Updates fields editable from the user interface in a project entry in the database.
     *
     * @return void
     */
    public function handleSaveProject() {
        $id = $this->getFromBody('id');
        $title = $this->getFromBody('title');
        $typeId = $this->getFromBody('typeId');
        $compensationId = $this->getFromBody('compensationId');
        $focusId = $this->getFromBody('focusId');
        $ndaIpId = $this->getFromBody('ndaIpId');
        $additionalEmails = $this->getFromBody('additionalEmails');
        $comments = $this->getFromBody('comments');
        $dateStart = $this->getFromBody('dateStart');
        $dateEnd = $this->getFromBody('dateEnd');
        $description = $this->getFromBody('description');
        $motivation = $this->getFromBody('motivation');
        $objectives = $this->getFromBody('objectives');
        $minQualifications = $this->getFromBody('minQualifications');
        $preferredQualifications = $this->getFromBody('preferredQualifications');
        $videoLink = $this->getFromBody('videoLink');
        $websiteLink = $this->getFromBody('websiteLink');
        
		//Clear all existing keywords to account for removed keywords.
		$this->keywordsDao->removeAllKeywordsForEntity($id);
		
		$keywordsBracketSeparatedString = $this->getFromBody('keywords');			
		$keywordsArray = explode('[', $keywordsBracketSeparatedString);

		//TODO: Remove extra white space character.

		
		foreach ($keywordsArray as $keyword){
			$keyword = strtok($keyword, "],");
			if(!$this->keywordsDao->keywordExistsForEntity($keyword, $id)){
				if(!$this->keywordsDao->keywordExists($keyword)){
					//If the keyword doesn't exist, create a new entry and set approved to 0.
					$this->keywordsDao->addKeyword($keyword, 0);
				}
				
				$k = $this->keywordsDao->getKeyword($keyword);
				//Parameters are the keyword model and the project's id.
				$this->keywordsDao->addKeywordInJoinTable($k, $id);
				
			}			
		}
		

        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle case when project is not found

        $dateStart = $dateStart != '' ? new \DateTime($dateStart) : null;
        $dateEnd = $dateEnd != '' ? new \DateTime($dateEnd) : null;

        $project->setTitle($title)
            ->setAdditionalEmails($additionalEmails)
            ->setProposerComments($comments)
            ->setDateStart($dateStart)
            ->setDateEnd($dateEnd)
            ->setDescription($description)
            ->setMotivation($motivation)
            ->setObjectives($objectives)
            ->setMinQualifications($minQualifications)
            ->setPreferredQualifications($preferredQualifications)
            ->setVideoLink($videoLink)
            ->setWebsiteLink($websiteLink);

        $project->getCompensation()->setId($compensationId);
        $project->getFocus()->setId($focusId);
        $project->getNdaIp()->setId($ndaIpId);
        $project->getType()->setId($typeId);

        $project->setDateUpdated(new \DateTime());

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to save project'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully saved project'
        ));
    }

    /**
     * Handles a request to submit a project for approval
     *
     * @return void
     */
    public function handleSubmitForApproval() {
        $id = $this->getFromBody('id');
        $title = $this->getFromBody('title');
        $typeId = $this->getFromBody('typeId');
        $compensationId = $this->getFromBody('compensationId');
        $focusId = $this->getFromBody('focusId');
        $ndaIpId = $this->getFromBody('ndaIpId');
        $additionalEmails = $this->getFromBody('additionalEmails');
        $comments = $this->getFromBody('comments');
        $dateStart = $this->getFromBody('dateStart');
        $dateEnd = $this->getFromBody('dateEnd');
        $description = $this->getFromBody('description');
        $motivation = $this->getFromBody('motivation');
        $objectives = $this->getFromBody('objectives');
        $minQualifications = $this->getFromBody('minQualifications');
        $preferredQualifications = $this->getFromBody('preferredQualifications');
        $videoLink = $this->getFromBody('videoLink');
        $websiteLink = $this->getFromBody('websiteLink');

        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle case when project is not found

        // Save any changes
        $dateStart = $dateStart != '' ? new \DateTime($dateStart) : null;
        $dateEnd = $dateEnd != '' ? new \DateTime($dateEnd) : null;

        $project->setTitle($title)
            ->setAdditionalEmails($additionalEmails)
            ->setProposerComments($comments)
            ->setDateStart($dateStart)
            ->setDateEnd($dateEnd)
            ->setDescription($description)
            ->setMotivation($motivation)
            ->setObjectives($objectives)
            ->setMinQualifications($minQualifications)
            ->setPreferredQualifications($preferredQualifications)
            ->setVideoLink($videoLink)
            ->setWebsiteLink($websiteLink);

        $project->getCompensation()->setId($compensationId);
        $project->getFocus()->setId($focusId);
        $project->getNdaIp()->setId($ndaIpId);
        $project->getType()->setId($typeId);

        $project->setDateUpdated(new \DateTime());

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to save project before submission'));
        }

        $project->getStatus()->setId(CapstoneProjectStatus::PENDING_APPROVAL);

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to submit project for approval'));
        }

        $link = $this->getAbsoluteLinkTo('pages/editProject.php?id=' . $id) ;
        $this->mailer->sendProjectSubmissionConfirmationEmail($project, $link);

        $this->respond(new Response(
            Response::OK,
            'Successfully submitted project for approval'
        ));
    }

    /**
     * Handles updating the default image for a project in the database.
     *
     * @return void
     */
    public function handleDefaultImageSelected() {
        $imageId = $this->getFromBody('imageId');
        $projectId = $this->getFromBody('projectId');

        $project = $this->projectsDao->getCapstoneProject($projectId);
        // TODO: handle case when project is not found

        $image = $this->projectsDao->getCapstoneProjectImage($imageId);
        // TODO: handle case when image is not found

        $image->setProject($project);

        $image->setIsDefault(true);

        $ok = $this->projectsDao->updateCapstoneProjectDefaultImage($image);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to update capstone image'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully updated capstone image',
            array('name' => $image->getName())
        ));
    }

    /**
     * Request handler for approving a capstone project.
     *
     * @return void
     */
    public function handleApproveProject() {
        $id = $this->getFromBody('projectId');

        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle case when project is not found

        $project->getStatus()->setId(CapstoneProjectStatus::ACCEPTING_APPLICANTS);

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to approve project'));
        }

        $link = $this->getAbsoluteLinkTo('pages/viewSingleProject.php?id=' . $id);
        $this->mailer->sendProjectApprovedEmail($project, $link);

        $this->respond(new Response(
            Response::OK,
            'Successfully approved project. An email has been sent to the proposer.'
        ));
    }

    /**
     * Request handler for rejecting a capstone project.
     *
     * @return void
     */
    public function handleRejectProject() {
        $id = $this->getFromBody('projectId');
        $reason = $this->getFromBody('reason');

        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle case when project is not found

        $project->getStatus()->setId(CapstoneProjectStatus::REJECTED);

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to reject project'));
        }

        $this->mailer->sendProjectRejectedEmail($project, $reason);

        $this->respond(new Response(
            Response::OK,
            'Successfully rejected project. An email has been sent to the proposer.'
        ));
    }

    /**
     * Request handler for publishing a project (making it publicly viewable).
     *
     * @return void
     */
    public function handlePublishProject() {
        $id = $this->getFromBody('id');

        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle when not found

        $project->setIsHidden(false);

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to publish project'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully published project.'
        ));
    }

    /**
     * Request handler for unpublishing a project (making it not viewable to the public).
     *
     * @return void
     */
    public function handleUnpublishProject() {
        $id = $this->getFromBody('id');

        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle when not found

        $project->setIsHidden(true);

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to unpublish project'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully unpublished project.'
        ));
    }

    public function handleArchiveProject() {
        $id = $this->getFromBody('id');

        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle when not found

        $project->setIsArchived(true);

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to archive project'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully Archived project.'
        ));
    }

    /**
     * Handles the HTTP request on the API resource. 
     * 
     * This effectively will invoke the correct action based on the `action` parameter value in the request body. If
     * the `action` parameter is not in the body, the request will be rejected. The assumption is that the request
     * has already been authorized before this function is called.
     *
     * @return void
     */
    public function handleRequest() {
        // Make sure the action parameter exists
        $action = $this->getFromBody('action');

        // Call the correct handler based on the action
        switch ($action) {

            case 'createProject':
                $this->handleCreateProject();

            case 'updateCategory':
                $this->handleUpdateProjectCategory();

            case 'saveProject':
                $this->handleSaveProject();

            case 'submitForApproval':
                $this->handleSubmitForApproval();

            case 'defaultImageSelected':
                $this->handleDefaultImageSelected();

            case 'approveProject':
                $this->handleApproveProject();

            case 'rejectProject':
                $this->handleRejectProject();

            case 'publishProject':
                $this->handlePublishProject();

            case 'unpublishProject':
                $this->handleUnpublishProject();

            case 'archiveProject':
                $this->handleArchiveProject();

            default:
                $this->respond(new Response(Response::BAD_REQUEST, 'Invalid action on project resource'));
        }
    }

    private function getAbsoluteLinkTo($path) {
        return $this->config->getBaseUrl() . $path;
    }
}
