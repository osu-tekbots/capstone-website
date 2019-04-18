<?php
namespace Model;

/**
 * Represents the status of an application for a capstone project.
 */
class CapstoneApplicationStatus {

    /** @var int */
    private $id;
    /** @var string */
    private $name;

    public function __construct($id, $name) {
        $this->setId($id);
        $this->setName($name);
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
