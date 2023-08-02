<?php
namespace Model;

/**
 * Data structure representing a Project Log
 */
class CapstoneProjectLog {

    /** @var string */
    private $projectid;

    /** @var DateTime */
    private $dateCreated;

    /** @var string */
    private $message;

    /**
     * Constructs a new instance of a Project log in the capstone system.
     *
     * @param string $projectId the ID of the Project.
     * @param Datetime $dateCreated the date the log was created.
     * @param string $message the text value of the log message.
     */
    public function __construct($projectId, $dateCreated, $message) {
        $this->setProjectId($projectId);
        $this->setDateCreated($dateCreated);
        $this->setMessage($message);
    }

    /**
     * Get the value of id
     */
    public function getProjectId() {
        return $this->projectId;
    }

    /**
     * Set the value of projectId
     *
     * @return  self
     */
    public function setProjectId($projectId) {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get the value of dateCreated
     */
    public function getDateCreated() {
        return $this->dateCreated;
    }

    /**
     * Set the value of dateCreated
     *
     * @return  self
     */
    public function setDateCreated($dateCreated) {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get the value of message
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */
    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }
}
