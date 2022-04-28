<?php
namespace Api;

use Model\CapstoneProject;
use Model\Keyword;
use Model\Category;
use Model\PreferredCourse;
use Model\User;
use Model\UsersDao;

/**
 * Defines the logic for how to handle AJAX requests made to modify course information.
 */
class PreferredCoursesActionHandler extends ActionHandler {

    /** @var \DataAccess\CapstoneProjectsDao */
    private $projectsDao;
    /** @var \DataAccess\UsersDao */
    private $usersDao;
    /** @var \DataAccess\KeywordsDao */
    private $keywordsDao;	
    /** @var \DataAccess\CategoriesDao */
    private $categoriesDao;
    /** @var \DataAccess\PreferredCoursesDao */
    private $preferredCoursesDao;
    /** @var \Util\ConfigManager */
    private $config;

    /**
     * Constructs a new instance of the action handler for requests on courses resources.
     *
     * @param \DataAccess\PreferredCoursesDao $PreferredCoursesDao the data access object for courses
     * @param \DataAccess\PreferredCoursesDao $usersDao the data access object for users
     * @param \Email\ProjectMailer $mailer the mailer used to send project related emails
     * @param \Util\ConfigManager $config the configuration manager providing access to site config
     * @param \Util\Logger $logger the logger to use for logging information about actions
     */
    public function __construct($projectsDao, $usersDao, $keywordsDao, $categoriesDao, $preferredCoursesDao, $mailer, $config, $logger) {
        parent::__construct($logger);
        $this->projectsDao = $projectsDao;
        $this->usersDao = $usersDao;
		$this->keywordsDao = $keywordsDao;
        $this->categoriesDao = $categoriesDao;
        $this->preferredCoursesDao = $preferredCoursesDao;
        $this->mailer = $mailer;
        $this->config = $config;
    }

    public function verifyAdminSession() {
        if ($_SESSION['accessLevel'] != 'Admin') {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Permission Denied'));
        }
        return;
    }

    /**
     * Creates a new preferred course entry in the database.
     *
     * @return void
     */
    public function handleCreatePreferredCourse() {
        // Ensure all the requred parameters are present
        $this->requireParam('code');
        $this->requireParam('name');

        $body = $this->requestBody;

        $course = new PreferredCourse();
        $course->setCode($body['code']);
        $course->setName($body['name']);

        $ok = $this->preferredCoursesDao->addPreferredCourse($course);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to create new course'));
        }
        

        $this->respond(new Response(
            Response::CREATED, 
            'Successfully created new project resource'
        ));
    }

    public function handleEditPreferredCourse() {
        $id = $this->getFromBody('id');
        $code = $this->getFromBody('code');
        $name = $this->getFromBody('name');
       

        $course = $this->preferredCoursesDao->getPreferredCourseById($id);
        // TODO: handle case when project is not found

        $course->setCode($code);
        $course->setName($name);

		
        $ok = $this->preferredCoursesDao->updatePreferredCourse($course);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to save course'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully saved course'
        ));
    }

    public function handleDeleteCourse() {
        $id = $this->getFromBody('id');

        // $project = $this->preferredCoursesDao->getPreferredCourseById($id);
        // TODO: handle when not found

        $ok = $this->preferredCoursesDao->deletePreferredCourse($id);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to delete course'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully delete course.'
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

            case 'createCourse':
                $this->handleCreatePreferredCourse();
				break;
            case 'editCourse':
                $this->handleEditPreferredCourse();
				break;
            case 'deleteCourse':
                $this->handleDeleteCourse();
                break;
				
            default:
                $this->respond(new Response(Response::BAD_REQUEST, 'Invalid action on course resource'));
        }
    }

    private function getAbsoluteLinkTo($path) {
        return $this->config->getBaseUrl() . $path;
    }
}