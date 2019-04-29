<?php
namespace Model;

use Util\IdGenerator;

/**
 * Data structure representing a user
 */
class User {

    /** @var string */
    private $id;

    /** @var UserType */
    private $type;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var UserSalutation */
    private $salutation;

    /** @var string */
    private $email;

    /** @var string */
    private $phone;

    /** @var string */
    private $major;

    /** @var string */
    private $affiliation;

    /** @var string */
    private $onid;

    /** @var UserAuthProvider */
    private $authProvider;

    /** @var string */
    private $authProviderId;

    /** @var \DateTime */
    private $dateCreated;

    /** @var \DateTime */
    private $dateUpdated;

    /** @var \DateTime */
    private $dateLastLogin;

    /**
     * Constructs a new instance of a user in the capstone system.
     * 
     * If no ID is provided, the alphanumeric ID will be generated using a random, cryptographically secure approach.
     *
     * @param string|null $id the ID of the user. If null, a new ID will be generated for the user.
     */
    public function __construct($id = null) {
        if ($id == null) {
            $id = IdGenerator::generateSecureUniqueId();
            $this->setId($id);
            $this->setType(new UserType());
            $this->setAuthProvider(new UserAuthProvider());
            $this->setSalutation(new UserSalutation());
            $this->setDateCreated(new \DateTime());
        } else {
            $this->setId($id);
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
     * Get the value of type
     */ 
    public function getType() {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of firstName
     */ 
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return  self
     */ 
    public function setFirstName($firstName) {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName
     */ 
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @return  self
     */ 
    public function setLastName($lastName) {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of salutation
     */ 
    public function getSalutation() {
        return $this->salutation;
    }

    /**
     * Set the value of salutation
     *
     * @return  self
     */ 
    public function setSalutation($salutation) {
        $this->salutation = $salutation;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of phone
     */ 
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */ 
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of major
     */ 
    public function getMajor() {
        return $this->major;
    }

    /**
     * Set the value of major
     *
     * @return  self
     */ 
    public function setMajor($major) {
        $this->major = $major;

        return $this;
    }

    /**
     * Get the value of affiliation
     */ 
    public function getAffiliation() {
        return $this->affiliation;
    }

    /**
     * Set the value of affiliation
     *
     * @return  self
     */ 
    public function setAffiliation($affiliation) {
        $this->affiliation = $affiliation;

        return $this;
    }

    /**
     * Get the value of onid
     */ 
    public function getOnid() {
        return $this->onid;
    }

    /**
     * Set the value of onid
     *
     * @return  self
     */ 
    public function setOnid($onid) {
        $this->onid = $onid;

        return $this;
    }

    /**
     * Get the value of authProvider
     */ 
    public function getAuthProvider() {
        return $this->authProvider;
    }

    /**
     * Set the value of authProvider
     *
     * @return  self
     */ 
    public function setAuthProvider($authProvider) {
        $this->authProvider = $authProvider;

        return $this;
    }

    /**
     * Get the value of authProviderId
     */ 
    public function getAuthProviderId() {
        return $this->authProviderId;
    }

    /**
     * Set the value of authProviderId
     *
     * @return  self
     */ 
    public function setAuthProviderId($authProviderId) {
        $this->authProviderId = $authProviderId;

        return $this;
    }

    /**
     * Get the value of dateCreated
     */ 
    public function getDateCreated() {
        return $this->dateCreated;
    }

    /**
     * Set the value of dateCreated
     *
     * @return  self
     */ 
    public function setDateCreated($dateCreated) {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get the value of dateUpdated
     */ 
    public function getDateUpdated() {
        return $this->dateUpdated;
    }

    /**
     * Set the value of dateUpdated
     *
     * @return  self
     */ 
    public function setDateUpdated($dateUpdated) {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * Get the value of dateLastLogin
     */ 
    public function getDateLastLogin() {
        return $this->dateLastLogin;
    }

    /**
     * Set the value of dateLastLogin
     *
     * @return  self
     */ 
    public function setDateLastLogin($dateLastLogin) {
        $this->dateLastLogin = $dateLastLogin;

        return $this;
    }
}
