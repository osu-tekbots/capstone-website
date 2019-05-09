<?php
namespace Api;

use Model\CapstoneApplication;
use Model\CapstoneApplicationStatus;

/**
 * Defines the logic for how to handle AJAX requests made to modify project application information.
 */
class ApplicationsActionHandler extends ActionHandler {

    /** @var \DataAccess\CapstoneApplicationsDao */
    private $applicationsDao;
    /** @var \DataAccess\CapstoneProjectsDao */
    private $projectsDao;
    /** @var \DataAccess\UsersDao */
    private $usersDao;
    /** @var \Email\ApplicationMailer */
    private $mailer;
    /** @var \Util\ConfigManager */
    private $config;

    /**
     * Constructs a new instance of the action handler for requests on project resources.
     *
     * @param \DataAccess\CapstoneApplicationsDao $applicationsDao the data access object for applications
     * @param \DataAccess\CapstoneProjectsDao $projectsDao the data access object for project information
     * @param \DataAccess\UsersDao $usersDao the data access object for users
     * @param \Email\ApplicationMailer $mailer the mailer used to send application related emails
     * @param \Util\ConfigManager $config the configuration manager providing access to site config
     * @param \Util\Logger $logger the logger to use for logging information about actions
     */
    public function __construct($applicationsDao, $projectsDao, $usersDao, $mailer, $config, $logger) {
        parent::__construct($logger);
        $this->applicationsDao = $applicationsDao;
        $this->projectsDao = $projectsDao;
        $this->usersDao = $usersDao;
        $this->mailer = $mailer;
        $this->config = $config;
    }

    /**
     * Handles a request to create a new application on a capstone project.
     *
     * @return void
     */
    public function handleCreateApplication() {
        $pid = $this->getFromBody('projectId');
        $uid = $this->getFromBody('uid');

        $user = $this->usersDao->getUser($uid);
        // TODO: handle case when user is not found

        $project = $this->projectsDao->getCapstoneProject($pid);
        // TODO: handle case when project is not found

        $application = new CapstoneApplication();
        $application->setCapstoneProject($project)->setStudent($user);

        $ok = $this->applicationsDao->addNewApplication($application);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to create application'));
        }

        $this->respond(new Response(
            Response::CREATED,
            'Successfully created new application',
            array('id' => $application->getId())
        ));
    }

    /**
     * Handles a request to save updated application information.
     *
     * @return void
     */
    public function handleSaveApplication() {
        $applicationId = $this->getFromBody('applicationId');
        $justification = $this->getFromBody('justification');
        $skillSet = $this->getFromBody('skillSet');
        $timeAvailable = $this->getFromBody('timeAvailable');
        $portfolioLink = $this->getFromBody('portfolioLink');

        $application = $this->applicationsDao->getApplication($applicationId);
        // TODO: handle case when application is not found

        $application->setJustification($justification)
            ->setSkillSet($skillSet)
            ->setTimeAvailable($timeAvailable)
            ->setPortfolioLink($portfolioLink)
            ->setDateUpdated(new \DateTime());

        $ok = $this->applicationsDao->updateApplication($application);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to save application draft'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully saved project draft'
        ));
    }

    /**
     * Handles a request to submit an application by a student for a capstone project.
     *
     * @return void
     */
    public function handleSubmitApplication() {
        $applicationId = $this->getFromBody('applicationId');
        $justification = $this->getFromBody('justification');
        $skillSet = $this->getFromBody('skillSet');
        $timeAvailable = $this->getFromBody('timeAvailable');
        $portfolioLink = $this->getFromBody('portfolioLink');

        $application = $this->applicationsDao->getApplication($applicationId);
        // TODO: handle case when application is not found

        // Fetch the project to so we can get propopser information
        $project = $this->projectsDao->getCapstoneProject($application->getCapstoneProject()->getId());
        $application->setCapstoneProject($project);

        $application->setJustification($justification)
            ->setSkillSet($skillSet)
            ->setTimeAvailable($timeAvailable)
            ->setPortfolioLink($portfolioLink)
            ->setDateUpdated(new \DateTime());

        $ok = $this->applicationsDao->updateApplication($application);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to save application before submitting'));
        }

        $application->getStatus()->setId(CapstoneApplicationStatus::SUBMITTED);

        $ok = $this->applicationsDao->updateApplication($application);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to submit application'));
        }

        $link = $this->getAbsoluteLinkTo("pages/editApplication.php?id=$applicationId");
        $this->mailer->sendApplicationSubmissionConfirmation($application, $link);

        $link = $this->getAbsoluteLinkTo("pages/reviewApplication.php?id=$applicationId");
        $this->mailer->sendApplicationSubmissionNotification($application, $link);

        $this->respond(new Response(
            Response::OK,
            'Successfully submitted application'
        ));
    }

    /**
     * Handles a request to review a project application.
     *
     * @return void
     */
    public function handleReviewApplication() {
        $applicationId = $this->getFromBody('applicationId');
        $interestLevelId = $this->getFromBody('interestLevelId');
        $comments = $this->getFromBody('comments');

        $application = $this->applicationsDao->getApplication($applicationId);
        // TODO: handle case when not found

        $application->getReviewInterestLevel()->setId($interestLevelId);
        $application->setReviewProposerComments($comments);

        $ok = $this->applicationsDao->updateApplication($application);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to save application review'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully saved application review'
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

            case 'createApplication':
                $this->handleCreateApplication();

            case 'saveApplication':
                $this->handleSaveApplication();

            case 'submitApplication':
                $this->handleSubmitApplication();

            case 'reviewApplication':
                $this->handleReviewApplication();

            default:
                $this->respond(new Response(Response::BAD_REQUEST, 'Invalid action on application resource'));
        }
    }

    /**
     * Constructs and returns an absolute URL to the resource at the relative path
     *
     * @param string $path the relative URL to the resource
     * @return string the absolute URL
     */
    private function getAbsoluteLinkTo($path) {
        return $this->config->getBaseUrl() . $path;
    }
}
