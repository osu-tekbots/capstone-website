<?php
namespace DataAccess;

/**
 * Connection to the MySQL database server through which all queries and transactions are executed.
 */
class DatabaseConnection {

    private $conn = null;

    /**
     * Creates a new instance of a database connection.
     *
     * @param string $host the host address of the database server
     * @param string $dbname the name of the database to connect to
     * @param string $username the name of the user to use to access the database
     * @param string $password the password associated with the user
     * @throws Exception if the attempt to connect to the database fails
     */
    public function __construct($host, $dbname, $username, $password) {
        $url = 'mysql:host=' . $host . ';dbname=' . $dbname;
        try {
            $this->conn = new \PDO($url, $username, $password);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception('Failed to connect to database: ' . $e->getMessage());
        }
    }

    /**
     * Closes the database connection.
     */
    public function __destruct() {
        $this->conn = null;
    }

    /**
     * Begin a new MySQL transaction.
     *
     * @throws Exception if there is no active database connection or an error occurs while starting the transaction
     * @return void
     */
    public function startTransaction() {
        if ($this->conn) {
            try {
                $this->conn->beginTransaction();
            } catch (\PDOException $e) {
                throw new Exception('Failed to start transaction: ' . $e->getMessage());
            }
        } else {
            throw new \Exception('Failed to start transaction: no database connection');
        }
    }

    /**
     * Abort the current MySQL transaction.
     *
     * @throws \Exception if there is no active database connection or an error occurs while attempting to rollback
     * @return void
     */
    public function rollback() {
        if ($this->conn) {
            try {
                $this->conn->rollBack();
            } catch (\PDOException $e) {
                throw new \Exception('Failed to rollback transaction: ' . $e->getMessage());
            }
        } else {
            throw new Exception('Failed to rollback transaction: no database connection');
        }
    }

    /**
     * Commit the current MySQL transaction.
     *
     * @throws Exception if there is no active database connection or an error occurs while attempting to commit
     * @return void
     */
    public function commit() {
        if ($this->conn) {
            try {
                $this->conn->commit();
            } catch (\PDOException $e) {
                throw new \Exception('Failed to commit transaction: ' . $e->getMessage());
            }
        } else {
            throw new \Exception('Failed to commit transaction: no database connection');
        }
    }

    /**
     * Fetch data from the database.
     * 
     * This function is specifically used to retrieve data from the database (i.e. SELECT queries). It will return
     * an associative array containing the results of the query. When variables or user-provided values need to
     * be part of the query, use a parameter placeholder by inserting a ':' prefixed symbol name in the SQL query and 
     * then pass the variables needed to populate the placeholders in as an associative array. 
     * The example below fetches all students who are older than 24 years old and are graduating in 2019:
     * 
     * ```php
     * $sql = 'SELECT * FROM students WHERE age > :age AND year_graduating = :year';
     * $params = array(':age' => 24, ':year' => 2019);
     * $result = $db->query($sql, $params);
     * ```
     *
     * @param string $sql the SQL query to execute
     * @param mixed[] $params an array of the paramters to safely insert into the SQL query
     * @throws Exception if there is no active database connection or an error occurs while fetching the data
     * @return mixed[]
     */
    public function query($sql, $params = array()) {
        if (!$this->conn) {
            throw new \Exception('Failed to execute query: no database connection established');
        }
        try {
            $prepared = $this->conn->prepare($sql);
            $this->bind($prepared, $params);
            $prepared->setFetchMode(\PDO::FETCH_ASSOC);
            $prepared->execute();

            return $prepared->fetchAll();
        } catch (\PDOException $e) {
            throw new \Exception('Failed to execute query: ' . $e->getMessage());
        }
    }

    /**
     * Execute a query that modifies data in the database.
     * 
     * This function is specifically used to modify data in the database (i.e. INSERT, UPDATE, and DELETE queries).
     * When variables or user-provided values need to be part of the query, use a parameter placeholder by inserting 
     * a ':' prefixed symbol name in the SQL query and then pass the variables needed to populate the placeholders in 
     * as an associative array. The example below sets the age of the user with ID 1234 to 55:
     * 
     * ```php
     * $sql = 'UPDATE users SET age = :age WHERE id = :id;
     * $params = array(':age' => 55, ':id' => '1234');
     * $result = $db->execute($sql, $params);
     * ```
     *
     * @param string $sql the SQL query to execute
     * @param mixed[] $params an array of the paramters to safely insert into the SQL query
     * @throws \Exception if there is no active database connection or an error occurs while executing the query
     * @return void
     */
    public function execute($sql, $params = array()) {
        if (!$this->conn) {
            throw new \Exception('Failed to execute statement: no connection to database established');
        }
        try {
            $prepared = $this->conn->prepare($sql);
            $this->bind($prepared, $params);
            $prepared->execute();
        } catch (\PDOException $e) {
            throw new \Exception('Failed to execute statement: ' . $e->getMessage());
        }
    }

    /**
     * Binds the provided paramters to the statement, performing necessary cleaning and SQL injection prevention.
     *
     * @param \PDOStatement $statement the prepared statement to bind the parameters to
     * @param mixed[] $params the parameters to bind to the statement
     * @throws \PDOException if the function fails to bind a parameter to the statement
     * @return void
     */
    private function bind($statement, $params) {
        $ok = true;
        foreach($params as $marker => $value) {
            $type = \gettype($value);
            switch($type) {
                case 'integer':
                    $ok = $statement->bindValue($marker, $value, \PDO::PARAM_INT);
                    break;
                case 'boolean':
                    $ok = $statement->bindValue($marker, $value, \PDO::PARAM_BOOL);
                    break;
                case 'NULL':
                    $ok = $statement->bindValue($marker, $value, \PDO::PARAM_NULL);
                    break;
                default:
                    $ok = $statement->bindValue($marker, $value, \PDO::PARAM_STR);
            }
            if(!$ok) {
                throw new \PDOException("Failed to bind parameter of type '$type' with value '$value' to '$marker'");
            }
        }
    }

    /**
     * Constructs a new instance of a database connection from the provided configuration array.
     * 
     * The provided array must have the following fields:
     * - `host` : the host IP address or URL for the database server
     * - `user` : the username the server will authenticate with
     * - `password` : the password used to authenticate the user
     * - `db_name` : the name of the database to connect with
     *
     * @param string[] $config the configuration values used to connect to and authenticate with the database server
     * @throws \Exception if the attempt to establish a database connection fails
     * @return DatabaseConnection
     */
    public static function FromConfig($config) {
        return new DatabaseConnection($config['host'], $config['db_name'], $config['user'], $config['password']);
    }
}
