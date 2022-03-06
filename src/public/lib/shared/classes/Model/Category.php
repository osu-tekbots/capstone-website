<?php
namespace Model;

class Category {

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
}