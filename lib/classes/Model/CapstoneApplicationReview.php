<?php
namespace Model;

use Util\IdGenerator;

class CapstoneApplicationReview {
    /** @var string */
    private $id;
    /** @var CapstoneApplication */
    private $application;
    /** @var CapstoneApplicationReviewInterestLevel */
    private $interestLevel;
    /** @var string */
    private $comments;

    /**
     * Creates a new instance of a capstone application review.
     *
     * @param string|null $id the ID of the application review. If null, a random ID will be generated.
     */
    public function __construct($id = null) {
        if ($id == null) {
            $id = IdGenerator::generateSecureUniqueId();
            $this->setId($id);
            $this->setInterestLevel(new CapstoneApplicationReviewInterestLevel());
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
     * Get the value of application
     */ 
    public function getApplication() {
        return $this->application;
    }

    /**
     * Set the value of application
     *
     * @return  self
     */ 
    public function setApplication($application) {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the value of interestLevel
     */ 
    public function getInterestLevel() {
        return $this->interestLevel;
    }

    /**
     * Set the value of interestLevel
     *
     * @return  self
     */ 
    public function setInterestLevel($interestLevel) {
        $this->interestLevel = $interestLevel;

        return $this;
    }

    /**
     * Get the value of comments
     */ 
    public function getComments() {
        return $this->comments;
    }

    /**
     * Set the value of comments
     *
     * @return  self
     */ 
    public function setComments($comments) {
        $this->comments = $comments;

        return $this;
    }
}
