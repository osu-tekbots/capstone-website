<?php
namespace Model;

use Util\IdGenerator;

class CapstoneApplication {
    /** @var string */
    private $id;
    /** @var string */
    private $capstoneProjectId;
    /** @var CapstoneProject */
    private $capstoneProject;
    /** @var string */
    private $studentId;
    /** @var User */
    private $student;
    /** @var string */
    private $justification;
    /** @var string */
    private $timeAvailable;
    /** @var string */
    private $skillSet;
    /** @var string */
    private $portfolioLink;
    /** @var  CapstoneApplicationStatus */
    private $status;
    /** @var CapstoneInterestLevel */
    private $reviewInterestLevel;
    /** @var string */
    private $reviewProposerComments;
    /** @var \DateTime */
    private $dateCreated;
    /** @var \DateTime */
    private $dateUpdated;
    /** @var \DateTime */
    private $dateSubmitted;

    /**
     * Creates a new instance of a capstone application.
     * 
     * If an ID is provided, defaults will not be set. If an ID is not provided, a new ID will be generated and
     * defaults will be set.
     *
     * @param string|null $id the ID of the application. If null, a random ID will be generated.
     */
    public function __construct($id = null) {
        if ($id == null) {
            $id = IdGenerator::generateSecureUniqueId();
            $this->setId($id);
            $this->setStatus(new CapstoneApplicationStatus());
            $this->setReviewInterestLevel(new CapstoneInterestLevel());
            $this->setDateCreated(new \DateTime());
        } else {
            $this->setId($id);
        }
    }

    /**
     * Get the value of id
     */ 
    public function getId() {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of capstoneProject
     */ 
    public function getCapstoneProject() {
        return $this->capstoneProject;
    }

    /**
     * Set the value of capstoneProject
     *
     * @return  self
     */ 
    public function setCapstoneProject($capstoneProject) {
        $this->capstoneProject = $capstoneProject;

        return $this;
    }

    /**
     * Get the value of student
     */ 
    public function getStudent() {
        return $this->student;
    }

    /**
     * Set the value of student
     *
     * @return  self
     */ 
    public function setStudent($student) {
        $this->student = $student;

        return $this;
    }

    /**
     * Get the value of justification
     */ 
    public function getJustification() {
        return $this->justification;
    }

    /**
     * Set the value of justification
     *
     * @return  self
     */ 
    public function setJustification($justification) {
        $this->justification = $justification;

        return $this;
    }

    /**
     * Get the value of timeAvailable
     */ 
    public function getTimeAvailable() {
        return $this->timeAvailable;
    }

    /**
     * Set the value of timeAvailable
     *
     * @return  self
     */ 
    public function setTimeAvailable($timeAvailable) {
        $this->timeAvailable = $timeAvailable;

        return $this;
    }

    /**
     * Get the value of skillSet
     */ 
    public function getSkillSet() {
        return $this->skillSet;
    }

    /**
     * Set the value of skillSet
     *
     * @return  self
     */ 
    public function setSkillSet($skillSet) {
        $this->skillSet = $skillSet;

        return $this;
    }

    /**
     * Get the value of portfolioLink
     */ 
    public function getPortfolioLink() {
        return $this->portfolioLink;
    }

    /**
     * Set the value of portfolioLink
     *
     * @return  self
     */ 
    public function setPortfolioLink($portfolioLink) {
        $this->portfolioLink = $portfolioLink;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status) {
        $this->status = $status;

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
     * Get the value of dateUpdated
     */ 
    public function getDateUpdated() {
        return $this->dateUpdated;
    }

    /**
     * Set the value of dateUpdated
     *
     * @return  self
     */ 
    public function setDateUpdated($dateUpdated) {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * Get the value of dateSubmitted
     */ 
    public function getDateSubmitted() {
        return $this->dateSubmitted;
    }

    /**
     * Set the value of dateSubmitted
     *
     * @return  self
     */ 
    public function setDateSubmitted($dateSubmitted) {
        $this->dateSubmitted = $dateSubmitted;

        return $this;
    }

    /**
     * Get the value of studentId
     */ 
    public function getStudentId() {
        return $this->studentId;
    }

    /**
     * Set the value of studentId
     *
     * @return  self
     */ 
    public function setStudentId($studentId) {
        $this->studentId = $studentId;

        return $this;
    }

    /**
     * Get the value of capstoneProjectId
     */ 
    public function getCapstoneProjectId() {
        return $this->capstoneProjectId;
    }

    /**
     * Set the value of capstoneProjectId
     *
     * @return  self
     */ 
    public function setCapstoneProjectId($capstoneProjectId) {
        $this->capstoneProjectId = $capstoneProjectId;

        return $this;
    }

    /**
     * Get the value of reviewInterestLevel
     */ 
    public function getReviewInterestLevel() {
        return $this->reviewInterestLevel;
    }

    /**
     * Set the value of reviewInterestLevel
     *
     * @return  self
     */ 
    public function setReviewInterestLevel($reviewInterestLevel) {
        $this->reviewInterestLevel = $reviewInterestLevel;

        return $this;
    }

    /**
     * Get the value of reviewProposerComments
     */ 
    public function getReviewProposerComments() {
        return $this->reviewProposerComments;
    }

    /**
     * Set the value of reviewProposerComments
     *
     * @return  self
     */ 
    public function setReviewProposerComments($reviewProposerComments) {
        $this->reviewProposerComments = $reviewProposerComments;

        return $this;
    }
}
