<?php
namespace Api;

use Model\CapstoneProject;
use Model\CapstoneProjectStatus;

/**
 * Defines the logic for how to handle AJAX requests made to modify project information.
 */
class ProjectsActionHandler extends ActionHandler {

    /** @var \DataAccess\CapstoneProjectsDao */
    private $projectsDao;
    /** @var \DataAccess\UsersDao */
    private $usersDao;
    /** @var \Email\ProjectMailer */
    private $mailer;
    /** @var \Util\ConfigManager */
    private $config;
    /** @var \Util\Logger */
    private $logger;

    /**
     * Constructs a new instance of the action handler for requests on project resources.
     *
     * @param \DataAccess\CapstoneProjectsDao $projectsDao the data access object for projects
     * @param \DataAccess\CapstoneProjectsDao $usersDao the data access object for users
     * @param \Email\ProjectMailer $mailer the mailer used to send project related emails
     * @param \Util\ConfigManager $config the configuration manager providing access to site config
     * @param \Util\Logger $logger the logger to use for logging information about actions
     */
    public function __construct($projectsDao, $usersDao, $mailer, $config, $logger) {
        parent::__construct($logger);
        $this->projectsDao = $projectsDao;
        $this->usersDao = $usersDao;
        $this->mailer = $mailer;
        $this->config = $config;
        $this->logger = $logger;
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
        $this->requireParam('id');
        $this->requireParam('typeId');
        $this->requireParam('compensationId');
        $this->requireParam('focusId');
        $this->requireParam('ndaIpId');
        $this->requireParam('additionalEmails');
        $this->requireParam('comments');
        $this->requireParam('dateStart');
        $this->requireParam('dateEnd');
        $this->requireParam('description');
        $this->requireParam('motivation');
        $this->requireParam('objectives');
        $this->requireParam('videoLink');
        $this->requireParam('websiteLink');
        // TODO: handle keywords

        $body = $this->requestBody;

        $project = $this->projectsDao->getCapstoneProject($body['id']);
        // TODO: handle case when project is not found

        $dateStart = $body['dateStart'] != '' ? new \DateTime($body['dateStart']) : null;
        $dateEnd = $body['dateEnd'] != '' ? new \DateTime($body['dateEnd']) : null;

        $project->setAdditionalEmails($body['additionalEmails'])
            ->setProposerComments($body['comments'])
            ->setDateStart($dateStart)
            ->setDateEnd($dateEnd)
            ->setDescription($body['description'])
            ->setMotivation($body['motivation'])
            ->setObjectives($body['objectives'])
            ->setVideoLink($body['videoLink'])
            ->setWebsiteLink($body['websiteLink']);

        $project->getCompensation()->setId($body['compensationId']);
        $project->getFocus()->setId($body['focusId']);
        $project->getNdaIp()->setId($body['ndaIpId']);
        $project->getType()->setId($body['typeId']);

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

        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle case when project is not found

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
     * Request handler for publishing a project (making it publically viewable).
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

            default:
                $this->respond(new Response(Response::BAD_REQUEST, 'Invalid action on project resource'));
        }
    }

    private function getAbsoluteLinkTo($path) {
        return $this->config->getBaseUrl() . $path;
    }
}
