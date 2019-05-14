<?php
namespace Model;

/**
 * Data class representing an CapstoneProjectNDAIP enumeration
 */
class CapstoneProjectNDAIP {
    const NO_AGREEMENT_REQUIRED = 1;
    const NDA_REQUIRED = 2;
    const NDA_IP_REQUIRED = 3;
    
    /** @var integer */
    private $id;
    
    /** @var  string */
    private $name;

    /**
     * Constructs a new instance of a CapstoneProjectNDAIP.
     *
     * @param integer $id the ID of the CapstoneProjectNDAIP. This should come directly from the database.
     * @param string $name the name associated with the CapstoneProjectNDAIP
     */
    public function __construct($id = null, $name = null) {
        if ($id == null && $name == null) {
            $this->setId(self::NO_AGREEMENT_REQUIRED);
            $this->setName('No Agreement Required');
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
