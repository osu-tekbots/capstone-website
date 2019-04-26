<?php
namespace DataAccess;

use Model\CapstoneApplication;
use Model\CapstoneApplicationStatus;

/**
 * Contains logic for database interactions with capstone project application data in the database. 
 * 
 * DAO stands for 'Data Access Object'
 */
class CapstoneApplicationsDao {

    /** @var DatabaseConnection */
    private $conn;

    /** @var \Util\Logger */
    private $logger;

    /**
     * Constructs a new instance of a Capstone Application Data Access Object.
     *
     * @param DatabaseConnection $connection the connection used to perform application related queries on the database
     * @param \Util\Logger $logger the logger to use for logging messages and errors associated with fetching data
     */
    public function __construct($connection, $logger = null) {
        $this->conn = $connection;
        $this->logger = $logger;
    }

    /**
     * Fetches all of the applications in the database.
     *
     * @return \Model\CapstoneApplication|boolean an array of the resulting applications, or false if the fetch fails
     */
    public function getAllApplications() {
        try {
            $sql = 'SELECT * FROM capstone_application, capstone_application_status ';
            $sql .= 'WHERE ca_cas_id = cas_id';
            $results = $this->conn->query($sql);
            if (!$results) {
                return false;
            }

            return \array_map('self::ExtractApplicationFromRow', $results);
        } catch (\Exception $e) {
            $this->logError('Failed to get all applications: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches all of the applications in the database associated with the project with the provided ID.
     *
     * @param string $projectId the ID of the project whose applications the DAO will fetch
     * @return \Model\CapstoneApplication|boolean an array of the resulting applications, or false if the fetch fails
     */
    public function getAllApplicationForProject($projectId) {
        try {
            $sql = 'SELECT * FROM capstone_application, capstone_application_status, user ';
            $sql .= 'WHERE ca_cp_id = :id AND ca_cas_id = cas_id AND ca_u_id = u_id';
            $params = array(':id' => $projectId);
            $results = $this->conn->query($sql, $params);
            if (!$results || \count($results) == 0) {
                return false;
            }

            return \array_map('self::ExtractApplicationFromRowWithProject', $results);
        } catch (\Exception $e) {
            $this->logError('Failed to get applications for project with id "' . $projectId . '": ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches all of the applications in the database associated with the user with the provided ID.
     *
     * @param string $projectId the ID of the user whose applications the DAO will fetch
     * @return \Model\CapstoneApplication|boolean an array of the resulting applications, or false if the fetch fails
     */
    public function getAllApplicationsForUser($userId) {
        try {
            $sql = 'SELECT * FROM capstone_application, capstone_application_status, user ';
            $sql .= 'WHERE ca_u_id = :id AND ca_cas_id = cas_id AND ca_u_id = u_id';
            $params = array(':id' => $userId);
            $results = $this->conn->query($sql, $params);
            if (!$results || \count($results) == 0) {
                return false;
            }

            return \array_map('self::ExtractApplicationFromRow', $results);
        } catch (\Exception $e) {
            $this->logError('Failed to get applications for user with id "' . $userId . '": ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches a single application with the provided ID from the database.
     *
     * @param string $id
     * @return \Model\CapstoneApplication|boolean the resulting application if it exists and the query succeeds, false
     * otherwise
     */
    public function getApplication($id) {
        try {
            $sql = 'SELECT * FROM capstone_application, capstone_application_status, user ';
            $sql .= 'WHERE ca_id = :id AND ca_cas_id = cas_id AND ca_u_id = u_id';
            $params = array(':id' => $id);
            $results = $this->conn->query($sql, $params);
            if (!$results || \count($results) == 0) {
                return false;
            }

            return self::ExtractApplicationFromRow($results[0]);
        } catch (\Exception $e) {
            $this->logError('Failed to get application with id "' . $id . '": ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Adds a new capstone application object to the database.
     *
     * @param \Model\CapstoneApplication $application the application to add
     * @return boolean true if the execution of the query succeeds, false otherwise
     */
    public function addNewApplication($application) {
        try {
            $sql = 'INSERT INTO capstone_application (ca_id, ca_cp_id, ca_u_id, ca_justification, ca_time_available, ';
            $sql .= 'ca_skill_set, ca_portfolio_link, ca_cas_id, ca_date_created, ca_date_updated, ca_date_submitted) ';
            $sql .= 'VALUES(:id, :projid, :userid, :just, :timeav, :skills, :port, :statid, :datec, :dateu, :dates)';
            $params = array(
                ':id' => $application->getId(),
                ':projid' => $application->getCapstoneProject()->getId(),
                ':userid' => $application->getStudent()->getId(),
                ':just' => $application->getJustification(),
                ':timeav' => $application->getTimeAvailable(),
                ':skills' => $application->getSkillSet(),
                ':port' => $application->getPortfolioLink(),
                ':statid' => $application->getStatus()->getId(),
                ':datec' => $application->getDateCreated(),
                ':dateu' => $application->getDateUpdated(),
                ':dates' => $application->getDateSubmitted()
            );
            $this->conn->execute($sql, $params);
            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to insert new application with id "' . $application->getId() . '": ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update selected fields for an existing capstone application in the database.
     * 
     * @param \Model\CapstoneApplication $application the capstone application to update
     * @return boolean true if the update succeeds, false otherwise
     */
    public function updateApplication($application) {
        try {
            $sql = 'UPDATE capstone_application SET ';
            $sql .= 'ca_justification = :just, ';
            $sql .= 'ca_time_available = :timeav, ';
            $sql .= 'ca_skill_set = :skills, ';
            $sql .= 'ca_portfolio_link = :port, ';
            $sql .= 'ca_cas_id = :statid, ';
            $sql .= 'ca_date_updated = :dateu, ';
            $sql .= 'ca_date_submitted = :dates ';
            $sql .= 'WHERE ca_id = :id';
            $params = array(
                ':id' => $application->getId(),
                ':just' => $application->getJustification(),
                ':timeav' => $application->getTimeAvailable(),
                ':skills' => $application->getSkillSet(),
                ':port' => $application->getPortfolioLink(),
                ':statid' => $application->getStatus()->getId(),
                ':dateu' => $application->getDateUpdated(),
                ':dates' => $application->getDateSubmitted()
            );
            $this->conn->execute($sql, $params);
            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to update application with id "' . $application->getId() . '": ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new capstone application object using data from a database row.
     * 
     * The row is assumed to also have project information that we also want to extract.
     * 
     * @param mixed[] $row the database row being used to create the capstone application
     * @return \Model\CapstoneApplication the extracted application
     */
    public static function ExtractApplicationFromRowWithProject($row) {
        return self::ExtractApplicationFromRow($row, true);
    }

    /**
     * Creates a new capstone application object using data from a database row.
     * 
     * @param mixed[] $row the database row being used to create the capstone application
     * @param boolean $includeProject flag to indicate whether to also extract the project from the row.
     * @return \Model\CapstoneApplication the extracted application
     */
    public static function ExtractApplicationFromRow($row, $includeProject = false) {
        // TODO: also set the capstone project
        $app = (new CapstoneApplication($row['ca_id']))
            ->setStudent(UsersDao::ExtractUserFromRow($row))
            ->setJustification($row['ca_justification'])
            ->setTimeAvailable($row['ca_time_available'])
            ->setSkillSet($row['ca_skill_set'])
            ->setPortfolioLink($row['ca_portfolio_link'])
            ->setStatus(self::ExtractApplicationStatusFromRow($row))
            ->setDateCreated(new \DateTime($row['ca_date_created']))
            ->setDateUpdated(new \DateTime($row['ca_date_updated']))
            ->setDateSubmitted(new \DateTime($row['ca_date_submitted']));
            if($includeProject) {
                $app->setCapstoneProject(CapstoneProjectsDao::ExtractCapstoneProjectFromRow($row));
            }
            return $app;
    }

    /**
     * Creates a new capstone application status object using data from a database row.
     * 
     * @param mixed[] $row the database row being used to create the capstone application status
     * @param boolean $applicationInRow indicating whether entries from the capstone application table are in the row
     * @return \Model\CapstoneApplication the extracted application status
     */
    public static function ExtractApplicationStatusFromRow($row, $applicationInRow = false) {
        $idKey = $applicationInRow ? 'ca_cas_id' : 'cas_id';
        return new CapstoneApplicationStatus($row[$idKey], $row['cas_name']);
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
