<?php
namespace Model;

/**
 * Data structure representing a Project
 */
class Project {

    /** @var string */
    private $id;
	
	/** @var string */
    private $userID;
	
	/** @var string */
    private $title;
	
	/** @var string */
    private $motivation;
	
	/** @var string */
    private $description;
	
	/** @var string */
    private $objectives;
	
	/** @var \DateTime */
    private $dateStart;

    /** @var \DateTime */
    private $dateEnd;
	
	/** @var string */
    private $minQualifications;
	
	/** @var string */
    private $preferredQualifications;
	
	/** @var ProjectCompensation */
    private $compensation;
	
	/** @var string */
    private $additionalEmails;
	
	/** @var ProjectCategory */
    private $category;
	
	/** @var ProjectType */
    private $type;
	
	/** @var ProjectFocus */
    private $focus;
	
	/** @var ProjectCop */
    private $cop;
	
	/** @var ProjectNDAIP */
    private $ndaIp;
	
	/** @var string */
    private $websiteLink;
	
	/** @var string */
    private $imageLink;
	
	/** @var string */
    private $videoLink;
	
	/** @var boolean */
    private $isHidden;
	
	/** @var string */
    private $proposerComments;
	
	/** @var ProjectStatus */
    private $status;
	
	/** @var boolean */
	private $archived;
	
	/** @var /DateTime */
    private $dateCreated;
	
	/** @var /DateTime */
    private $dateUpdated;

    /**
     * Constructs a new instance of a Project in the capstone system.
     * 
     * @param string|null $id the ID of the Project. If null, a new ID will be generated for the Project.
     */
    public function __construct() {

    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of dateCreated
     */ 
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set the value of dateCreated
     *
     * @return  self
     */ 
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get the value of dateUpdated
     */ 
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Set the value of dateUpdated
     *
     * @return  self
     */ 
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

}