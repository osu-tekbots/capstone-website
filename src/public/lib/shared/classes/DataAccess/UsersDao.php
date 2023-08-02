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
            $sql .= 'WHERE u_ut_id = ut_id AND u_us_id = us_id AND u_uap_id = uap_id ';
            $sql .= 'ORDER BY u_lname ASC';
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
     * Fetches a single user with the user's email.
     *
     * @param string $email the email of the user
     * @return User|boolean the corresponding User from the database if the fetch succeeds and the user exists, 
     * false otherwise
     */
    public function getUserByEmail($email) {
        try {
            $sql = 'SELECT * FROM user, user_type, user_salutation, user_auth_provider ';
            $sql .= 'WHERE u_email = :email AND u_ut_id = ut_id AND u_us_id = us_id AND u_uap_id = uap_id';
            $params = array(':email' => $email);
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
     * Fetches a single local user with the user's email.
     *
     * @param string $email the email of the user
     * @return User|boolean the corresponding User from the database if the fetch
     * succeeds and the user exists, false otherwise
     */
    public function getLocalUserWithEmail($email) {

        try {
            # Get user with provided credentials
            $sql = 'SELECT * FROM user, user_type, user_salutation, user_auth_provider 
				WHERE 
				u_email = :email AND 
				u_ut_id = ut_id AND 
				u_us_id = us_id AND 
				u_uap_id = uap_id AND 
				uap_name = :uap_name';
            $params = array(
                ':email' => $email,
                ':uap_name' => 'Local'
            );

            $result = $this->conn->query($sql, $params);
            if ($result) {
                return self::ExtractUserFromRow($result[0]);
            }

            return false;
        } catch (\Exception $e) {
            $this->logError('Failed to fetch single user by credentials: ' . $e->getMessage());

            return false;
        }
    }
    /**
     * Fetches a single local user with the user's email.
     *
     * @param string $email the email of the user
     * @return User|boolean the corresponding User from the database if the fetch succeeds and the user exists, 
     * false otherwise
     */
    public function getLocalUserWithCredentials($email, $password) {
        $user = $this->getLocalUserWithEmail($email);
        if (!$user) {
            return false;
        }
		try {
            $sql = 'SELECT * FROM user, user_type, user_salutation, user_auth_provider, user_local_auth 
				WHERE 
				u_email = :email AND 
				u_ut_id = ut_id AND 
				u_us_id = us_id AND 
				u_uap_id = uap_id AND 
				uap_name = :uap_name AND
				ula_id = u_id';
            $params = array(
                ':email' => $email,
                ':uap_name' => 'Local'
            );

            $result = $this->conn->query($sql, $params);
            if ($result) {
				if(password_verify($password, $result[0]['ula_pw'])) {
						return self::ExtractUserFromRow($result[0]);
					}
                return false;
            }
            return false;
        } catch (\Exception $e) {
            $this->logError('Failed to fetch single user by credentials: ' . $e->getMessage());

            return false;
        }
    }

    public function getRandomStringOfLength($n = 128) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        $len = strlen($chars) - 1;
        for ($i = 0; $i < $n; $i++) {
            $str .= $chars[rand(0, $len)];
        }
        return $str;
    }

    /**
     * Adds a new local password reset attempt.
     *
     * @param string $email the email of the user requesting a password reset
     * @return string|boolean reset_code value if success, False if the query execution fails
     */
    public function addNewLocalUserResetAttempt($email) {
        $user = $this->getLocalUserWithEmail($email);
        if (!$user) {
            return false;
        }

        $expire_minutes = 60;
        $code = $this->getRandomStringOfLength(24);

        $sql =  ' INSERT INTO user_local_auth_reset';
        $sql .= ' (ular_user, ular_code, ular_date_expires)';
        $sql .= ' VALUES';
        $sql .= " (:ular_user, :ular_code, NOW() + INTERVAL $expire_minutes MINUTE)";

        $params = array(
            ':ular_user' => $user->getId(),
            ':ular_code' => $code
        );

        try {
            $this->conn->execute($sql, $params);
        } catch (\Exception $e) {
            $this->logError('Failed to add new password reset attempt: ' . $e->getMessage());
            return false;
        }

        return $code;
    }

    /**
     * Checks if user has a valid password reset code.
     *
     * @param string $email the email of the user
     * @param string $code the password reset code
     * @return boolean True if valid, False if invalid
     */
    public function checkLocalUserResetAttempt($email, $code) {
        $user = $this->getLocalUserWithEmail($email);
        if (!$user) {
            return false;
        }

        try {
            $sql  = 'SELECT * FROM  user_local_auth_reset 
				WHERE 
				ular_user = :ular_user AND 
				ular_code = :ular_code AND 
				ular_date_expires > NOW()';

            $params = array(
                ':ular_user' => $user->getId(),
                ':ular_code' => $code
            );

            $result = $this->conn->query($sql, $params);
            if ($result) {
                return true;
            }
        } catch (\Exception $e) {
            $this->logError('Failed to fetch from table: ' . $e->getMessage());
            return false;
        }

        return false;
    }
	
	/**
     * Clears all reset codes for local user by email address.
     *
     * @param string $email the email of the user
     * @return boolean True if valid, False if invalid
     */
    public function deleteLocalUserResetAttempts($email) {
        $user = $this->getLocalUserWithEmail($email);
        if (!$user)
            return false;

		try {
			$sql  = ' DELETE FROM user_local_auth_reset WHERE ular_user = :ular_user';
			$params = array(
				':ular_user' => $user->getId()
			);

			$this->conn->execute($sql, $params);

			return true;
		} catch (\Exception $e) {
			$this->logError('Failed to delete reset code from database: ' . $e->getMessage());
			return false;
		}
        return false;
    }

    /**
     * Checks if user has a valid password reset code.
     *
     * @param string $email the email of the user
     * @param string $password the password to write tot he db
     * @return boolean True if success, False if fail
     */
    public function setLocalUserPassword($email, $password) {
        $user = $this->getLocalUserWithEmail($email);
        if (!$user)
            return false;

		$ula_pw = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = 'UPDATE user_local_auth SET ula_pw = :ula_pw WHERE ula_id = :ula_id';
            $params = array( ':ula_id' => $user->getId(), ':ula_pw' => $ula_pw );
            $this->conn->execute($sql, $params);
            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to update user password: ' . $e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * Adds a new local user to the database.
     *
     * @param \Model\User $user the user to add to the database
     * @return boolean true if the query execution succeeds, false otherwise.
     */
    public function setupLocalUserPassword($user) {
        try {
            $sql = 'INSERT INTO user_local_auth (ula_id, ula_pw) VALUES (:ula_id, null)';
            $params = array( ':ula_id' => $user->getId());
            $this->conn->execute($sql, $params);
            
        } catch (\Exception $e) {
            $this->logError('Failed to add new user: ' . $e->getMessage());
            return false;
        }

        return true;
    }



    private function getSalt() {
        try {
            $sql = 'SELECT * FROM user_local_auth_salt LIMIT 1';
            $params = array();
            $result = $this->conn->query($sql, $params);
            if (!$result || \count($result) == 0) {
                return false;
            }
            $salt = $result[0]['ulas_salt'];
            return $salt;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Fetches a single local user with the user's email.
     *
     * @param string $email the email of the user
     * @return User|boolean the corresponding User from the database if the fetch
     * succeeds and the user exists, false otherwise
     */
    public function getLocalUserWithEmail($email) {

        try {
            # Get user with provided credentials
            $sql = 'SELECT * FROM ';
            $sql .= ' user, user_type, user_salutation, user_auth_provider, user_local_auth ';
            $sql .= ' WHERE';
            $sql .= ' u_onid = :id';
            $sql .= ' AND u_ut_id = ut_id';
            $sql .= ' AND u_us_id = us_id';
            $sql .= ' AND u_uap_id = uap_id';
            $sql .= ' AND uap_name = :uap_name';

            $params = array(
                ':email' => $email,
                ':uap_name' => 'Local'
            );

            $result = $this->conn->query($sql, $params);
            if (!$result || \count($result) == 0) {
                return false;
            }

            return self::ExtractUserFromRow($result[0]);
        } catch (\Exception $e) {
            $this->logError('Failed to fetch single user by credentials: ' . $e->getMessage());

            return false;
        }
    }
    /**
     * Fetches a single local user with the user's email.
     *
     * @param string $email the email of the user
     * @return User|boolean the corresponding User from the database if the fetch succeeds and the user exists, 
     * false otherwise
     */
    public function getLocalUserWithCredentials($email, $password) {
        $salt = $this->getSalt();
        if (!$salt) {
            return false;
        }
        $ula_pw = hash('sha256', $password . $salt);

        try {
            # Get user with provided credentials
            $sql = 'SELECT * FROM ';
            $sql .= ' user, user_type, user_salutation, user_auth_provider, user_local_auth ';
            $sql .= ' WHERE';
            $sql .= ' u_onid = :id';
            $sql .= ' AND u_ut_id = ut_id';
            $sql .= ' AND u_us_id = us_id';
            $sql .= ' AND u_uap_id = uap_id';
            $sql .= ' AND uap_name = :uap_name';
            $sql .= ' AND ula_pw = :ula_pw';

            $params = array(
                ':email' => $email,
                ':uap_name' => 'Local',
                ':ula_pw' => $ula_pw
            );

            $result = $this->conn->query($sql, $params);
            if (!$result || \count($result) == 0) {
                return false;
            }

            return self::ExtractUserFromRow($result[0]);
        } catch (\Exception $e) {
            $this->logError('Failed to fetch single user by credentials: ' . $e->getMessage());

            return false;
        }
    }

    public function getRandomStringOfLength($n = 128) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        $len = strlen($chars) - 1;
        for ($i = 0; $i < $n; $i++) {
            $str .= $chars[rand(0, $len)];
        }
        return $str;
    }

    /**
     * Adds a new local password reset attempt.
     *
     * @param string $email the email of the user requesting a password reset
     * @return string|boolean reset_code value if success, False if the query execution fails
     */
    public function addNewLocalUserResetAttempt($email) {
        $user = $this->getLocalUserWithEmail($email);
        if (!$user) {
            return false;
        }

        $expire_minutes = 10;
        $code = $this->getRandomStringOfLength(128);

        $sql =  ' INSERT INTO user_local_auth_reset';
        $sql .= ' (ular_user, ular_code, ular_date_expires)';
        $sql .= ' VALUES';
        $sql .= " (:ular_user, :ular_code, NOW() + INTERVAL $expire_minutes MINUTE)";

        $params = array(
            ':ular_user' => $user->getId(),
            ':ular_code' => $code
        );

        try {
            $this->conn->execute($sql, $params);
        } catch (\Exception $e) {
            $this->logError('Failed to add new password reset attempt: ' . $e->getMessage());
            return false;
        }

        return $code;
    }

    /**
     * Checks if user has a valid password reset code.
     *
     * @param string $email the email of the user
     * @param string $code the password reset code
     * @return boolean True if valid, False if invalid
     */
    public function checkLocalUserResetAttempt($email, $code) {
        $user = $this->getLocalUserWithEmail($email);
        if (!$user) {
            return false;
        }

        try {
            $sql  = ' SELECT * FROM ';
            $sql .= ' user_local_auth_reset ';
            $sql .= ' WHERE';
            $sql .= ' ular_user = :ular_user';
            $sql .= ' AND ular_code = :ular_code';
            $sql .= ' AND ular_date_expires > NOW() - INTERVAL 1 MINUTE';

            $params = array(
                ':ular_user' => $$user->getId(),
                ':ular_code' => $$code
            );

            $result = $this->conn->query($sql, $params);
            if (!$result || \count($result) == 0) {
                return false;
            }

            try {
                $sql  = ' DELETE FROM user_local_auth_reset';
                $sql .= ' WHERE';
                $sql .= ' ular_user = :ular_user';
                $sql .= ' AND ular_code = :ular_code';
                $sql .= ' AND ular_date_expires > NOW() - INTERVAL 1 MINUTE';

                $params = array(
                    ':ular_user' => $$user->getId(),
                    ':ular_code' => $$code
                );

                $this->conn->execute($sql, $params);

                return true;
            } catch (\Exception $e) {
                $this->logError('Failed to delete reset code from database: ' . $e->getMessage());
                return false;
            }

        } catch (\Exception $e) {
            $this->logError('Failed to fetch single user by credentials: ' . $e->getMessage());
            return false;
        }

        return false;
    }

    /**
     * Checks if user has a valid password reset code.
     *
     * @param string $email the email of the user
     * @param string $password the password to write tot he db
     * @return boolean True if success, False if fail
     */
    public function setLocalUserPassword($email, $password) {
        $salt = $this->$getSalt();
        if (!$salt) {
            return false;
        }
        $ula_pw = hash('sha256', $password . $salt);

        try {
            $sql = 'UPDATE user_local_auth SET (ula_pw) VALUES (:ula_pw) WHERE ula_id = :ula_id';
            $params = array( ':ula_id' => $user->getId(), ':ula_pw' => $ula_pw );
            $this->conn->execute($sql, $params);
            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to update user password: ' . $e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * Adds a new local user to the database.
     *
     * @param \Model\User $user the user to add to the database
     * @return boolean true if the query execution succeeds, false otherwise.
     */
    public function addNewLocalUser($user, $password) {
        $salt = $this->$getSalt();
        if (!$salt) {
            return false;
        }
        $ula_pw = hash('sha256', $password . $salt);

        try {
            $sql = 'INSERT INTO user_local_auth (ula_id, ula_pw) VALUES (:ula_id, :ula_pw)';
            $params = array( ':ula_id' => $user->getId(), ':ula_pw' => $ula_pw );
            $this->conn->execute($sql, $params);
            
        } catch (\Exception $e) {
            $this->logError('Failed to add new user: ' . $e->getMessage());
            return false;
        }

        $success = $this->addNewuser($user);

        if (!$success) {
            try {
                $sql = 'DELETE FROM user_local_auth WHERE ula_id = :ula_id';
                $params = array(':ula_id' => $user->getId());
                $this->conn->execute($sql, $params);
                return false;
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
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
			$sql .= 'u_onid = :onid, ';
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
				':onid' => $user->getOnid(),
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
            ->setDateCreated(new \DateTime(($row['u_date_created'] == '' ? "now" : $row['u_date_created']))) //Edit made on 3/31/23 not tested
            ->setDateUpdated(new \DateTime(($row['u_date_updated'] == '' ? "now" : $row['u_date_updated'])))
            ->setDateLastLogin(new \DateTime(($row['u_date_last_login'] == '' ? "now" : $row['u_date_last_login'])));
        
        return $user;
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
