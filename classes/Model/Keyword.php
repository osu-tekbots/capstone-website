<?php
namespace Model;

class Keyword {
    
    /** @var integer */
    private $id;
    
    /** @var  string */
    private $name;
	
	private $parentId;
	
	private $approved;

    /**
     * Constructs a new instance of a CapstoneProjectFocus.
     *
     * @param integer $id the ID of the CapstoneProjectFocus. This should come directly from the database.
     * @param string $name the name associated with the CapstoneProjectFocus
     */
    public function __construct($id = null, $name = null) {
        if ($id == null && $name == null) {
            $this->setId(null);
            $this->setName('None');
        } else {
            $this->setId($id);
            $this->setName($name);
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
     * Get the value of name
     */ 
    public function getName() {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name) {
        $this->name = $name;

        return $this;
    }
	
	    /**
     * Get the value of parent id
     */ 
    public function getParentId() {
        return $this->parentId;
    }

    /**
     * Set the value of parent id
     *
     * @return  self
     */ 
    public function setParentId($id) {
        $this->parentId = $id;

        return $this;
    }
	
	 /**
     * Get the value of id
     */ 
    public function getApproved() {
        return $this->approved;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setApproved($approved) {
        $this->approved = $approved;

        return $this;
    }
}
