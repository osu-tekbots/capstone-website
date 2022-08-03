<?php
namespace Model;

/**
 * Data class representing an CapstoneProjectCompensation enumeration
 */
class CapstoneProjectCompensation {
    const NONE = 1;
    const HOURLY = 2;
    const STIPEND = 3;
    const COMPLETION_DEPENDENT = 4;
    const OTHER = 5;
    
    /** @var integer */
    private $id;
    
    /** @var  string */
    private $name;

    /**
     * Constructs a new instance of a CapstoneProjectCompensation.
     *
     * @param integer $id the ID of the CapstoneProjectCompensation. This should come directly from the database.
     * @param string $name the name associated with the CapstoneProjectCompensation
     */
    public function __construct($id = null, $name = null) {
        if ($id == null && $name == null) {
            $this->setId(self::NONE);
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
}
