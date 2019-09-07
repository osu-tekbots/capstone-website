<?php
namespace Model;

use Util\IdGenerator;

/**
 * Data structure representing a Project
 */
class CapstoneProject {

    /** @var string */
    private $id;
    
    /** @var string */
    private $proposerId;

    /** @var User */
    private $proposer;
	
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
	
    /** @var CapstoneProjectCompensation */
    private $compensation;
	
    /** @var string */
    private $additionalEmails;
	
    /** @var CapstoneProjectCategory */
    private $category;
	
    /** @var CapstoneProjectType */
    private $type;
	
    /** @var CapstoneProjectFocus */
    private $focus;
	
    /** @var CapstoneProjectCop */
    private $cop;
	
    /** @var CapstoneProjectNDAIP */
    private $ndaIp;
	
    /** @var string */
    private $websiteLink;
	
    /** @var CapstoneProjectImage[] */
    private $images;
	
    /** @var string */
    private $videoLink;
	
    /** @var boolean */
    private $isHidden;

    /** @var boolean */
    private $isArchived;
	
    /** @var string */
    private $proposerComments;
	
    /** @var CapstoneProjectStatus */
    private $status;
	
    /** @var boolean */
    private $archived;
	
    /** @var \DateTime */
    private $dateCreated;
	
    /** @var \DateTime */
    private $dateUpdated;

    /**
     * Constructs a new instance of a Project in the capstone system.
     * 
     * @param string|null $id the ID of the Project. If null, a new ID will be generated for the Project.
     */
    public function __construct($id = null) {
        if ($id == null) {
            $id = IdGenerator::generateSecureUniqueId();
            $this->setCompensation(new CapstoneProjectCompensation());
            $this->setCategory(new CapstoneProjectCategory());
            $this->setType(new CapstoneProjectType());
            $this->setFocus(new CapstoneProjectFocus());
            $this->setCop(new CapstoneProjectCop());
            $this->setNdaIp(new CapstoneProjectNDAIP());
            $this->setStatus( new CapstoneProjectStatus());
            $this->setDateCreated(new \DateTime()); //No idea why there is a '\' here... Don 7/29/2019
            $this->setIsHidden(true);
            $this->setIsArchived(false);
        }
        $this->setId($id);
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
     * Get the value of type
     */ 
    public function getType() {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of the proposer
     */ 
    public function getProposer() {
        return $this->proposer;
    }

    /**
     * Set the value of the proposer
     *
     * @return  self
     */ 
    public function setProposer($proposer) {
        $this->proposer = $proposer;

        return $this;
    }

    /**
     * Get the value of title
     */ 
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of motivation
     */ 
    public function getMotivation() {
        return $this->motivation;
    }

    /**
     * Set the value of motivation
     *
     * @return  self
     */ 
    public function setMotivation($motivation) {
        $this->motivation = $motivation;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of objectives
     */ 
    public function getObjectives() {
        return $this->objectives;
    }

    /**
     * Set the value of objectives
     *
     * @return  self
     */ 
    public function setObjectives($objectives) {
        $this->objectives = $objectives;

        return $this;
    }

    /**
     * Get the value of dateStart
     */ 
    public function getDateStart() {
        return $this->dateStart;
    }

    /**
     * Set the value of dateStart
     *
     * @return  self
     */ 
    public function setDateStart($dateStart) {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get the value of dateEnd
     */ 
    public function getDateEnd() {
        return $this->dateEnd;
    }

    /**
     * Set the value of dateEnd
     *
     * @return  self
     */ 
    public function setDateEnd($dateEnd) {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get the value of minQualifications
     */ 
    public function getMinQualifications() {
        return $this->minQualifications;
    }

    /**
     * Set the value of minQualifications
     *
     * @return  self
     */ 
    public function setMinQualifications($minQualifications) {
        $this->minQualifications = $minQualifications;

        return $this;
    }

    /**
     * Get the value of preferredQualifications
     */ 
    public function getPreferredQualifications() {
        return $this->preferredQualifications;
    }

    /**
     * Set the value of preferredQualifications
     *
     * @return  self
     */ 
    public function setPreferredQualifications($preferredQualifications) {
        $this->preferredQualifications = $preferredQualifications;

        return $this;
    }

    /**
     * Get the value of compensation
     */ 
    public function getCompensation() {
        return $this->compensation;
    }

    /**
     * Set the value of compensation
     *
     * @return  self
     */ 
    public function setCompensation($compensation) {
        $this->compensation = $compensation;

        return $this;
    }

    /**
     * Get the value of additionalEmails
     */ 
    public function getAdditionalEmails() {
        return $this->additionalEmails;
    }

    /**
     * Set the value of additionalEmails
     *
     * @return  self
     */ 
    public function setAdditionalEmails($additionalEmails) {
        $this->additionalEmails = $additionalEmails;

        return $this;
    }

    /**
     * Get the value of category
     */ 
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @return  self
     */ 
    public function setCategory($category) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of focus
     */ 
    public function getFocus() {
        return $this->focus;
    }

    /**
     * Set the value of focus
     *
     * @return  self
     */ 
    public function setFocus($focus) {
        $this->focus = $focus;

        return $this;
    }

    /**
     * Get the value of cop
     */ 
    public function getCop() {
        return $this->cop;
    }

    /**
     * Set the value of cop
     *
     * @return  self
     */ 
    public function setCop($cop) {
        $this->cop = $cop;

        return $this;
    }

    /**
     * Get the value of ndaIp
     */ 
    public function getNdaIp() {
        return $this->ndaIp;
    }

    /**
     * Set the value of ndaIp
     *
     * @return  self
     */ 
    public function setNdaIp($ndaIp) {
        $this->ndaIp = $ndaIp;

        return $this;
    }

    /**
     * Get the value of websiteLink
     */ 
    public function getWebsiteLink() {
        return $this->websiteLink;
    }

    /**
     * Set the value of websiteLink
     *
     * @return  self
     */ 
    public function setWebsiteLink($websiteLink) {
        $this->websiteLink = $websiteLink;

        return $this;
    }

    /**
     * Get the value of images
     */ 
    public function getImages() {
        return $this->images;
    }

    /**
     * Set the value of images
     *
     * @return  self
     */ 
    public function setImages($images) {
        $this->images = $images;

        return $this;
    }

    /**
     * Get the value of videoLink
     */ 
    public function getVideoLink() {
        return $this->videoLink;
    }

    /**
     * Set the value of videoLink
     *
     * @return  self
     */ 
    public function setVideoLink($videoLink) {
        $this->videoLink = $videoLink;

        return $this;
    }

    /**
     * Get the value of isHidden
     */ 
    public function getIsHidden() {
        return $this->isHidden;
    }

    /**
     * Set the value of isHidden
     *
     * @return  self
     */ 
    public function setIsHidden($isHidden) {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get the value of proposerComments
     */ 
    public function getProposerComments() {
        return $this->proposerComments;
    }

    /**
     * Set the value of proposerComments
     *
     * @return  self
     */ 
    public function setProposerComments($proposerComments) {
        $this->proposerComments = $proposerComments;

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
     * Get the value of archived
     */ 
    public function getIsArchived() {
        return $this->archived;
    }

    /**
     * Set the value of archived
     *
     * @return  self
     */ 
    public function setIsArchived($archived) {
        $this->archived = $archived;

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
     * Get the value of proposerId
     */ 
    public function getProposerId() {
        return $this->proposerId;
    }

    /**
     * Set the value of proposerId
     *
     * @return  self
     */ 
    public function setProposerId($proposerId) {
        $this->proposerId = $proposerId;

        return $this;
    }
}
