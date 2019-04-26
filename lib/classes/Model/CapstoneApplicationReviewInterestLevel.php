<?php
namespace Model;

/**
 * Represent an interest level enumeration from the database
 */
class CapstoneApplicationReviewInterestLevel {
    const DESIREABLE = 1;
    const UNDESIREABLE = 2;
    const IMPARTIAL = 3;

    /** @var string */
    private $id;
    /** @var string */
    private $name;

    public function __construct($id = null, $name = null) {
        if ($id == null && $name == null) {
            $this->setId(self::IMPARTIAL);
            $this->setName('Impartial');
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
