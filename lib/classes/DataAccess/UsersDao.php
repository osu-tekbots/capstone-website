<?php
namespace DataAccess;

use Model\User;
use Model\UserType;
use Model\UserSalutation;
use Model\UserAuthProvider;

/**
 * Contains logic for database interactions with user data in the database. 
 * 
 * DAO stands for 'Data Access Object'
 */
class UsersDao {

    /** @var DatabaseConnection */
    private $conn;

    /** @var \Util\Logger */
    private $logger;

    /**
     * Constructs a new instance of a User Data Access Object.
     *
     * @param DatabaseConnection $connection the connection used to perform user-related queries on the database
     * @param \Util\Logger $logger the logger to use for logging messages and errors associated with fetching user data
     */
    public function __construct($connection, $logger = null) {
        $this->logger = $logger;
        $this->conn = $connection;
    }

    /**
     * Fetches all the users from the database.
     * 
     * If an error occurs during the fetch, the function will return `false`.
     *
     * @return User[]|boolean an array of User objects if the fetch succeeds, false otherwise
     */
    public function getAllUsers() {
        try {
            $sql = 'SELECT * FROM user';
            $result = $this->conn->query($sql);
            if (!$result) {
                return false;
            }

            return \array_map(array('self::ExtractFromRow'), $result);
        } catch (\Exception $e) {
            $this->logError('Failed to fetch users: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Fetches a single user with the given ID from the database.
     *
     * @param string $id the ID of the user to fetch
     * @return User|boolean the corresponding User from the database if the fetch succeeds and the user exists, 
     * false otherwise
     */
    public function getUser($id) {
        try {
            $sql = 'SELECT * FROM user WHERE u_id = ?';
            $params = array($id);
            $result = $this->conn->query($sql, $params);
            if (!$result || \count($result) == 0) {
                return false;
            }

            return self::ExtractFromRow($result[0]);
        } catch (\Exception $e) {
            $this->logError('Failed to fetch single user by ID: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Fetches a single user with the AuthProvider's provided ID from the database.
     * 
     * The ID provided by the AuthProvider is determined when the user logs in for the first time using one
     * of the authentication methods.
     *
     * @param string $id the ID returned from the AuthProvider for the User to fetch
     * @return User|boolean the corresponding User from the database if the fetch succeeds and the user exists, 
     * false otherwise
     */
    public function getUserByAuthProviderProvidedId($id) {
        try {
            $sql = 'SELECT * FROM user WHERE u_uap_provided_id = ?';
            $params = array($id);
            $result = $this->conn->query($sql, $params);
            if (!$result || \count($result) == 0) {
                return false;
            }

            return self::ExtractFromRow($result[0]);
        } catch (\Exception $e) {
            $this->logError('Failed to fetch single user by ID: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Adds a new user to the database.
     *
     * @param \Model\User $user the user to add to the database
     * @return boolean true if the query execution succeeds, false otherwise.
     */
    public function addNewUser($user) {
        try {
            $sql = 'INSERT INTO user ';
            $sql .= '(u_id, u_ut_id, u_fname, u_lname, u_us_id, u_email, u_phone, u_major, u_affiliation, u_onid ';
            $sql .= 'u_uap_id, u_auth_provider_id, u_date_created) ';
            $sql .= 'VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
            $params = array(
                $user->getId(),
                $user->getType()->getId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getSalutation()->getId(),
                $user->getEmail(),
                $user->getPhone(),
                $user->getMajor(),
                $user->getAffiliation(),
                $user->getOnid(),
                $user->getAuthProvider()->getId(),
                $user->getAuthProviderId(),
                $user->getDateCreated()
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to add new user: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Updates an existing user in the database. 
     * 
     * This function only updates trivial user information, such as the type, first and last names, salutation, majors, 
     * affiliations, and contact information.
     *
     * @param \Model\User $user the user to update
     * @return boolean true if the query execution succeeds, false otherwise.
     */
    public function updateUser($user) {
        try {
            $sql = 'UPDATE user SET ';
            $sql .= 'u_ut_id = ?';
            $sql .= 'u_fname = ?, ';
            $sql .= 'u_lname = ?, ';
            $sql .= 'u_us_id = ?, ';
            $sql .= 'u_email = ?, ';
            $sql .= 'u_phone = ?, ';
            $sql .= 'u_major = ?, ';
            $sql .= 'u_affiliation = ?, ';
            $sql .= 'u_date_updated = ? ';
            $sql .= 'WHERE u_id = ?';
            $params = array(
                $user->getType()->getId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getSalutation()->getId(),
                $user->getEmail(),
                $user->getPhone(),
                $user->getMajor(),
                $user->getAffiliation(),
                new \DateTime(),
                $user->getId()
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to update user: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Creates a new User object by extracting the information from a row in the database.
     *
     * @param string[] $row a row from the database containing user information
     * @return \Model\User
     */
    public static function ExtractFromRow($row) {
        return (new User($row['u_id']))
            ->setType(new UserType(\intval($row['u_ut_id']), $row['ut_name']))
            ->setFirstName($row['u_fname'])
            ->setLastName(array('u_lname'))
            ->setSalutation(new UserSalutation(\intval($row['u_us_id']), $row['us_name']))
            ->setEmail($row['u_email'])
            ->setPhone($row['u_phone'])
            ->setMajor($row['u_major'])
            ->setAffiliation($row['u_affiliation'])
            ->setOnid($row['u_onid'])
            ->setAuthProvider(new UserAuthProvider(\intval($row['u_uap_id']), $row['uap_name']))
            ->setAuthProviderId($row['u_uap_provided_id'])
            ->setDateCreated(new \DateTime($row['u_date_created']))
            ->setDateUpdated(new \DateTime($row['u_date_updated']))
            ->setDateLastLogin(new \DateTime($row['u_date_last_login']));
    }

    /**
     * Logs an error if a logger was provided to the class when it was constructed.
     * 
     * Essentially a wrapper around the error logging so we don't cause the equivalent of a null pointer exception.
     *
     * @param string $message the message to log.
     * @return void
     */
    private function logError($message) {
        if ($this->logger != null) {
            $this->logger->error($message);
        }
    }
}
