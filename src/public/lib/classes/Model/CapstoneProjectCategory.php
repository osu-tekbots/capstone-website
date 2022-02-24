<?php
namespace Model;

/**
 * Data class representing an CapstoneProjectCategory enumeration
 */
class CapstoneProjectCategory {
    const NONE = 1;
    const ELECTRICAL_ENG = 2;
    const COMP_SCI = 3;
    const CS467 = 4;
    const CS46X = 5;
    const ECE44X = 6;
    const CS467_ONCAMPUS = 7;
    const CS46X_ONCAMPUS = 8;
    const ECE44X_ONCAMPUS = 9;

    /** @var integer */
    private $id;
    
    /** @var  string */
    private $name;

    /**
     * Constructs a new instance of a CapstoneProjectCategory.
     *
     * @param integer $id the ID of the CapstoneProjectCategory. This should come directly from the database.
     * @param string $name the name associated with the CapstoneProjectCategory
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
