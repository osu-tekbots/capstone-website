<?php
namespace Model;

/**
 * Data class representing an CapstoneProjectFocus enumeration
 */
class CapstoneProjectFocus {
    const NONE = 1;
    const RESEARCH = 2;
    const DEVELOPMENT = 3;
    const R_AND_D = 4;
    
    /** @var integer */
    private $id;
    
    /** @var  string */
    private $name;

    /**
     * Constructs a new instance of a CapstoneProjectFocus.
     *
     * @param integer $id the ID of the CapstoneProjectFocus. This should come directly from the database.
     * @param string $name the name associated with the CapstoneProjectFocus
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
