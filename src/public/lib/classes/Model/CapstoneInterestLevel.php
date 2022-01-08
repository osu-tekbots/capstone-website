<?php
namespace Model;

/**
 * Represent an interest level enumeration from the database
 */
class CapstoneInterestLevel {
    const NOT_SPECIFIED = 1;
    const IMPARTIAL = 2;
    const DESIRABLE = 3;
    const UNDESIRABLE = 4;

    /** @var string */
    private $id;
    /** @var string */
    private $name;

    public function __construct($id = null, $name = null) {
        if ($id == null && $name == null) {
            $this->setId(self::NOT_SPECIFIED);
            $this->setName('Not Specified');
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
