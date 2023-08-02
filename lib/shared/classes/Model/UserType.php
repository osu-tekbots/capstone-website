<?php
namespace Model;

/**
 * Data class representing a user type enumeration.
 */
class UserType {
    const STUDENT = 1;
    const PROPOSER = 2;
    const ADMIN = 3;

    /** @var integer */
    private $id;
    
    /** @var string */
    private $name;

    /**
     * Creates a new instance of a UserType.
     *
     * @param integer $id the ID of the UserType. This should come directly from the database.
     * @param string $name the name of the type
     */
    public function __construct($id = null, $name = null) {
        if ($id == null && $name == null) {
            $this->setId(self::STUDENT);
            $this->setName('Student');
        } else {
            $this->setId($id);
            $this->setName($name);
        }
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
}
