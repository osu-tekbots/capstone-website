<?php
namespace Model;

/**
 * Data class representing an CapstoneProjectStatus enumeration
 */
class CapstoneProjectStatus {

    const CREATED = 1;
    const PENDING_APPROVAL = 2;
    const REJECTED = 3;
    const ACCEPTING_APPLICANTS = 4;
    const IN_PROGRESS = 5;
    const COMPLETE = 6;
    const INCOMPLETE = 7;
    
    /** @var integer */
    private $id;
    
    /** @var  string */
    private $name;

    /**
     * Constructs a new instance of a CapstoneProjectStatus.
     *
     * @param integer $id the ID of the CapstoneProjectStatus. This should come directly from the database.
     * @param string $name the name associated with the CapstoneProjectStatus
     */
    public function __construct($id, $name) {
        $this->setId($id);
        $this->setName($name);
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
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
