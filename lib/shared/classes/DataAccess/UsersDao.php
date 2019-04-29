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

    /** @var boolean */
    private $echoOnError;

    /**
     * Constructs a new instance of a User Data Access Object.
     *
     * @param DatabaseConnection $connection the connection used to perform user-related queries on the database
     * @param \Util\Logger $logger the logger to use for logging messages and errors associated with fetching user data
     * @param boolean $echoOnError determines whether to echo an error whether or not a logger is present
     */
    public function __construct($connection, $logger = null, $echoOnError = false) {
        $this->logger = $logger;
        $this->conn = $connection;
        $this->echoOnError = $echoOnError;
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
            $sql = 'SELECT * FROM user, user_type, user_salutation, user_auth_provider ';
            $sql .= 'WHERE u_ut_id = ut_id AND u_us_id = us_id AND u_uap_id = uap_id';
            $result = $this->conn->query($sql);

            return \array_map('self::ExtractUserFromRow', $result);
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
            $sql = 'SELECT * FROM user, user_type, user_salutation, user_auth_provider ';
            $sql .= 'WHERE u_id = :id AND u_ut_id = ut_id AND u_us_id = us_id AND u_uap_id = uap_id';
            $params = array(':id' => $id);
            $result = $this->conn->query($sql, $params);
            if (!$result || \count($result) == 0) {
                return false;
            }

            return self::ExtractUserFromRow($result[0]);
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
            $sql = '
            SELECT * 
            FROM user, user_type, user_salutation, user_auth_provider
            WHERE u_uap_provided_id = :id AND u_ut_id = ut_id AND u_us_id = us_id AND u_uap_id = uap_id
            ';
            $params = array(':id' => $id);
            $result = $this->conn->query($sql, $params);
            if (\count($result) == 0) {
                return false;
            }

            return self::ExtractUserFromRow($result[0]);
        } catch (\Exception $e) {
            $this->logError('Failed to fetch single user by ID: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Fetches a single user with the user's ONID.
     *
     * @param string $onid the ONID of the user, provided by OSU
     * @return User|boolean the corresponding User from the database if the fetch succeeds and the user exists, 
     * false otherwise
     */
    public function getUserByOnid($onid) {
        try {
            $sql = 'SELECT * FROM user, user_type, user_salutation, user_auth_provider ';
            $sql .= 'WHERE u_onid = :id AND u_ut_id = ut_id AND u_us_id = us_id AND u_uap_id = uap_id';
            $params = array(':id' => $onid);
            $result = $this->conn->query($sql, $params);
            if (!$result || \count($result) == 0) {
                return false;
            }

            return self::ExtractUserFromRow($result[0]);
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
            $sql .= '(u_id, u_ut_id, u_fname, u_lname, u_us_id, u_email, u_phone, u_major, u_affiliation, u_onid, ';
            $sql .= 'u_uap_id, u_uap_provided_id, u_date_created) ';
            $sql .= 'VALUES (:id,:type,:fname,:lname,:salu,:email,:phone,:maj,:affil,:onid,:auth,:authpid,:datec)';
            $params = array(
                ':id' => $user->getId(),
                ':type' => $user->getType()->getId(),
                ':fname' => $user->getFirstName(),
                ':lname' => $user->getLastName(),
                ':salu' => $user->getSalutation()->getId(),
                ':email' => $user->getEmail(),
                ':phone' => $user->getPhone(),
                ':maj' => $user->getMajor(),
                ':affil' => $user->getAffiliation(),
                ':onid' => $user->getOnid(),
                ':auth' => $user->getAuthProvider()->getId(),
                ':authpid' => $user->getAuthProviderId(),
                ':datec' => QueryUtils::FormatDate($user->getDateCreated())
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
            $sql .= 'u_ut_id = :type,';
            $sql .= 'u_fname = :fname, ';
            $sql .= 'u_lname = :lname, ';
            $sql .= 'u_us_id = :salu, ';
            $sql .= 'u_email = :email, ';
            $sql .= 'u_phone = :phone, ';
            $sql .= 'u_major = :maj, ';
            $sql .= 'u_affiliation = :affil, ';
            $sql .= 'u_date_updated = :dateu ';
            $sql .= 'WHERE u_id = :id';
            $params = array(
                ':type' => $user->getType()->getId(),
                ':fname' => $user->getFirstName(),
                ':lname' => $user->getLastName(),
                ':salu' => $user->getSalutation()->getId(),
                ':email' => $user->getEmail(),
                ':phone' => $user->getPhone(),
                ':maj' => $user->getMajor(),
                ':affil' => $user->getAffiliation(),
                ':dateu' => QueryUtils::FormatDate($user->getDateUpdated()),
                ':id' => $user->getId()
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to update user: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Fetch all available user type enumerations from the database.
     *
     * @return UserType[]|boolean the UserType objects from the database on success, false otherwise
     */
    public function getUserTypes() {
        try {
            $sql = 'SELECT * FROM user_type';
            $results = $this->conn->query($sql);
            return \array_map('self::ExtractUserTypeFromRow', $results);
        } catch (\Exception $e) {
            $this->logError('Failed to fetch user types: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all available user salutation enumerations from the database.
     *
     * @return UserSalutation[]|boolean the UserSalutation objects from the database on success, false otherwise
     */
    public function getUserSalutations() {
        try {
            $sql = 'SELECT * FROM user_salutation';
            $results = $this->conn->query($sql);
            return \array_map('self::ExtractUserSalutationFromRow', $results);
        } catch (\Exception $e) {
            $this->logError('Failed to fetch user salutations: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all available user auth provider enumerations from the database.
     *
     * @return UserAuthProvider[]|boolean the UserAuthProvider objects from the database on success, false otherwise
     */
    public function getUserAuthProviders() {
        try {
            $sql = 'SELECT * FROM user_auth_provider';
            $results = $this->conn->query($sql);
            return \array_map('self::ExtractUserAuthProviderFromRow', $results);
        } catch (\Exception $e) {
            $this->logError('Failed to fetch user auth providers: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new User object by extracting the information from a row in the database.
     *
     * @param string[] $row a row from the database containing user information
     * @return \Model\User
     */
    public static function ExtractUserFromRow($row) {
        $user = new User($row['u_id']);
        $user->setType(self::ExtractUserTypeFromRow($row, true))
            ->setFirstName($row['u_fname'])
            ->setLastName($row['u_lname'])
            ->setSalutation(self::ExtractUserSalutationFromRow($row, true))
            ->setEmail($row['u_email'])
            ->setPhone($row['u_phone'])
            ->setMajor($row['u_major'])
            ->setAffiliation($row['u_affiliation'])
            ->setOnid($row['u_onid'])
            ->setAuthProvider(self::ExtractUserAuthProviderFromRow($row, true))
            ->setAuthProviderId($row['u_uap_provided_id'])
            ->setDateCreated(new \DateTime($row['u_date_created']))
            ->setDateUpdated(new \DateTime($row['u_date_updated']))
            ->setDateLastLogin(new \DateTime($row['u_date_last_login']));
    }

    /**
     * Creates a new UserType object by extracting the necessary information from a row in a database.
     * 
     * The extraction will default to using the UserType ID from the user table if it is present so that this
     * function can be used on the user table alone without joining on the user type table.
     *
     * @param mixed[] $row the row from the database
     * @param boolean $userInRow flag indicating whether entries from the user table are in the row or not
     * @return \Model\UserType the user type extracted from the row
     */
    public static function ExtractUserTypeFromRow($row, $userInRow = false) {
        $idKey = $userInRow ? 'u_ut_id' : 'ut_id';
        $name = isset($row['ut_name']) ? $row['ut_name'] : null;
        return new UserType(\intval($row[$idKey]), $name);
    }

    /**
     * Creates a new UserSalutation object by extracting the necessary information from a row in a database.
     * 
     * The extraction will default to using the UserSalutation ID from the user table if it is present so that this
     * function can be used on the user table alone without joining on the user salutation table.
     *
     * @param mixed[] $row the row from the database
     * @param boolean $userInRow flag indicating whether entries from the user table are in the row or not
     * @return \Model\UserSalutation the user salutation extracted from the row
     */
    public static function ExtractUserSalutationFromRow($row, $userInRow = false) {
        $idKey = $userInRow ? 'u_us_id' : 'us_id';
        $name = isset($row['us_name']) ? $row['us_name'] : null;
        return new UserSalutation(\intval($row[$idKey]), $name);
    }

    /**
     * Creates a new UserAuthProvider object by extracting the necessary information from a row in a database.
     * 
     * The extraction will default to using the UserAuthProvider ID from the user table if it is present so that this
     * function can be used on the user table alone without joining on the user auth provider table.
     *
     * @param mixed[] $row the row from the database
     * @param boolean $userInRow flag indicating whether entries from the user table are in the row or not
     * @return \Model\UserAuthProvider the user auth provider extracted from the row
     */
    public static function ExtractUserAuthProviderFromRow($row, $userInRow = false) {
        $idKey = $userInRow ? 'u_uap_id' : 'uap_id';
        $name = isset($row['uap_name']) ? $row['uap_name'] : null;
        return new UserAuthProvider(\intval($row[$idKey]), $name);
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
        if ($this->echoOnError) {
            echo "$message\n";
        }
    }
}
