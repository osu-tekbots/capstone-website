<?php
namespace Model;

use Util\IdGenerator;

/**
 * Represents an image (JPEG, PNG, etc.) associated with a capstone project. Projects can have multiple
 * images.
 */
class CapstoneProjectImage {

    /** @var string */
    private $id;
    /** @var string */
    private $projectId;
    /** @var CapstoneProject */
    private $project;
    /** @var string */
    private $name;
    /** @var boolean */
    private $isDefault;
    /** @var boolean */
    private $isProvided;
    /** @var string */
    private $providedImageName;

    public function __construct($id = null) {
        if ($id == null) {
            $id = IdGenerator::generateSecureUniqueId();
            $this->setIsDefault(false);
            $this->setIsProvided(false);
        }
        $this->setId($id);
    }

    /**
     * Set the ID for the image
     *
     * @param string $id
     * @return self 
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the ID
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set the project the image belongs to
     *
     * @param CapstoneProject $project
     * @return self
     */
    public function setProject($project) {
        $this->project = $project;
        return $this;
    }

    /**
     * Get the project the image belongs to
     *
     * @return CapstoneProject
     */
    public function getProject() {
        return $this->project;
    }

    /**
     * Sets the name of the image file
     *
     * @param string $name
     * @return self
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the image file
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets whether the image is the default or not.
     *
     * @param boolean $isDefault
     * @return self
     */
    public function setIsDefault($isDefault) {
        $this->isDefault = $isDefault;
        return $this;
    }

    /**
     * Tells whether the image is the default or not.
     *
     * @return boolean
     */
    public function getIsDefault() {
        return $this->isDefault;
    }

    /**
     * Get the value of projectId
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
     * Get the value of isProvided
     */ 
    public function getIsProvided() {
        return $this->isProvided;
    }

    /**
     * Set the value of isProvided
     *
     * @return  self
     */ 
    public function setIsProvided($isProvided) {
        $this->isProvided = $isProvided;

        return $this;
    }

    /**
     * Get the value of providedImageName
     */ 
    public function getProvidedImageName() {
        return $this->providedImageName;
    }

    /**
     * Set the value of providedImageName
     *
     * @return  self
     */ 
    public function setProvidedImageName($providedImageName) {
        $this->providedImageName = $providedImageName;

        return $this;
    }
}
