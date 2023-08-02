<?php
namespace Api;

use Model\Keyword;


/**
 * Defines the logic for how to handle AJAX requests made to modify keywords.
 */
class KeywordsActionHandler extends ActionHandler {

    /** @var \DataAccess\KeywordsDao */
    private $keywordsDao;
    /** @var \Util\ConfigManager */
    private $config;

    /**
     * Constructs a new instance of the action handler for requests on project resources.
     *
     * @param \DataAccess\KeywordsDao $keywordsDao the data access object for keywords
     * @param \Util\ConfigManager $config the configuration manager providing access to site config
     * @param \Util\Logger $logger the logger to use for logging information about actions
     */
    public function __construct($keywordsDao, $config, $logger) {
        parent::__construct($logger);
        $this->keywordsDao = $keywordsDao;
        $this->config = $config;
    }

    /**
     * Handles a request to merge a group of keywords to use the same word in all relevant capstone projects.
     *
     * @return void
     */
    public function handleMergeKeywords() {
        $this->requireParam('keywordIds');
        
        $keywordIds = $this->getFromBody('keywordIds');

        $ok = $this->keywordsDao->adminMergeKeywords($keywordIds);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to merge keywords'));
        }

        $this->respond(new Response(Response::OK, 'Keywords merged!'));
    }

    /**
     * Handles a request to change the approval status of a keyword.
     * Approved keywords show up in the keyword autocomplete list when editing projects 
     *     and as a filter option when browsing projests.
     *
     * @return void
     */
    public function handleUpdateApproval() {
        $this->requireParam('approved');
        $this->requireParam('keywordId');
        
        $approved = $this->getFromBody('approved');
        $keywordId = $this->getFromBody('keywordId');

        $ok = $this->keywordsDao->adminUpdateApproval($keywordId, $approved);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to update keyword'));
        }

        $this->respond(new Response(Response::OK, "Keyword ".($approved ? "approved" : "unapproved")."!"));
    }

    /**
     * Handles a request to edit a keyword's text as it's displayed in all relevant capstone projects.
     *
     * @return void
     */
    public function handleEditKeyword() {
        $this->requireParam('keywordText');
        $this->requireParam('keywordId');
        
        $keywordText = $this->getFromBody('keywordText');
        $keywordId = $this->getFromBody('keywordId');

        $ok = $this->keywordsDao->adminUpdateKeyword($keywordId, $keywordText);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to update keyword'));
        }

        $this->respond(new Response(Response::OK, "Keyword updated!"));
    }

    /**
     * Handles a request to remove a keyword from all relevant capstone projects.
     *
     * @return void
     */
    public function handleRemoveKeyword() {
        $this->requireParam('keywordId');
        
        $keywordId = $this->getFromBody('keywordId');

        $ok = $this->keywordsDao->adminRemoveKeywordEverywhere($keywordId);
        if (!$ok) {
            $this->respond(new Response(Response::INTERNAL_SERVER_ERROR, 'Failed to remove keyword'));
        }

        $this->respond(new Response(Response::OK, 'Keyword removed!'));
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
            case 'mergeKeywords':
                $this->handleMergeKeywords();
                break;
            case 'updateApproval':
                $this->handleUpdateApproval();
                break;
            case 'editKeyword':
                $this->handleEditKeyword();
                break;
            case 'removeKeyword':
                $this->handleRemoveKeyword();
                break;
            default:
                $this->respond(new Response(Response::BAD_REQUEST, 'Invalid action on keywords resource'));
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
