<?php
namespace Api;

use Model\CapstoneProject;
use Model\CapstoneProjectLog;
use Model\CapstoneProjectStatus;
use Model\Keyword;
use Model\Category;
use Model\PreferredCourse;
use Model\User;
use Model\UsersDao;

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
    /** @var \DataAccess\CategoriesDao */
    private $categoriesDao;
    /** @var \DataAccess\PreferredCoursesDao */
    private $preferredCoursesDao;
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

    public function verifyIsProjectOwner($projectId) {
        $project = $this->projectsDao->getCapstoneProject($projectId);
        if ($project->getPropser()->getId() != $_SESSION['userID']) {
            if ($_SESSION['accessLevel'] != 'Admin') {
                $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Permission Denied'));
            }
        }
        return;
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
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'AH: Failed to create new project'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Project Created"
        ));

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

        if ($this->categoriesDao->categoryExistsForEntity($body['categoryId'], $body['projectId'])) {
            $ok = $this->categoriesDao->removeCategoryInJoinTable($body['categoryId'], $body['projectId']);
        }
        else {
            $ok = $this->categoriesDao->addCategoryInJoinTable($body['categoryId'], $body['projectId']);
        }
        
        $project = $this->projectsDao->getCapstoneProject($body['projectId']);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to update project category'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Category Updated"
        ));

        $this->respond(new Response(
            Response::OK,
            'Successfully updated the project'
        ));
    }


    /**
     * Updates the projects admin comments in the database.
     *
     * @return void
     */
    public function handleUpdateProjectAdminComments() {
        // Ensure all required parameters are present
        $this->requireParam('projectId');
        $this->requireParam('adminComments');

        $body = $this->requestBody;

        $project = $this->projectsDao->getCapstoneProject($body['projectId']);
        // TODO: handle case when project is not found

        $project->setAdminComments($body['adminComments']);

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to update project'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Admin Comments Updated"
        ));

        $this->respond(new Response(
            Response::OK,
            'Successfully updated the admin comments'
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
        $isSponsored = $this->getFromBody('isSponsored');
        $numberGroups = $this->getFromBody('numberGroupsId');
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
/*
        //Clear all existing preferred courses to account for removed courses.
		$this->preferredCoursesDao->removeAllPreferredCoursesForEntity($id);
        $preferredCoursesBracketSeparatedString = $this->getFromBody('preferredCourses');

        $preferredCoursesBracketSeparatedString = str_replace("[", "", $preferredCoursesBracketSeparatedString);
        $preferredCoursesBracketSeparatedString = str_replace("]", "", $preferredCoursesBracketSeparatedString);
        $preferredCoursesBracketSeparatedString = str_replace("\n", "", $preferredCoursesBracketSeparatedString);
        $preferredCoursesBracketSeparatedString = str_replace("\t", "", $preferredCoursesBracketSeparatedString);
        $preferredCoursesBracketSeparatedString = str_replace(" ", "", $preferredCoursesBracketSeparatedString);
        $preferredCoursesBracketSeparatedString = substr_replace($preferredCoursesBracketSeparatedString, "", -1);
        
        if (strlen($preferredCoursesBracketSeparatedString) > 0) {
            $preferredCoursesArray = explode(',', $preferredCoursesBracketSeparatedString);
            foreach ($preferredCoursesArray as $code){
                $pc = $this->preferredCoursesDao->getPreferredCourseByCode($code);
                $this->preferredCoursesDao->addPreferredCourseInJoinTable($pc, $id);
            }
        }
*/
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
            ->setNumberGroups($numberGroups)
            ->setMinQualifications($minQualifications)
            ->setPreferredQualifications($preferredQualifications)
            ->setVideoLink($videoLink)
            ->setWebsiteLink($websiteLink)
			->setIsSponsored($isSponsored);

        $project->getCompensation()->setId($compensationId);
        $project->getFocus()->setId($focusId);
        $project->getNdaIp()->setId($ndaIpId);
        $project->getType()->setId($typeId);
		
        $project->setDateUpdated(new \DateTime("now")); //Edit made on 3/31/23 not tested

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to save project'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Project Saved"
        ));

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
        $numberGroups = $this->getFromBody('numberGroupsId');
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
            ->setNumberGroups($numberGroups)
            ->setObjectives($objectives)
            ->setMinQualifications($minQualifications)
            ->setPreferredQualifications($preferredQualifications)
            ->setVideoLink($videoLink)
            ->setWebsiteLink($websiteLink);

        $project->getCompensation()->setId($compensationId);
        $project->getFocus()->setId($focusId);
        $project->getNdaIp()->setId($ndaIpId);
        $project->getType()->setId($typeId);

        $project->setDateUpdated(new \DateTime("now")); //Edit made on 3/31/23 not tested

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to save project before submission'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Project Saved before Submission for Approval"
        ));

        $project->getStatus()->setId(CapstoneProjectStatus::PENDING_APPROVAL);

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to submit project for approval'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Submitted for Approval"
        ));

        $link = $this->getAbsoluteLinkTo('pages/viewSingleProject.php?id=' . $id) ;
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
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Image Updated"
        ));

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
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Project Approved"
        ));

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
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Project Rejected"
        ));

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
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Project Published"
        ));

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
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to hide project'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Hidden"
        ));

        $this->respond(new Response(
            Response::OK,
            'Successfully hid project.'
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
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Project Archived"
        ));

        $this->respond(new Response(
            Response::OK,
            'Successfully Archived project.'
        ));
    }

    public function handleUnarchiveProject() {
        $id = $this->getFromBody('id');

        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle when not found

        $project->setIsArchived(false);

        $ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to unarchive project'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Project Unarchived"
        ));

        $this->respond(new Response(
            Response::OK,
            'Successfully Unarchived project.'
        ));
    }

    public function handleAddEditor() {
        $this->verifyAdminSession();

        $projectId = $this->getFromBody('projectId');
        $editorId = $this->getFromBody('editorId');

        $ok = $this->projectsDao->addCapstoneProjectEditor($projectId, $editorId);

        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to add editor'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully Added editor.'
        ));
    }

    public function handleDeleteEditor() {        
        $this->verifyAdminSession();

        $projectId = $this->getFromBody('projectId');
        $editorId = $this->getFromBody('editorId');
        
        $ok = $this->projectsDao->deleteCapstoneProjectEditor($projectId, $editorId);

        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to delete editor'));
        }

        $this->respond(new Response(
            Response::OK,
            'Successfully Deleted editor.'
        ));
    }
	
    public function handleDeleteProject() {
        $id = $this->getFromBody('id');

        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle when not found
        $pImages = $project->getImages();
        if ($pImages){
            foreach ($pImages as $image) {
                $imageId = $image->getId();
                $name = $image->getName();
                $ok = $this->projectsDao->deleteCapstoneProjectImage($imageId);
                if (!$ok) {
                    $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to unlink image'));
                }
            }
        }
        
        $ok = $this->projectsDao->deleteCapstoneProjectDBImages($id);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to delete image DB entries'));
        }

        $ok = $this->projectsDao->deleteCapstoneProjectApplications($id);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to delete project applications'));
        }

        $ok = $this->projectsDao->deleteCapstoneProject($id);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to delete project'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Project Deleted"
        ));

        $this->respond(new Response(
            Response::OK,
            'Successfully purged project.'
        ));
    }
	
	public function handleDeleteImage() {
        $id = $this->getFromBody('id');
		$pImage = $this->getFromBody('imageId');
		
        $project = $this->projectsDao->getCapstoneProject($id);
        // TODO: handle when not found
        
		$ok = $this->projectsDao->deleteCapstoneProjectImage($imageId);
		if (!$ok) {
			$this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to unlink image'));
		}
            
        $ok = $this->projectsDao->deleteCapstoneProjectDBImage($id, $pImage);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to delete image entry in DB'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Image Deleted"
        ));

        $this->respond(new Response(
            Response::OK,
            'Successfully purged project.'
        ));
    }

	
	/**
     * Updates the projects type in the database.
     *
     * @return void
     */
    public function handleUpdateProjectType() {
        // Ensure all required parameters are present
        $this->requireParam('projectId');
        $this->requireParam('typeId');

        $body = $this->requestBody;

        //Load project
		$project = $this->projectsDao->getCapstoneProject($body['projectId']);
        // TODO: handle case when project is not found

        //Update Project
		$project->getType()->setId($body['typeId']);
		
        //Save Project
		$ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to update project'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Type Updated"
        ));

        $this->respond(new Response(
            Response::OK,
            'Successfully updated the project'
        ));
    }
	
	/**
     * Updates the projects type in the database.
     *
     * @return void
     */
    public function handleUpdateProposer() {
        // Ensure all required parameters are present
        $this->requireParam('projectId');
        $this->requireParam('proposerId');

        $body = $this->requestBody;

        //Load project and user
		$project = $this->projectsDao->getCapstoneProject($body['projectId']);
        $user = $this->usersDao->getUser($body['proposerId']);
        // TODO: handle case when project is not found

        //Update Project
		$project->setProposer($user);
		$project->setProposerId($body['proposerId']);
       
	   //Save Project
		$ok = $this->projectsDao->updateCapstoneProject($project);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to update project'));
        }
        $this->projectsDao->insertCapstoneProjectLog(new CapstoneProjectLog(
            $project->getId(),
            new \DateTime,
            "Proposer Updated"
        ));


        $this->respond(new Response(
            Response::OK,
            'Successfully updated the project'
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
				break;
            case 'updateCategory':
                $this->handleUpdateProjectCategory();
				break;
            case 'updateAdminComments':
                $this->handleUpdateProjectAdminComments();
				break;
            case 'updateProposer':
                $this->handleUpdateProposer();
				break;
            case 'saveProject':
                $this->handleSaveProject();
				break;
            case 'submitForApproval':
                $this->handleSubmitForApproval();
				break;
            case 'defaultImageSelected':
                $this->handleDefaultImageSelected();
				break;
            case 'approveProject':
                $this->handleApproveProject();
				break;
            case 'rejectProject':
                $this->handleRejectProject();
				break;
            case 'publishProject':
                $this->handlePublishProject();
				break;
            case 'unpublishProject':
                $this->handleUnpublishProject();
				break;
            case 'unarchiveProject':
                $this->handleUnarchiveProject();
				break;
            case 'archiveProject':
                $this->handleArchiveProject();
				break;
            case 'deleteProject':
                $this->handleDeleteProject();
				break;		
			case 'updateType':
                $this->handleUpdateProjectType();
				break;			
            case 'deleteEditor':
                $this->handleDeleteEditor();
                break;		
            case 'addEditor':
                $this->handleAddEditor();
                break;
				
            default:
                $this->respond(new Response(Response::BAD_REQUEST, 'Invalid action on project resource'));
        }
    }

    private function getAbsoluteLinkTo($path) {
        return $this->config->getBaseUrl() . $path;
    }
}
